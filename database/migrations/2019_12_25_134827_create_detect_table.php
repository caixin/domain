<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = 'detect';

        Schema::create($tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('node_id')->default(0)->comment('節點ID');
            $table->integer('domain_id')->default(0)->comment('域名ID');
            $table->dateTime('lock_time')->default('1970-01-01 00:00:00')->comment('鎖定時間');
            $table->tinyInteger('status')->default(0)->comment('狀態 0:正常 1:以上都是異常');
            $table->dateTime('created_at')->default('1970-01-01 00:00:00')->comment('建檔時間');
            $table->string('created_by', 50)->default('')->comment('新增者');
            $table->dateTime('updated_at')->default('1970-01-01 00:00:00')->comment('更新時間');
            $table->string('updated_by', 50)->default('')->comment('更新者');
            $table->unique(['node_id','domain_id']);
            $table->index('created_at');
            $table->index('domain_id');
        });

        DB::statement("ALTER TABLE `$tableName` COMMENT '節點網域檢測'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detect');
    }
}
