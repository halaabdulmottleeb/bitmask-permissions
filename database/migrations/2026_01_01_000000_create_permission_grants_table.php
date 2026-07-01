<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = $this->tableName();

        if (Schema::hasTable($table)) {
            return;
        }

        Schema::create($table, function (Blueprint $table): void {
            $table->id();
            $table->morphs('subject');
            $table->string('resource_type');
            $table->unsignedBigInteger('resource_id')->nullable();
            $table->unsignedBigInteger('mask')->default(0);
            $table->timestamps();

            $table->index(['resource_type', 'resource_id']);
            $table->unique(
                ['subject_type', 'subject_id', 'resource_type', 'resource_id'],
                'bitmask_subject_resource_unique',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName());
    }

    private function tableName(): string
    {
        return config()->string('bitmask-permissions.table', 'permission_grants');
    }
};
