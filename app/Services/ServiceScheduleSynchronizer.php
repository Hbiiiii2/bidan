<?php

namespace App\Services;

use App\Models\Schedule;
use App\Models\Service;
use Carbon\Carbon;

class ServiceScheduleSynchronizer
{
    public const DEFAULT_MIDWIFE_DAILY_QUOTA = 10;

    public function sync(Service $service): void
    {
        $service->loadMissing(['midwives' => function ($query) {
            $query->select('users.id', 'users.available_days', 'users.available_start_time', 'users.available_end_time');
        }]);

        if ($service->midwives->isEmpty()) {
            return;
        }

        $fromDate = $service->available_from_date ?? $service->available_date;
        $untilDate = $service->available_until_date ?? $service->available_date ?? $fromDate;

        if (!$fromDate || !$untilDate) {
            return;
        }

        $startDate = Carbon::parse($fromDate)->startOfDay();
        $endDate = Carbon::parse($untilDate)->startOfDay();
        $today = now()->startOfDay();

        if ($endDate->lt($today)) {
            return;
        }

        if ($startDate->lt($today)) {
            $startDate = $today;
        }

        foreach ($service->midwives as $midwife) {
            $timeRange = $this->resolveTimeRange(
                $service->available_start_time,
                $service->available_end_time,
                $midwife->available_start_time,
                $midwife->available_end_time,
            );

            if (!$timeRange) {
                continue;
            }

            $dailyQuota = $this->resolveMidwifeDailyQuota($service, (int) $midwife->id);
            $availableDays = $this->normalizeAvailableDays($midwife->available_days);
            $cursor = $startDate->copy();

            while ($cursor->lte($endDate)) {
                $weekday = strtolower($cursor->englishDayOfWeek);
                $isAvailableDay = empty($availableDays) || in_array($weekday, $availableDays, true);

                if ($isAvailableDay) {
                    $this->upsertSchedule(
                        midwifeId: (int) $midwife->id,
                        date: $cursor->toDateString(),
                        startTime: $timeRange['start_time'],
                        endTime: $timeRange['end_time'],
                        dailyQuota: $dailyQuota,
                    );
                }

                $cursor->addDay();
            }
        }
    }

    public function resolveMidwifeDailyQuota(Service $service, int $midwifeId): int
    {
        $service->loadMissing('midwives');

        $midwife = $service->midwives->firstWhere('id', $midwifeId);
        $value = (int) ($midwife?->pivot?->max_daily_quota ?? self::DEFAULT_MIDWIFE_DAILY_QUOTA);

        return max(1, $value);
    }

    private function upsertSchedule(int $midwifeId, string $date, string $startTime, string $endTime, int $dailyQuota): void
    {
        $schedule = Schedule::query()
            ->where('midwife_id', $midwifeId)
            ->whereDate('date', $date)
            ->first();

        if (!$schedule) {
            Schedule::create([
                'midwife_id' => $midwifeId,
                'date' => $date,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'quota' => $dailyQuota,
            ]);

            return;
        }

        $activeBookingCount = $schedule->bookings()
            ->where('status', '!=', 'canceled')
            ->count();

        $schedule->quota = max($schedule->quota, $dailyQuota, $activeBookingCount);

        if ($activeBookingCount === 0) {
            $schedule->start_time = $startTime;
            $schedule->end_time = $endTime;
        }

        $schedule->save();
    }

    private function resolveTimeRange(?string $serviceStart, ?string $serviceEnd, ?string $midwifeStart, ?string $midwifeEnd): ?array
    {
        $start = max(
            $this->normalizeTime($serviceStart, '08:00:00'),
            $this->normalizeTime($midwifeStart, '00:00:00')
        );

        $end = min(
            $this->normalizeTime($serviceEnd, '16:00:00'),
            $this->normalizeTime($midwifeEnd, '23:59:59')
        );

        if ($start >= $end) {
            return null;
        }

        return [
            'start_time' => $start,
            'end_time' => $end,
        ];
    }

    private function normalizeTime(?string $time, string $fallback): string
    {
        if (!$time) {
            return $fallback;
        }

        $trimmed = trim($time);

        if (strlen($trimmed) === 5) {
            return $trimmed . ':00';
        }

        return substr($trimmed, 0, 8);
    }

    private function normalizeAvailableDays(mixed $availableDays): array
    {
        if (!is_array($availableDays)) {
            return [];
        }

        return collect($availableDays)
            ->map(fn ($day) => strtolower((string) $day))
            ->filter()
            ->values()
            ->all();
    }
}
