<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('consultations')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE consultations MODIFY objectives TEXT NULL');
            DB::statement('ALTER TABLE consultations MODIFY content_worked TEXT NULL');
            DB::statement('ALTER TABLE consultations MODIFY clinical_observations TEXT NULL');
            DB::statement('ALTER TABLE consultations MODIFY interventions TEXT NULL');
            DB::statement('ALTER TABLE consultations MODIFY planning TEXT NULL');
            DB::statement('ALTER TABLE consultations MODIFY homework TEXT NULL');
            DB::statement('ALTER TABLE consultations MODIFY emotional_state TEXT NULL');
            DB::statement('ALTER TABLE consultations MODIFY engagement_level TEXT NULL');
            DB::statement('ALTER TABLE consultations MODIFY insights TEXT NULL');
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('consultations') || DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE consultations MODIFY emotional_state ENUM('joy','sadness','anger','fear','disgust','surprise') NULL");
        DB::statement("ALTER TABLE consultations MODIFY engagement_level ENUM('high','medium','low') NULL");
    }
};
