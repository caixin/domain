<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetectLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = 'detect_log';

        Schema::create($tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('node_id')->default(0)->comment('節點ID');
            $table->integer('domain_id')->default(0)->comment('域名ID');
            $table->tinyInteger('status')->default(0)->comment('狀態 0:正常 1以上都是異常');
            $table->dateTime('created_at')->default('1970-01-01 00:00:00')->comment('建檔時間');
            $table->index(['node_id','domain_id']);
            $table->index('created_at');
        });

        DB::statement("ALTER TABLE `$tableName` comment '網域異常LOG'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detect_log');
    }
}
