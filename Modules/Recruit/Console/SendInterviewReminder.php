<?php

namespace Modules\Recruit\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Recruit\Entities\RecruitInterviewSchedule;
use Modules\Recruit\Events\CandidateInterviewScheduleEvent;
use Modules\Recruit\Events\InterviewScheduleEvent;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SendInterviewReminder extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-interview-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send interview reminder to the attendees before time specified in database';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $events = RecruitInterviewSchedule::with(['jobApplication', 'company:id,timezone'])
            ->where('schedule_date', '>', now()->format('Y-m-d H:i:s'))
            ->where('send_reminder_all', 1)
            ->get();

        foreach ($events as $interview) {
            $company = $interview->company;
            
            $reminderDateTime = $this->calculateReminderDateTime($interview, $company)->shiftTimezone('UTC')->setTimezone($company->timezone);

            if ($reminderDateTime->equalTo(now()->timezone($company->timezone)->startOfMinute())) {
                event(new InterviewScheduleEvent($interview, $interview->employees));
                event(new CandidateInterviewScheduleEvent($interview, $interview->jobApplication, $interview->candidateComment));
            }
        }
    }

    public function calculateReminderDateTime(RecruitInterviewSchedule $interview, $company)
    {
        $time = $interview->remind_time_all;
        $type = $interview->remind_type_all;

        $reminderDateTime = '';

        switch ($type) {
        case 'day':
            $reminderDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $interview->schedule_date, $company->timezone)->subDays($time);
            break;
        case 'hour':
            $reminderDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $interview->schedule_date, $company->timezone)->subHours($time);
            break;
        case 'minute':
            $reminderDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $interview->schedule_date, $company->timezone)->subMinutes($time);
            break;
        }

        return $reminderDateTime;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }

}
