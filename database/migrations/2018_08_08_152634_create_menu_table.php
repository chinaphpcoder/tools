<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->increments('id');
            $table->integer('pid')->default('0')->comment('父级ID');
            $table->string('title',255)->default('')->comment('标题');
            $table->integer('sort')->default('0')->comment('排序（同级有效）');
            $table->string('url',255)->default('')->comment('链接地址');
            $table->string('tip',255)->default('')->comment('提示');
            $table->tinyInteger('type')->default('0')->comment('菜单类型 0：通用 1：活动');
            $table->tinyInteger('status')->default('0')->comment('是否显示');
            $table->timestamp('created_at')->useCurrent()->comment('创建时间');
            $table->timestamp('update_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('更新时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menus');
    }
}
