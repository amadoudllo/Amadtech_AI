<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add admin column to users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'moderator', 'user'])->default('user')->after('country_code');
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('role');
            }
            if (!Schema::hasColumn('users', 'is_blocked')) {
                $table->boolean('is_blocked')->default(false)->after('is_active');
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('is_blocked');
            }
        });

        // Admin Settings Table
        Schema::create('admin_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key')->unique();
            $table->longText('setting_value');
            $table->string('type')->default('string'); // string, integer, boolean, json
            $table->text('description')->nullable();
            $table->timestamps();
            $table->index('setting_key');
        });

        // Activity Log Table
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action');
            $table->string('target_type')->nullable();
            $table->unsignedBigInteger('target_id')->nullable();
            $table->text('description')->nullable();
            $table->json('changes')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index('user_id');
            $table->index('created_at');
        });

        // Request Stats Table
        Schema::create('request_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('model');
            $table->integer('prompt_tokens');
            $table->integer('completion_tokens');
            $table->float('temperature');
            $table->float('response_time_ms');
            $table->boolean('success')->default(true);
            $table->text('error_message')->nullable();
            $table->timestamp('created_at');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index('user_id');
            $table->index('created_at');
            $table->index('model');
        });

        // AI Config Table
        Schema::create('ai_configs', function (Blueprint $table) {
            $table->id();
            $table->string('model_name')->unique();
            $table->float('temperature')->default(0.7);
            $table->integer('max_tokens')->default(2000);
            $table->float('top_p')->default(1.0);
            $table->integer('requests_per_minute')->default(60);
            $table->integer('requests_per_day')->default(1000);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Abuse Reports Table
        Schema::create('abuse_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reported_by_user_id')->nullable();
            $table->unsignedBigInteger('reported_user_id')->nullable();
            $table->string('type'); // content, spam, harassment, etc.
            $table->text('content');
            $table->enum('status', ['pending', 'reviewed', 'confirmed', 'dismissed'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->foreign('reported_by_user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('reported_user_id')->references('id')->on('users')->onDelete('set null');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'is_active', 'is_blocked', 'last_login_at']);
        });
        
        Schema::dropIfExists('admin_settings');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('request_stats');
        Schema::dropIfExists('ai_configs');
        Schema::dropIfExists('abuse_reports');
    }
};
