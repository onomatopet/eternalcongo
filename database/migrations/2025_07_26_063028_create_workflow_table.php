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
        Schema::create('workflow_logs', function (Blueprint $table) {
            $table->id();
            $table->string('period', 7);
            $table->string('step', 50);
            $table->string('action', 50);
            $table->enum('status', ['started', 'completed', 'failed', 'skipped']);
            $table->unsignedBigInteger('user_id');
            $table->json('details')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('period');
            $table->index('step');
            $table->index('user_id');
            $table->index('status');
            $table->index(['period', 'step', 'status'], 'idx_workflow_logs_lookup');

            // Foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_logs');
    }
};
