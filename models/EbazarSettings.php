<?php


namespace iAmirNet\Ebazar\Models;

use Model;

class EbazarSettings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];
    public $settingsCode = 'iamirnet_shipping_settings';
    public $settingsFields = '$/iamirnet/ebazar/models/settings/fields_ebazar.yaml';
}