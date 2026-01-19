<?php

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== VERIFYING PAYMENT UPDATE WORKFLOW ===\n";

// 1. Find Pending Payment
$payment = Payment::where('payment_status', 'pending')->first();
if (!$payment) {
    echo "No pending payments found. Please seed data.\n";
    exit;
}
echo "[Context] Payment ID: {$payment->id} (Amount: {$payment->amount})\n";

// 2. Simulate Request with File
$controller = new \App\Http\Controllers\PaymentController();

// Create dummy file for upload
$file = UploadedFile::fake()->image('proof.jpg');

$request = Request::create(route('payments.update', $payment->id), 'PUT', [], [], [
    'payment_proof' => $file
]);

// Mock Session for back()
$session = $app['session']->store();
// $request->setSession($session); // Might fail as before, try catch approach

try {
    echo "[Action] Submitting Payment Proof...\n";
    $controller->update($request, $payment->id);
} catch (\Throwable $e) {
    // Expect redirect
    echo "[Note] Controller finished: " . $e->getMessage() . "\n";
}

// 3. Verify Database
$updatedPayment = Payment::find($payment->id);
if ($updatedPayment->payment_status === 'paid' && $updatedPayment->payment_proof) {
    echo "[SUCCESS] Payment status is PAID.\n";
    echo "[SUCCESS] Proof saved at: " . $updatedPayment->payment_proof . "\n";
} else {
    echo "[FAILURE] Payment status: " . $updatedPayment->payment_status . "\n";
}
