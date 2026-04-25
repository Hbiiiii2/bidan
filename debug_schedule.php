<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = App\Models\Service::with('midwives')->find(5);
echo 'service: ' . ($service ? $service->id : 'none') . "\n";
echo 'midwives: ' . ($service ? $service->midwives->count() : 0) . "\n";

if ($service) {
    $ids = $service->midwives->pluck('id')->toArray();
    $schedules = App\Models\Schedule::whereIn('midwife_id', $ids)
        ->whereDate('date', '>=', now())
        ->orderBy('date')
        ->orderBy('start_time')
        ->get();
    echo 'sched: ' . count($schedules) . "\n";
    foreach ($schedules as $s) {
        echo $s->id . ' ' . $s->midwife_id . ' ' . $s->date . ' ' . $s->start_time . ' ' . $s->quota . "\n";
    }
}
