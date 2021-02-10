<?php

namespace iAmirNet\Ebazar\Models\Methods;

use Azarinweb\Minimall\Models\Product;

trait Products
{
    public function setProducts($list)
    {
        foreach ($list as $product) {
            $this->procProduct(...$product);
        }
    }

    public function procProduct($product_id, $count, $percentDiscount = 0)
    {
        $product = $this->setProduct($product_id);
        if ($product)
            $this->products[] = [
                'EbazaarProductID' => $product, // Product id in ebazaar
                'Count' => (int) $count, // Request Number Of Product
                'DisCountPercent' => (int) $percentDiscount // No Discount
            ];
        return $product;
    }

    public function setProduct($product_id)
    {
        $new = true;
        $product = Product::find($product_id);
        if (!$product->weight)
            return false;
        $data['ClientProductID'] = $product->id;
        $data['Name'] = $product->name;
        $data['Price'] = $product->price()->integer / 10;
        $data['Weight'] = $product->weight ? : 0;
        $data['Count'] = $product->stock;
        $data['Enabled'] = true;
        $data['Visible'] = true;
        $data['IsStandard'] = true;
        $data['IsPocket'] = true;
        $data['Description'] = $product->description_short;
        $data['PercentDiscount'] = 0;
        if ($product->ebazar_post){
            $data['EbazaarProductID'] = $product->ebazar_post;
            $result = $this->_curl([$data], 'editProduct')->result;
            $new = false;
        }
        if ($new){
            $result = $this->_curl([$data], 'addProduct')->result;
            if ($result[0]->Succ && $product){
                $product->ebazar_post = $result[0]->EbazaarProductID;
                $product->save();
            }
        }
        return $product;
    }
}