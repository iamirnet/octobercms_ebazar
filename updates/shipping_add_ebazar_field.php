<?php namespace iAmirNet\Ebazar\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class ShippingAddEbazarField extends Migration
{
    public function up()
    {
        if (Schema::hasColumns('azarinweb_minimall_shipping_methods', ['ebazar_type','ebazar_free'])) {
            return;
        }

        Schema::table('azarinweb_minimall_shipping_methods', function($table)
        {
            $table->string('ebazar_type', 191)->nullable();
            $table->string('ebazar_free', 191)->nullable();
        });
    }

    public function down()
    {
        if (Schema::hasTable('azarinweb_minimall_shipping_methods')) {
            Schema::table('azarinweb_minimall_shipping_methods', function ($table) {
                $table->dropColumn(['ebazar_type']);
                $table->dropColumn(['ebazar_free']);
            });
        }
    }
}
