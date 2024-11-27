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
        Schema::table('clients', function (Blueprint $table) {
            $table->string('customer_id')->nullable()->after('id');
            $table->string('phone')->nullable()->after('mobile');
            $table->string('trading_name')->nullable()->after('name');
            $table->unsignedBigInteger('registration_country_id')->nullable()->after('country_id');
            $table->unsignedBigInteger('main_bank_id')->nullable()->after('registration_country_id');
            $table->unsignedBigInteger('second_bank_id')->nullable()->after('main_bank_id');
            $table->unsignedBigInteger('third_bank_id')->nullable()->after('second_bank_id');
            
            $table->foreign('registration_country_id')->references('id')->on('countries')->onDelete('set null');
            $table->foreign('main_bank_id')->references('id')->on('banks')->onDelete('set null');
            $table->foreign('second_bank_id')->references('id')->on('banks')->onDelete('set null');
            $table->foreign('third_bank_id')->references('id')->on('banks')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign(['registration_country_id']);
            $table->dropForeign(['main_bank_id']);
            $table->dropForeign(['second_bank_id']);
            $table->dropForeign(['third_bank_id']);
            
            $table->dropColumn([
                'customer_id',
                'phone',
                'trading_name',
                'registration_country_id',
                'main_bank_id',
                'second_bank_id',
                'third_bank_id'
            ]);
        });
    }
};
