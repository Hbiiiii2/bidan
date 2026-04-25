<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = App\Models\Service::with('midwives')->find(5);
if (!$service) {
    echo 'service not found\n';
    exit(1);
}

$ids = $service->midwives->pluck('id')->toArray();
$dates = App\Models\Schedule::whereIn('midwife_id', $ids)->orderBy('date')->orderBy('start_time')->get();
echo 'service id: ' . $service->id . "\n";
echo 'midwives: ' . $service->midwives->pluck('id')->join(',') . "\n";
foreach ($dates as $s) {
    echo implode(' | ', [
        $s->id,
        $s->midwife_id,
        $s->date,
        $s->start_time,
        $s->end_time,
        $s->quota,
    ]) . "\n";
}
