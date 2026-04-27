<?php



use App\Http\Controllers\DesignationController;
use Illuminate\Support\Facades\Route;
use Modules\Recruit\Http\Controllers\JobController;
use Modules\Recruit\Http\Controllers\SkillController;
use Modules\Recruit\Http\Controllers\ReportController;
use Modules\Recruit\Http\Controllers\JobFileController;
use Modules\Recruit\Http\Controllers\JobTypeController;
use Modules\Recruit\Http\Controllers\EvaluationController;
use Modules\Recruit\Http\Controllers\ApplicantNoteController;
use Modules\Recruit\Http\Controllers\InterviewFileController;
use Modules\Recruit\Http\Controllers\Front\FrontJobController;
use Modules\Recruit\Http\Controllers\JobApplicationController;
use Modules\Recruit\Http\Controllers\JobOfferLetterController;
use Modules\Recruit\Http\Controllers\RecruitSettingController;
use Modules\Recruit\Http\Controllers\WorkExperienceController;
use Modules\Recruit\Http\Controllers\RecruitDashboardController;
use Modules\Recruit\Http\Controllers\CandidateDatabaseController;
use Modules\Recruit\Http\Controllers\FooterSettingsController;
use Modules\Recruit\Http\Controllers\InterviewScheduleController;
use Modules\Recruit\Http\Controllers\JobApplicationBoardController;
use Modules\Recruit\Http\Controllers\JobApplicationFilesController;
use Modules\Recruit\Http\Controllers\JobOfferLetterFilesController;
use Modules\Recruit\Http\Controllers\InterviewRecommendationStatusController;
use Modules\Recruit\Http\Controllers\InterviewStageController;
use Modules\Recruit\Http\Controllers\JobCategoryController;
use Modules\Recruit\Http\Controllers\JobSubCategoryController;
use Modules\Recruit\Http\Controllers\RecruitCandidateFollowUpController;
use Modules\Recruit\Http\Controllers\RecruitCustomQuestionController;
use Modules\Recruit\Http\Controllers\RecruitEmailNotificationSettingsController;
use Modules\Recruit\Http\Controllers\RecruiterController;
use Modules\Recruit\Http\Controllers\RecruitSourceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::post('save-application', [FrontJobController::class, 'saveApplication'])->name('save_application');
Route::get('careers/{slug?}', [FrontJobController::class, 'index'])->name('recruit');
Route::get('job-opening/{slug?}', [FrontJobController::class, 'jobOpenings'])->name('job_opening');
Route::get('job-opening/fetch-job/{company?}', [FrontJobController::class, 'fetchJob'])->name('job-opening.fetch_job');
Route::get('job-apply/{slug}/{location?}/{company?}', [FrontJobController::class, 'jobApply'])->name('job_apply');
Route::get('job-detail/{jobId}/{locationId}/{company}', [FrontJobController::class, 'jobDetail'])->name('job-detail');
Route::get('job-detail-page/{slug}/{location?}/{company?}', [FrontJobController::class, 'jobDetailPage'])->name('job_detail_page');
Route::get('/jobOffer/{hash}/{company?}', [FrontJobController::class, 'jobOfferLetter'])->name('front.jobOffer');
Route::get('job-offer-download/{id}/{slug}', [FrontJobController::class, 'download'])->name('jobOffer.download');
Route::post('/jobOffer-accept/{id}', [FrontJobController::class, 'jobOfferLetterStatusChange'])->name('front.job-offer.accept');
Route::get('/thankyou/{slug?}', [FrontJobController::class, 'thankyouPage'])->name('front.thankyou-page');
Route::get('pages/{job?}/{slug?}', [FrontJobController::class, 'customPage'])->name('front.custom-page');
Route::get('job-details-modal', [FrontJobController::class, 'jobDetailsModal'])->name('front.job_details_modal');
Route::get('job-alert/{slug?}', [FrontJobController::class, 'jobAlert'])->name('front.job_alert');
Route::post('job-alert-save', [FrontJobController::class, 'jobAlertStore'])->name('front.job_alert_store');
Route::get('job-alert-unsubscribe/{slug?}/{alertHash?}', [FrontJobController::class, 'jobAlertUnsubscribe'])->name('front.job_alert_unsubscribe');
Route::get('accept-offer/{id}', [FrontJobController::class, 'acceptOffer'])->name('front.accept_offer');

