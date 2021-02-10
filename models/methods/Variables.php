<?php

namespace iAmirNet\Ebazar\Models\Methods;

use iAmirNet\Ebazar\Models\EbazarSettings;

trait Variables
{
    public $username = null;
    public $password = null;
    public $api_token = null;
    public $api_time = null;

    public $shipping = [];

    public $state = 1;
    public $city = 31;
    public $price = 500000;
    public $weight = 3500;
    public $serviceType = 0; // 0,1,3
    public $payType = 1; // 0,1,88

    public $prices = null;
    public $states = null;
    public $cities = null;
    public $bills = null;

    public $amount = null;
    public $amounts = [];

    public $products = [];

    public $errors = [
        "ebazar" => "خطای پست",
    ];

    public $domain = 'http://svc.ebazaar-post.ir/RestApi/';
    public $version = 'api/v0/';

    public $urls = [
        'setToken' => 'token',
        'getStats' => 'BaseInfo/Province',
        'getCities' => 'BaseInfo/City',
        'addProduct' => 'Product/Add',
        'editProduct' => 'Product/Edit',
        'getAmount' => 'Order/DeliveryPrice',
        'addParcel' => 'Order/AddParcel',
        'changeStatus' => 'Order/ChangeStatus',
    ];

    public $order = null;
    public $user = null;
}