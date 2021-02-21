<?php

namespace iAmirNet\Ebazar\Models\Methods;

use Azarinweb\Minimall\Models\Order as OrderModel;
use Carbon\Carbon;

trait Orders
{
    public function getAmounts($list)
    {
        foreach ($list as $product) {
            $this->amounts[] = $this->getAmount(...$product);
        }
        return $this->amounts;
    }

    public function getAmount($city = 31, $price = 500000, $weight = 3500, $serviceType = 0, $payType = 1)
    {
        if (is_array($city)) {
            $this->getState($city[0]);
            $this->getCity($city[1]);
        } else
            $this->city = $city;
        $this->price = $price;
        $this->weight = $weight;
        $this->serviceType = $serviceType;
        $this->payType = $payType;
        $result = $this->_curl([[
            "ClientOrderId" => time(),
            "CityCode" => (string) $this->city,
            "Price" => (int)($this->price / 10),
            "Weight" => (int)$this->weight,
            "ServiceType" => (int) $this->serviceType,
            "PayType" => (string) $this->payType
        ]], 'getAmount');
        $this->amount = $result->status ? $result->result[0] : 0;
        return $this->amount;
    }

    public function getShippingMethod()
    {
        switch ($this->shipping['method']) {
            case 'pishtaz':
                $this->serviceType = 1;
                break;
            case 'custom':
                $this->serviceType = 0;
                break;
            case 'pleasant':
                $this->serviceType = 3;
                break;
            default:
                $this->serviceType = 0;
        }
        return $this->serviceType;
    }

    public function getOrder($order)
    {
        return $this->order = is_numeric($order) ? OrderModel::findOrFail($order) : $order;
    }

    public function setOrderProducts()
    {
        foreach ($this->order->products()->get() as $product){
            $product = $this->setOrderProduct($product->product_id, $product->quantity);
            if ($product)
                $this->products[] = $product;
        }
        return $this->products;
    }

    public function setOrderProduct($product, $quantity)
    {
        $product = $this->setProduct($product);
        if ($product){
            $data['EbazaarProductID'] = (int) $product->ebazar_post;
            $data['Count'] = $quantity;
            $data['DisCountPercent'] = 0;
            return $data;
        }
        return $product;
    }

    public function addParcel($order)
    {
        $order = $this->getOrder($order);
        if (in_array($order->shipping['method']['ebazar_type'], ['pishtaz', 'custom', 'pleasant'])){
            $this->setOrderProducts();
            $this->getState($this->order->shipping_address['state']['name']);
            $this->getCity($this->order->shipping_address['city']);
            $this->shipping['method'] = $order->shipping['method']['ebazar_type'];
            $this->getShippingMethod();
            $data = [
                "ClientOrderId" =>  $this->order->id,
                "CityCode" => $this->city,
                "ServiceType" => $this->serviceType,
                "PayType" => $this->payType,
                "RegisterFirstName" => $this->order->customer->firstname,
                "RegisterLastName" => $this->order->customer->lastname,
                "RegisterAddress" => $this->order->shipping_address['lines'],
                "RegisterPhoneNumber" => $this->order->customer->user->phone ? : "0212222222",
                "RegisterMobile" => $this->order->customer->user->mobile ? : "0912222222222",
                "RegisterEmail" => $this->order->customer->user->email,
                "RegisterIp" => request()->ip(),
                "RegisterPostalCode" => $this->order->shipping_address['zip'],
                "Products" => $this->products
            ];
            $result = $this->_curl([$data], 'addParcel');
            if (isset($result->result) && isset($result->result[0]->ParcelCode)){
                $this->order->tracking_url = 'https://newtracking.post.ir';
                $this->order->tracking_number = $result->result[0]->ParcelCode;
                $this->order->save();
            }
            return true;
        }
        return false;
    }

    public static function _addParcel($order)
    {
        return (new self())->addParcel($order);
    }

    public function changeStatus($status = 2, $parcelCode)
    {
        $result = $this->_curl([
            'NewStatus' => $status,
            'ParcelCodes' => [$parcelCode],
        ], 'changeStatus')->result[0];
        return $result;
    }

    public function procShipping($totals, $method){
        $variables = [
            [
                $totals->getInput()->shipping_state_name,
                $totals->getInput()->shipping_city_name
            ],
            $totals->productPostTaxes(),
            $totals->weightTotal()
        ];
        if ($method->ebazar_type) $this->shipping['method'] = $method->ebazar_type;
        if (in_array($this->shipping['method'], ['pishtaz', 'custom', 'pleasant']) && $totals->weightTotal()){
            $this->getShippingMethod();
            $variables[] = $this->serviceType;
            if ($method->ebazar_free && $method->ebazar_free * 10 < $totals->productPostTaxes()){
                $price = 0;
                $this->shipping['free'] = true;
            } elseif (isset($this->shipping['price']) && $this->shipping['price'] && isset($this->shipping['hash']) && $this->shipping['hash'] == md5(serialize($variables))){
                $price = $this->shipping['price'];
                $this->shipping['free'] = false;
            }else{
                $amount = $this->getAmount(...$variables);
                $price = $amount ? ($amount->ShippingCost + $amount->ShippingTax) * 10 : 0;
                $this->shipping['price'] = $price;
                $this->shipping['hash'] = md5(serialize($variables));
                $this->shipping['free'] = false;
            }
        }else{
            $price = $method->price()->integer;
            $this->shipping = ['method' => 'none', 'price' => $price, 'free' => false];
        }
        \Session::put('shipping', $this->shipping);
        if($price>0)
        {
           $price = (($price)-($price%100000))+100000;
        }

        return $price;
    }

    public static function _procShipping($totals, $method){
        return (new self())->procShipping($totals, $method);
    }
}
