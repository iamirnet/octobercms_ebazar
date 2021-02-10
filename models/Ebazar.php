<?php

namespace iAmirNet\Ebazar\Models;

use iAmirNet\Ebazar\Models\Methods\Functions;
use iAmirNet\Ebazar\Models\Methods\Locations;
use iAmirNet\Ebazar\Models\Methods\Orders;
use iAmirNet\Ebazar\Models\Methods\Products;
use iAmirNet\Ebazar\Models\Methods\Variables;

class Ebazar
{
    use Variables, Functions, Locations, Products, Orders;
    public function __construct()
    {
        $this->username = (string) EbazarSettings::get('ebazar_username');
        $this->password = (string) EbazarSettings::get('ebazar_password');
        $this->api_token = (string) EbazarSettings::get('ebazar_api_token');
        $this->api_time = (string) EbazarSettings::get('ebazar_api_time');
        $this->checkToken();
        //$this->shipping = \Session::get('shipping') ? : [];
    }
}
