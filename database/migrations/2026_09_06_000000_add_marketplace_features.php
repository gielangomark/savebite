<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable()->unique()->after('email');
            $table->string('timezone')->default('Asia/Jakarta')->after('role');
        });
        Schema::table('merchants', function (Blueprint $table) {
            $table->string('timezone')->default('Asia/Jakarta')->after('longitude');
            $table->string('bank_code')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_name')->nullable();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->time('pickup_start_time')->nullable()->after('pickup_time');
            $table->time('pickup_end_time')->nullable()->after('pickup_start_time');
            $table->boolean('is_active')->default(true)->after('stock');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_gateway')->nullable()->after('payment_method');
            $table->string('gateway_reference')->nullable()->index();
            $table->unsignedInteger('subtotal_amount')->default(0);
            $table->unsignedInteger('platform_fee')->default(0);
            $table->unsignedInteger('merchant_amount')->default(0);
            $table->timestamp('pickup_expires_at')->nullable()->index();
            $table->timestamp('pickup_warning_sent_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
        });
        Schema::create('login_otps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->string('purpose')->default('login');
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamps();
            $table->index(['user_id', 'purpose', 'expires_at']);
        });
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->morphs('owner');
            $table->unsignedBigInteger('balance')->default(0);
            $table->timestamps();
            $table->unique(['owner_type', 'owner_id']);
        });
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->unsignedBigInteger('amount');
            $table->unsignedBigInteger('balance_after');
            $table->string('reference')->unique();
            $table->string('description');
            $table->nullableMorphs('related');
            $table->timestamps();
        });
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('amount');
            $table->unsignedInteger('admin_fee')->default(5000);
            $table->unsignedBigInteger('net_amount');
            $table->string('reference')->unique();
            $table->string('status')->default('requested');
            $table->string('gateway_reference')->nullable();
            $table->string('failure_reason')->nullable();
            $table->timestamps();
        });
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('notifications'); Schema::dropIfExists('withdrawals'); Schema::dropIfExists('wallet_transactions'); Schema::dropIfExists('wallets'); Schema::dropIfExists('login_otps'); }
};
