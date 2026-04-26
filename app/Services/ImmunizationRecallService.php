<?php

namespace App\Services;

use App\Models\Child;
use App\Models\Immunization;
use Carbon\Carbon;

class ImmunizationRecallService
{
    /**
     * @return array<int, array{vaccine_id:int,vaccine_name:string,next_dose:string,due_date:Carbon,interval_days:int,immunization:Immunization}>
     */
    public function recallsForChild(Child $child): array
    {
        $immunizations = Immunization::query()
            ->with('vaccine')
            ->where('child_id', $child->id)
            ->orderBy('date')
            ->orderBy('immunized_at')
            ->get();

        if ($immunizations->isEmpty()) {
            return [];
        }

        $byVaccine = $immunizations->groupBy(fn (Immunization $immunization) => $this->normalizeVaccineName($immunization->vaccine?->name));
        $recalls = collect();

        foreach ($byVaccine as $vaccineName => $records) {
            $rule = $this->rules()[$vaccineName] ?? null;

            if (!$rule) {
                continue;
            }

            $latestRecord = $records->sortByDesc(fn (Immunization $item) => $item->immunized_at ?? $item->date)->first();
            $doseNumber = $records->count();
            $nextIndex = $doseNumber - 1;

            if (!isset($rule['next_dose_labels'][$nextIndex], $rule['intervals'][$nextIndex])) {
                continue;
            }

            $interval = $rule['intervals'][$nextIndex];
            $dueDate = Carbon::parse($latestRecord->date)->copy()->startOfDay();

            if ($interval['unit'] === 'month') {
                $dueDate->addMonthsNoOverflow($interval['value']);
            } else {
                $dueDate->addDays($interval['value']);
            }

            $recalls->push([
                'vaccine_id' => (int) $latestRecord->vaccine_id,
                'vaccine_name' => $rule['label'],
                'next_dose' => $rule['next_dose_labels'][$nextIndex],
                'due_date' => $dueDate,
                'interval_days' => $this->intervalDays($interval),
                'immunization' => $latestRecord,
            ]);
        }

        return $recalls
            ->sortBy('due_date')
            ->values()
            ->all();
    }

    /**
    * @return array<string, array{label:string, intervals: array<int, array{unit:string, value:int}>, next_dose_labels: array<int,string>}>
     */
    public function rules(): array
    {
        return [
            'hepatitis b' => [
                'label' => 'Hepatitis B',
                'intervals' => [
                    ['unit' => 'day', 'value' => 30],
                    ['unit' => 'day', 'value' => 30],
                    ['unit' => 'day', 'value' => 30],
                    ['unit' => 'month', 'value' => 14],
                ],
                'next_dose_labels' => ['Dose 2', 'Dose 3', 'Dose 4', 'Booster'],
            ],
            'dpt-hb-hib' => [
                'label' => 'DPT-HB-Hib',
                'intervals' => [
                    ['unit' => 'day', 'value' => 30],
                    ['unit' => 'day', 'value' => 30],
                    ['unit' => 'day', 'value' => 180],
                ],
                'next_dose_labels' => ['Dose 2', 'Dose 3', 'Booster'],
            ],
            'polio' => [
                'label' => 'Polio',
                'intervals' => [
                    ['unit' => 'day', 'value' => 60],
                    ['unit' => 'day', 'value' => 30],
                    ['unit' => 'day', 'value' => 30],
                    ['unit' => 'day', 'value' => 180],
                ],
                'next_dose_labels' => ['Dose 1', 'Dose 2', 'Dose 3', 'Booster'],
            ],
            'mr' => [
                'label' => 'MR (Campak Rubella)',
                'intervals' => [
                    ['unit' => 'day', 'value' => 180],
                ],
                'next_dose_labels' => ['Booster'],
            ],
            'bcg' => [
                'label' => 'BCG',
                'intervals' => [],
                'next_dose_labels' => [],
            ],
        ];
    }

    public function normalizeVaccineName(?string $name): string
    {
        return strtolower(trim((string) $name));
    }

    /**
     * @param array{unit:string, value:int} $interval
     */
    private function intervalDays(array $interval): int
    {
        return $interval['unit'] === 'month'
            ? $interval['value'] * 30
            : $interval['value'];
    }
}
