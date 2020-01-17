<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomainTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = 'domain';

        Schema::create($tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id')->default(0)->comment('群組ID');
            $table->string('domain')->default('')->comment('域名');
            $table->date('deadline')->default('1970-01-01')->comment('網域到期日');
            $table->tinyInteger('ssl')->default(0)->comment('是否有SSL憑證');
            $table->string('supplier')->default('')->comment('购买地点');
            $table->string('remark')->default('')->comment('備註');
            $table->dateTime('created_at')->default('1970-01-01 00:00:00')->comment('建檔時間');
            $table->string('created_by', 50)->default('')->comment('新增者');
            $table->dateTime('updated_at')->default('1970-01-01 00:00:00')->comment('更新時間');
            $table->string('updated_by', 50)->default('')->comment('更新者');
            $table->index('group_id');
            $table->index('created_at');
            $table->unique('domain');
        });

        DB::statement("ALTER TABLE `$tableName` comment '網域列表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('domain');
    }
}
