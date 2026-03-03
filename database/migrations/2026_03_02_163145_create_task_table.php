<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->comment('タスク管理テーブル');

            $table->id('task_id');
            $table->uuid('task_uuid')->unique();
            $table->string('title')->comment('タイトル');
            $table->text('detail')->nullable()->comment('詳細');
            $table->date('due_date')->nullable()->comment('期限日');
            $table->string('status')->default('not_started')->comment('ステータス（not_started/in_progress/completed）');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
