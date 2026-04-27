<?php

namespace Modules\Recruit\Jobs;

use App\Traits\ExcelImportable;
use App\Traits\UniversalSearchTrait;
use Carbon\Exceptions\InvalidFormatException;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Modules\Recruit\Entities\ApplicationSource;
use Modules\Recruit\Entities\RecruitApplicationStatus;
use Modules\Recruit\Entities\RecruitJob;
use Modules\Recruit\Entities\RecruitJobApplication;
use PhpParser\Node\Stmt\Catch_;

class ImportJobApplicationJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels, UniversalSearchTrait;
    use ExcelImportable;

    private $row;
    private $columns;
    private $company;
    private $recruitJobId;

    /**
     * Create a new job instance.
     */
    public function __construct($row, $columns, $company = null)
    {
        $this->row = $row;
        $this->columns = $columns;
        $this->company = $company;
        $this->recruitJobId = request()->recruit_job_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->isColumnExists('full_name') && $this->getColumnValue('full_name') !== 'Full Name') {

            try {

                $fullName = $this->getColumnValue('full_name');

                if (empty($fullName)) {
                    $this->failJob(__('recruit::messages.fullNameRequired'));
                    return;
                }

                $email = $this->getColumnValue('email');

                $emailExists = RecruitJobApplication::where('email', $email)->exists();

                request()->merge(['type' => 'import']);

                if ($emailExists) {
                    $this->failJob(__('recruit::messages.emailAlreadyExists', ['email' => $email]));
                } else {

                $source_id = ApplicationSource::where('application_source', $this->getColumnValue('source'))->first();
                $status = RecruitApplicationStatus::where('slug', 'applied')->where('company_id', company()->id)->first();
                    $jobApp = new RecruitJobApplication();
                    $jobApp->company_id = $this->company?->id;
                    $jobApp->recruit_job_id = $this->recruitJobId;

                    $jobApp->full_name = $this->getColumnValue('full_name');
                    $jobApp->email = $this->getColumnValue('email');

                    $jobApp->phone = $this->getColumnValue('phone');
                    $jobApp->gender = $this->isColumnExists('gender') ? $this->getColumnValue('gender') : null;
                    $jobApp->location_id = $this->company?->id;
                    $jobApp->application_source_id = $source_id;
                    $jobApp->current_ctc = $this->getColumnValue('current_ctc');
                    $jobApp->expected_ctc = $this->getColumnValue('expected_ctc');
                    $jobApp->total_experience = $this->getColumnValue('total_experience');
                    $jobApp->recruit_application_status_id = $status?->id;
                    $jobApp->save();

            }

            } catch (InvalidFormatException $e) {
                DB::rollBack();
                $this->failJob(__('messages.invalidDate'));
            } catch (Exception $e) {
                DB::rollBack();
                $this->failJobWithMessage($e->getMessage());
            }

        } else {
            $this->failJob(__('messages.invalidData'));
        }

    }

}
