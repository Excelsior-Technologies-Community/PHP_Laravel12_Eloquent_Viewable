<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('views')) {
            Schema::table('views', function (Blueprint $table) {
                if (!Schema::hasColumn('views', 'device_type')) {
                    $table->string('device_type', 30)->default('Desktop')->after('collection');
                }
                if (!Schema::hasColumn('views', 'browser_name')) {
                    $table->string('browser_name', 50)->default('Chrome')->after('device_type');
                }
                if (!Schema::hasColumn('views', 'ip_address')) {
                    $table->string('ip_address', 45)->nullable()->after('browser_name');
                }
                if (!Schema::hasColumn('views', 'is_bot')) {
                    $table->boolean('is_bot')->default(false)->after('ip_address');
                }
            });
        }

        if (!Schema::hasTable('product_actions')) {
            Schema::create('product_actions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->string('action_type', 50); // add_to_cart, buy_now, favorite
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();

                $table->index('action_type');
                $table->index('created_at');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_actions');

        if (Schema::hasTable('views')) {
            Schema::table('views', function (Blueprint $table) {
                if (Schema::hasColumn('views', 'is_bot')) {
                    $table->dropColumn(['device_type', 'browser_name', 'ip_address', 'is_bot']);
                }
            });
        }
    }
};
