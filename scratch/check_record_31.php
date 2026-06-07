<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\MedicalRecord;

echo "\nScanning AI Logs for 'Bó thuốc nam':\n";
$logs = \App\Models\AiSuggestionLog::where('medical_record_id', 31)->get();
foreach ($logs as $log) {
    $responseStr = json_encode($log->response, JSON_UNESCAPED_UNICODE);
    if (str_contains($responseStr, 'Bó thuốc nam')) {
        echo "Found in Log ID: " . $log->id . "\n";
        echo "Response data: " . $responseStr . "\n\n";
    }
}
