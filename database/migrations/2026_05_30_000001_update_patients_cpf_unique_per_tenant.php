<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if ($cpfUniqueIndex = $this->uniqueIndexName(['cpf'])) {
            Schema::table('patients', function (Blueprint $table) use ($cpfUniqueIndex) {
                $table->dropUnique($cpfUniqueIndex);
            });
        }

        if (!$this->uniqueIndexName(['cpf', 'tenant_id'])) {
            Schema::table('patients', function (Blueprint $table) {
                $table->unique(['cpf', 'tenant_id'], 'unique_cpf_per_tenant');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if ($cpfTenantUniqueIndex = $this->uniqueIndexName(['cpf', 'tenant_id'])) {
            Schema::table('patients', function (Blueprint $table) use ($cpfTenantUniqueIndex) {
                $table->dropUnique($cpfTenantUniqueIndex);
            });
        }

        if (!$this->uniqueIndexName(['cpf'])) {
            Schema::table('patients', function (Blueprint $table) {
                $table->unique('cpf');
            });
        }
    }

    private function uniqueIndexName(array $columns): ?string
    {
        foreach (Schema::getIndexes('patients') as $index) {
            if (($index['unique'] ?? false) && ($index['columns'] ?? []) === $columns) {
                return $index['name'];
            }
        }

        return null;
    }
};
