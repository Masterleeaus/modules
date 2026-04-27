<?php

namespace Modules\Recruit\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Recruit\Entities\RecruitJobOfferLetter;
use Modules\Recruit\Entities\RecruitSetting;
use Modules\Recruit\Events\SendOfferLetterReminderEvent;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class OfferLetterReminder extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offer-letter-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder to candidate for action on offer letter.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $events = RecruitJobOfferLetter::with('jobApplication', 'company')
            ->where('status', 'pending')
            ->get();

        foreach ($events as $event) {

            $company = $event->company;

            $settings = RecruitSetting::where('company_id', $company->id)->first();
            $days = $settings->offer_letter_reminder;

            if($days){
                $remindDate = Carbon::createFromFormat('Y-m-d', $event->job_expire, $company->timezone)->subDays($days)->format('Y-m-d');
                $now = now()->timezone($company->timezone)->format('Y-m-d');

                if ($remindDate == $now) {
                    event(new SendOfferLetterReminderEvent($event));
                }
            }

        }
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
    protected
    function getOptions()
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }

}
