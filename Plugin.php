<?php namespace iAmirNet\Ebazar;

use App;
use Auth;
use Azarinweb\Minimall\Controllers\Products as ProductsController;
use Azarinweb\Minimall\Controllers\ShippingMethods as ShippingMethodsController;
use Azarinweb\Minimall\Models\Product as ProductModel;
use Azarinweb\Minimall\Models\ShippingMethod as ShippingMethodModel;
use iAmirNet\Ebazar\Models\EbazarSettings;
use iAmirNet\Ebazar\Updates\ProductAddEbazarField;
use iAmirNet\Ebazar\Updates\ShippingAddEbazarField;
use Event;
use Yaml;
use File;
use Backend;
use System\Classes\PluginBase;

require_once "updates/product_add_ebazar_field.php";
require_once "updates/shipping_add_ebazar_field.php";

class Plugin extends PluginBase
{
    /**
     * @var boolean Determine if this plugin should have elevated privileges.
     */
    public $elevated = true;

    public function pluginDetails()
    {
        return [
            'name'        => 'iamirnet.ebazar::lang.plugin.name',
            'description' => 'iamirnet.ebazar::lang.plugin.description',
            'author'      => 'iamirnet',
            'icon'        => 'icon-user',
            'homepage'    => 'https://iAmir.Net'
        ];
    }

    public function boot()
    {
        $this->extendModels();
        $this->extendControllers();
        (new ProductAddEbazarField())->up();
        (new ShippingAddEbazarField())->up();
    }

    public function register()
    {
        parent::register(); // TODO: Change the autogenerated stub
    }

    public function registerPermissions()
    {
        return [
            'iamirnet.ebazar.access_ebazar' => [
                'tab'   => 'iamirnet.ebazar::lang.plugin.tab',
                'label' => 'iamirnet.ebazar::lang.plugin.access_ebazar'
            ],
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'iamirnet.ebazar::lang.ebazar.title',
                'description' => 'iamirnet.ebazar::lang.ebazar.description',
                'category'    => 'iamirnet.minimall::lang.settings.shipping.title',
                'icon'        => 'icon-cog',
                'class'       => EbazarSettings::class,
                'order'       => 800,
                'permissions' => ['iamirnet.ebazar.access_ebazar']
            ]
        ];
    }
    protected function extendModels()
    {
        ProductModel::extend(function($model) {
            $model->addFillable([
                'ebazar_post',
            ]);
        });
        ShippingMethodModel::extend(function($model) {
            $model->addFillable([
                'ebazar_type',
                'ebazar_free',
            ]);
        });
    }

    protected function extendControllers()
    {
        ProductsController::extendFormFields(function($widget) {
            if (!$widget->model instanceof ProductModel) return;
            $configFile = plugins_path('iamirnet/ebazar/config/product_extra_fields.yaml');
            $config = Yaml::parse(File::get($configFile));
            $widget->addTabFields($config);
        });
        ShippingMethodsController::extendFormFields(function($widget) {
            if (!$widget->model instanceof ShippingMethodModel) return;
            $configFile = plugins_path('iamirnet/ebazar/config/shipping_extra_fields.yaml');
            $config = Yaml::parse(File::get($configFile));
            $widget->addTabFields($config);
        });
    }


}
