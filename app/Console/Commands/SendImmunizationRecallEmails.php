<?php

namespace App\Console\Commands;

use App\Mail\ImmunizationRecallMail;
use App\Models\Child;
use App\Models\ImmunizationRecallLog;
use App\Services\ImmunizationRecallService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendImmunizationRecallEmails extends Command
{
    protected $signature = 'app:send-immunization-recall-emails';

    protected $description = 'Send immunization recall emails to parents based on vaccine intervals';

    public function handle(ImmunizationRecallService $recallService): int
    {
        $children = Child::query()
            ->with(['user'])
            ->whereHas('immunizations')
            ->get();

        $sentCount = 0;

        foreach ($children as $child) {
            $recalls = $recallService->recallsForChild($child);

            if (empty($recalls)) {
                continue;
            }

            $parent = $child->user;

            if (!$parent || !$parent->email) {
                continue;
            }

            foreach ($recalls as $recall) {
                if ($recall['due_date']->gt(now()->startOfDay())) {
                    continue;
                }

                $alreadySent = ImmunizationRecallLog::query()
                    ->where('child_id', $child->id)
                    ->where('user_id', $parent->id)
                    ->where('vaccine_id', $recall['vaccine_id'])
                    ->where('next_dose', $recall['next_dose'])
                    ->whereDate('due_date', $recall['due_date']->toDateString())
                    ->exists();

                if ($alreadySent) {
                    continue;
                }

                Mail::to($parent->email)->send(new ImmunizationRecallMail(
                    child: $child,
                    vaccineName: $recall['vaccine_name'],
                    nextDose: $recall['next_dose'],
                    dueDate: $recall['due_date'],
                    intervalDays: $recall['interval_days'],
                    latestImmunization: $recall['immunization'],
                ));

                ImmunizationRecallLog::create([
                    'child_id' => $child->id,
                    'user_id' => $parent->id,
                    'vaccine_id' => $recall['vaccine_id'],
                    'immunization_id' => $recall['immunization']->id,
                    'next_dose' => $recall['next_dose'],
                    'due_date' => $recall['due_date']->toDateString(),
                    'sent_at' => now(),
                ]);

                $sentCount++;
                $this->info(sprintf(
                    'Sent recall email to %s for %s (%s)',
                    $parent->email,
                    $child->name,
                    $recall['vaccine_name']
                ));
            }
        }

        $this->info('Total recall emails sent: ' . $sentCount);

        return self::SUCCESS;
    }
}
