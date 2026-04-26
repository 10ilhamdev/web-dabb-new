<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('role_columns', function (Blueprint $table) {
            $table->boolean('is_primary')->default(false)->after('is_unique');
            $table->boolean('is_foreign')->default(false)->after('is_primary');
            $table->string('references_table', 100)->nullable()->after('is_foreign');
            $table->string('references_column', 100)->nullable()->after('references_table');
            $table->string('on_delete', 30)->nullable()->after('references_column');
            $table->string('on_update', 30)->nullable()->after('on_delete');
            $table->boolean('is_unsigned')->default(false)->after('on_update');
            $table->boolean('is_auto_increment')->default(false)->after('is_unsigned');
        });
    }

    public function down(): void
    {
        Schema::table('role_columns', function (Blueprint $table) {
            $table->dropColumn([
                'is_primary',
                'is_foreign',
                'references_table',
                'references_column',
                'on_delete',
                'on_update',
                'is_unsigned',
                'is_auto_increment',
            ]);
        });
    }
};
