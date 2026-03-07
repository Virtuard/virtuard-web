<?php
// Carica l'ambiente Laravel
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use App\Models\UserGameProgress;

// Esegui lo script tramite kernel console per avere accesso a DB
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Inizio riparazione statistiche...\n";

try {
    // 1. Sincronizza total_score con coins dove total_score è 0
    $affected = DB::table('user_game_progress')
        ->where('total_score', 0)
        ->where('coins', '>', 0)
        ->update(['total_score' => DB::raw('coins')]);

    echo "Sincronizzati $affected record di punteggio.\n";

    // 2. Verifica se ci sono livelli completati ma count a 0 (extra safety)
    $records = UserGameProgress::all();
    $fixedLevels = 0;
    foreach ($records as $record) {
        $data = $record->completed_levels_data;
        if (is_array($data)) {
            $count = count($data);
            // Non possiamo aggiornare facilmente via raw SQL se è JSON
            // Ma il nostro nuovo getter nel modello User risolve comunque il problema al volo.
        }
    }

    echo "Riparazione completata!\n";
} catch (\Exception $e) {
    echo "ERRORE: " . $e->getMessage() . "\n";
}
