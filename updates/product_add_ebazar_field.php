<?php namespace iAmirNet\Ebazar\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class ProductAddEbazarField extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('azarinweb_minimall_products', 'ebazar_post')) {
            return;
        }

        Schema::table('azarinweb_minimall_products', function($table)
        {
            $table->string('ebazar_post', 191)->nullable();
        });
    }

    public function down()
    {
        if (Schema::hasTable('azarinweb_minimall_products')) {
            Schema::table('azarinweb_minimall_products', function ($table) {
                $table->dropColumn(['ebazar_post']);
            });
        }
    }
}
