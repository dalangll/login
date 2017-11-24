<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('category_id')->comment('分类id');
            $table->string('goods_name')->comment('商品标题');
            $table->decimal('money',10,2)->comment('商品价格');
            $table->decimal('original_money',10,2)->comment('商品原价');
            $table->string('thumb');
            $table->text('banner');
            $table->string('description')->nullable()->comment('商品描述');
            $table->boolean('status')->default(true)->comment('商品的状态，默认为1表示上架，0表示下架');
            $table->integer('sale')->default(0)->comment('商品的销量');
            $table->boolean('stock')->default(true)->comment('商品的库存，默认为true表示有货，false表示缺货');
            $table->mediumInteger('order');
            $table->timestamps();
            $table->index('sale');
            $table->index('money');
            $table->index('order');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods');
    }
}
