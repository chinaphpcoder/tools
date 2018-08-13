<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillDetailLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_detail_log', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->increments('id');
            $table->integer('business_identity_id')->default('0')->comment('业务标识id');
            $table->string('request_no',255)->default('')->comment('平台请求流水号');
            $table->decimal('base_amount',20,2)->default('0')->comment('基准金额');
            $table->decimal('account_amount',20,2)->default('0')->comment('记账金额');
            $table->tinyInteger('status')->default('0')->comment('状态 1：平账 2：短款 3：长款');
            $table->timestamp('created_at')->useCurrent()->comment('创建时间');
            $table->timestamp('update_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('更新时间');
            $table->unique(['business_identity_id','request_no']);
            $table->index(['base_amount']);
            $table->index(['account_amount']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bill_detail_log');
    }
}
