<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_order', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('member_id')->comment('用户id');
            $table->string('goods_id')->comment('商品id');
            $table->char('code',13)->comment('订单号');
            $table->decimal('money',10,2)->comment('金额');
            $table->string('pay_type')->comment('充值渠道');
            $table->boolean('pay_status')->default(false)->comment('是否支付成功');
            $table->timestamp('pay_time')->nullable()->comment('支付时间');
            $table->timestamp('success_time')->nullable()->comment('支付成功时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_order');
    }
}