Route::group(['middleware' => 'auth', 'prefix' => 'account'], function () {
    Route::post('jobs/apply-quick-action', [JobController::class, 'applyQuickAction'])->name('jobs.apply_quick_action');
    Route::get('getJobSubCategories/{id}', [JobSubCategoryController::class, 'getSubCategories'])->name('get_job_sub_categories');
    Route::get('jobs/fetch-job', [JobController::class, 'fetchJob'])->name('jobs.fetch_job');
    Route::get('jobs/addRecruiter', [RecruiterController::class, 'addRecruiter'])->name('jobs.addRecruiter');
    Route::post('jobs/change-status', [JobController::class, 'changeJobStatus'])->name('jobs.change_job_status');
    Route::resource('interview-stages', InterviewStageController::class);
    Route::resource('jobs', JobController::class);
    Route::resource('work-experience', WorkExperienceController::class);
    Route::resource('job-sub-category', JobSubCategoryController::class);
    Route::resource('job-category', JobCategoryController::class);
    Route::resource('job-type', JobTypeController::class);

     // Interview schedule
     Route::group(
        ['prefix' => 'job-offer-letter'],
        function () {

            // Offer letter table action
            Route::post('send-offer-letter', [JobOfferLetterController::class, 'sendOffer'])->name('job-offer-letter.send-offer-letter');
            Route::post('withdraw-offer-letter', [JobOfferLetterController::class, 'withdrawOffer'])->name('job-offer-letter.withdraw-offer-letter');
            Route::post('apply-quick-action', [JobOfferLetterController::class, 'applyQuickAction'])->name('job-offer-letter.apply_quick_action');
            Route::post('change-status', [JobOfferLetterController::class, 'changeLetterStatus'])->name('job-offer-letter.change_letter_status');

            // Create new employee
            Route::get('create-employee/{id}', [JobOfferLetterController::class, 'createEmployee'])->name('job-offer-letter.create_employee');
            Route::post('employee-store', [JobOfferLetterController::class, 'employeeStore'])->name('job-offer-letter.employee-store');

            Route::get('fetch-applications', [JobOfferLetterController::class, 'fetchApplication'])->name('job-offer-letter.fetch-job-application');
            Route::get('fetch-component', [JobOfferLetterController::class, 'fetchComponent'])->name('job-offer-letter.fetch_component');
            Route::get('get-salary', [JobOfferLetterController::class, 'getSalary'])->name('job-offer-letter.get-salary');
            Route::get('fetched-currency', [JobOfferLetterController::class, 'fetchedCurrency'])->name('job-offer-letter.fetched-currency');

            // offer letter file
            Route::get('job-offer-file/download/{id}', [JobOfferLetterFilesController::class, 'download'])->name('job-offer-file.download');
            Route::resource('job-offer-file', JobOfferLetterFilesController::class);

            Route::get('create-designation', [JobOfferLetterController::class, 'createDesignation'])->name('job-offer-letter.create_designation');

            Route::post('store-designation', [JobOfferLetterController::class, 'designationStore'])->name('job-offer-letter.store-designation');
        });

    Route::resource('job-offer-letter', JobOfferLetterController::class);

    Route::post('job-skills/apply-quick-action', [SkillController::class, 'applyQuickAction'])->name('job-skills.apply_quick_action');
    Route::get('job-skills/addSkill', [SkillController::class, 'addSkill'])->name('job-skills.addSkill');
    Route::post('job-skills/storeSkill', [SkillController::class, 'storeSkill'])->name('job-skills.storeSkill');
    Route::post('job-skills/updateSkill/{id?}', [SkillController::class, 'updateSkill'])->name('job-skills.updateSkill');
    Route::resource('job-skills', SkillController::class);

    Route::resource('applicant-note', ApplicantNoteController::class);

    Route::get('job-files/download/{id}', [JobFileController::class, 'download'])->name('job_files.download');
    Route::resource('job-files', JobFileController::class);

    // Job application
    Route::group(
        ['prefix' => 'job-applications'],
        function () {
            Route::post('job-appboard/updateIndex', [JobApplicationBoardController::class, 'updateIndex'])->name('job-appboard.update_index');
            Route::post('job-appboard/add-status', [JobApplicationBoardController::class, 'addStatus'])->name('job-appboard.add-status');
            Route::post('job-appboard/add-skills', [JobApplicationBoardController::class, 'addSkill'])->name('job-appboard.add-skills');
            Route::post('job-appboard/store-status', [JobApplicationBoardController::class, 'storeStatus'])->name('job-appboard.store-status');
            // Fetch apply action label
            Route::get('job-appboard/fetch-status-model-label', [JobApplicationBoardController::class, 'fetchStatusModel'])->name('job-appboard.fetch-status-model-label');
            Route::post('job-appboard/collapseColumn', [JobApplicationBoardController::class, 'collapseColumn'])->name('job-appboard.collapse_column');
            Route::get('job-appboard/loadMore', [JobApplicationBoardController::class, 'loadMore'])->name('job-appboard.load_more');
            Route::get('job-appboard/application-remark/{id}/{board?}', [JobApplicationBoardController::class, 'applicationRemark'])->name('job-appboard.application_remark');
            Route::post('job-appboard/application-remark-store', [JobApplicationBoardController::class, 'applicationRemarkStore'])->name('job-appboard.application_remark_store');
            Route::get('job-appboard/interview/{id}/{board?}', [JobApplicationBoardController::class, 'interview'])->name('job-appboard.interview');
            Route::post('job-appboard/interview-store', [JobApplicationBoardController::class, 'interviewStore'])->name('job-appboard.interview_store');
            Route::get('job-appboard/offer-letter/{id}/{board?}', [JobApplicationBoardController::class, 'offerLetter'])->name('job-appboard.offer_letter');
            Route::post('job-appboard/offer-letter-store', [JobApplicationBoardController::class, 'offerLetterStore'])->name('job-appboard.offer_letter_store');
            Route::get('job-appboard/rejected-remark/{id}/{board?}', [JobApplicationBoardController::class, 'rejectedRemark'])->name('job-appboard.rejected_remark');
            Route::post('job-appboard/rejected-remark-store', [JobApplicationBoardController::class, 'rejectedRemarkStore'])->name('job-appboard.rejected_remark_store');

            Route::resource('job-appboard', JobApplicationBoardController::class);
            Route::resource('source-setting', RecruitSourceController::class);
            Route::get('import', [JobApplicationController::class, 'importJobApplication'])->name('job-applications.import');
            Route::post('import', [JobApplicationController::class, 'importStore'])->name('job-applications.import.store');
            Route::post('import/process', [JobApplicationController::class, 'importProcess'])->name('job-applications.import.process');
            Route::get('import/download-sample-csv', [JobApplicationController::class, 'downloadSampleCsv'])->name('job-applications.import.downloadSampleCsv');



        });

    Route::post('candidate-follow-up/change-follow-up-status', [RecruitCandidateFollowUpController::class, 'changefollowUpStatus'])->name('candidate-follow-up.change_follow_up_status');
    Route::resource('candidate-follow-up', RecruitCandidateFollowUpController::class);

    Route::post('job-applications/change-status', [JobApplicationController::class, 'changeStatus'])->name('job-applications.change_status');

    Route::post('job-applications/apply-quick-action', [JobApplicationController::class, 'applyQuickAction'])->name('job-applications.apply_quick_action');
    Route::get('job-application/location', [JobApplicationController::class, 'getLocation'])->name('job-applications.get_location');
    Route::get('job-applications/get_custom_fields', [JobApplicationController::class, 'getCustomFields'])->name('job-applications.get_custom_fields');
    Route::post('job-applications/quick-add-form-store', [JobApplicationController::class, 'quickAddFormStore'])->name('job-applications.quick_add_form_store');
    Route::resource('job-applications', JobApplicationController::class);

    Route::get('application-file/download/{id}', [JobApplicationFilesController::class, 'download'])->name('application-file.download');
    Route::resource('application-file', JobApplicationFilesController::class);

    // Footer links
    Route::post('footer-settings/change-status/{id}', [FooterSettingsController::class, 'changeStatus'])->name('footer-settings.change_status');
    Route::resource('footer-settings', FooterSettingsController::class);

    // Interview schedule
    Route::group(
        ['prefix' => 'interview-schedule'],
        function () {
            Route::get('table-view', [InterviewScheduleController::class, 'tableView'])->name('interview-schedule.table_view');

            Route::post('apply-quick-action', [InterviewScheduleController::class, 'applyQuickAction'])->name('interview-schedule.apply_quick_action');
            Route::post('change-status', [InterviewScheduleController::class, 'changeInterviewStatus'])->name('interview-schedule.change_interview_status');

            Route::post('update-occurrence/{id}', [InterviewScheduleController::class, 'updateOccurrence'])->name('interview-schedule.update_occurrence');

            // Interview reschedule
            Route::get('reschedule', [InterviewScheduleController::class, 'reschedule'])->name('interview-schedule.reschedule');
            Route::post('reschedule/store', [InterviewScheduleController::class, 'rescheduleStore'])->name('interview-schedule.reschedule.store');

            // Employee Response
            Route::get('response/{id}/{type}', [InterviewScheduleController::class, 'employeeResponse'])->name('interview-schedule.response');
            Route::post('employee-response', [InterviewScheduleController::class, 'response'])->name('interview-schedule.employee_response');
            Route::delete('/interview-rounds/{id}/delete', [InterviewScheduleController::class, 'deleteInterviewRounds'])->name('interview-rounds.delete');

            // Interview file
            Route::get('interview-files/download/{id}', [InterviewFileController::class, 'download'])->name('interview_files.download');
            Route::resource('interview-files', InterviewFileController::class);

        });

    Route::resource('interview-schedule', InterviewScheduleController::class);

    // Candidate Database
    Route::resource('candidate-database', CandidateDatabaseController::class);

    // Report
    Route::post('report-chart', [ReportController::class, 'reportChartData'])->name('jobreport.chart');
    Route::resource('recruit-job-report', ReportController::class);

    Route::resource('evaluation', EvaluationController::class);
    Route::resource('recommendation-status', InterviewRecommendationStatusController::class);


    Route::resource('notification-settings', RecruitEmailNotificationSettingsController::class);
    // Dashboard
    Route::resource('recruit-dashboard', RecruitDashboardController::class);

    // Custom Question
    Route::post('custom-question-settings/change-status', [RecruitCustomQuestionController::class, 'changeQuestionStatus'])->name('custom-question-settings.change_status');
    Route::resource('custom-question-settings', RecruitCustomQuestionController::class);

    // Recruit Settings
    Route::resource('recruit-settings', RecruitSettingController::class);
    Route::resource('recruiter', RecruiterController::class);
});
