<?php

namespace Modules\Recruit\Console;

use Illuminate\Console\Command;
use Modules\Recruit\Entities\RecruitCandidateFollowUp;
use Modules\Recruit\Events\CandidateFollowUpReminderEvent;

class CandidateFollowupReminderCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'candidate-followup-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification of followup to recruiter or added by user';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $followups = RecruitCandidateFollowUp::with('application', 'application.job')
            ->where('next_follow_up_date', '>=', now())
            ->where('send_reminder', 'yes')
            ->get();

        foreach ($followups as $followup) {

            $remindTime = $followup->remind_time;
            $reminderDate = null;

            if ($followup->remind_type == 'day') {
                $reminderDate = $followup->next_follow_up_date->subDays($remindTime);
            }
            elseif ($followup->remind_type == 'hour') {
                $reminderDate = $followup->next_follow_up_date->subHours($remindTime);
            }
            else {
                $reminderDate = $followup->next_follow_up_date->subMinutes($remindTime);
            }

            if ($reminderDate->format('Y-m-d H:i') == now()->timezone($followup->application->company->timezone)->format('Y-m-d H:i')) {
                event(new CandidateFollowUpReminderEvent($followup));
            }

        }

    }

}
