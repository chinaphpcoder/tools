<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_record', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->increments('id');
            $table->integer('user_id')->default('0')->comment('用户id');
            $table->string('business_identity',200)->default('')->comment('业务标识');
            $table->string('business_alias',200)->default('')->comment('别名');
            $table->tinyInteger('status')->default('0')->comment('状态 0：未上传文件 1：已上传基准文件 2：已上传实际数据 3：平账 4：不平账');
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
        Schema::dropIfExists('bill_record');
    }
}
