<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Permite marcar o agendamento como confirmado assim que paciente e pagamento forem concluídos.
        DB::statement("ALTER TABLE client_appointments MODIFY status ENUM('Open', 'Confirmed', 'Done', 'Canceled') NOT NULL DEFAULT 'Open'");
    }

    public function down(): void
    {
        // Mantem os registros existentes migraveis antes de remover o valor Confirmed do enum.
        DB::statement("UPDATE client_appointments SET status = 'Open' WHERE status = 'Confirmed'");
        DB::statement("ALTER TABLE client_appointments MODIFY status ENUM('Open', 'Done', 'Canceled') NOT NULL DEFAULT 'Open'");
    }
};
