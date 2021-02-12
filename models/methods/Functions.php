<?php

namespace iAmirNet\Ebazar\Models\Methods;

use iAmirNet\Ebazar\Models\EbazarSettings;

trait Functions
{
    public function checkToken()
    {
        $new = false;
        if (!$this->api_token) $new = true;
        if (!$this->api_time || $this->api_time + 3300 < time()) $new = true;
        if ($new) {
            $result = $this->__curl(http_build_query([
                "username" => $this->username,
                "password" => $this->password,
                "grant_type" => 'password',
            ]),
                $this->domain . $this->urls['setToken'],
                [
                    'Content-Type: application/x-www-form-urlencoded',
                ]);
            $this->api_token = isset($result->access_token) ? $result->access_token : '';

            if (isset($result->access_token))$this->api_time = time();
            EbazarSettings::set('ebazar_api_token', $this->api_token);
            if (isset($result->access_token))EbazarSettings::set('ebazar_api_time', $this->api_time);
        }
    }

    public function _curl($data, $url)
    {

        $headers = array(
            'cache-control' => 'no-cache',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_token,);
        $result = $this->__curl(is_array($data) ? json_encode($data) : $data, $this->domain . $this->version . $this->urls[$url], $headers);
        if (isset($result->ResCode) && $result->ResCode == 0) {
            return (object)['status' => true, 'result' => $result->Data];
        } else {
            return (object)['status' => false,
                'code' => isset($result->ResCode) ? $result->ResCode : -1,
                'message' => isset($result->ResMsg) ? $result->ResMsg : 'خطا ای بازار پست'
            ];
        }
    }

    public function __curl($data, $url, $headers = null)
    {
        /*if ($url == 'http://svc.ebazaar-post.ir/RestApi/api/v0/Order/DeliveryPrice')
            dd($data);*/
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = json_decode(curl_exec($ch));
        return $result;
    }
}
