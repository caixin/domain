<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJumpLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = 'jump_log';

        Schema::create($tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->string('url')->default('')->comment('當前網址');
            $table->integer('domain_id')->default(0)->comment('網域ID');
            $table->integer('status')->default(0)->comment('狀態');
            $table->string('ip', 50)->default('')->comment('IP');
            $table->json('ip_info')->comment('IP資訊');
            $table->dateTime('created_at')->default('1970-01-01 00:00:00')->comment('建檔時間');
            $table->index('created_at');
        });

        DB::statement("ALTER TABLE `$tableName` comment 'API日誌'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jump_log');
    }
}
