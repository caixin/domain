<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomainGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = 'domain_group';

        Schema::create($tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->default('')->comment('群組名稱');
            $table->string('path')->default('')->comment('路徑');
            $table->integer('target_id')->default(0)->comment('目標群組');
            $table->integer('mode')->default(0)->comment('檢查模式 1:檢查碼 2:css字串 4:HTML字串1 8:HTML字串2');
            $table->string('value1', 50)->default('')->comment('檢查碼');
            $table->string('value2', 500)->default('')->comment('CSS字串');
            $table->string('value3', 500)->default('')->comment('HTML字串1');
            $table->string('value4', 500)->default('')->comment('HTML字串2');
            $table->tinyInteger('status')->default(0)->comment('狀態 0:關閉 1:啟用');
            $table->dateTime('created_at')->default('1970-01-01 00:00:00')->comment('建檔時間');
            $table->string('created_by', 50)->default('')->comment('新增者');
            $table->dateTime('updated_at')->default('1970-01-01 00:00:00')->comment('更新時間');
            $table->string('updated_by', 50)->default('')->comment('更新者');
            $table->index('created_at');
        });

        DB::statement("ALTER TABLE `$tableName` comment '網域群組'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('domain_group');
    }
}
