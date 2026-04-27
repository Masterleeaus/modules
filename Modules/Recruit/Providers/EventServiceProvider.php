<?php

namespace Modules\Recruit\Providers;

use App\Events\NewCompanyCreatedEvent;
use App\Observers\RecruitSourceObserver as ObserversRecruitSourceObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Recruit\Entities\ApplicationSource;
use Modules\Recruit\Entities\RecruitApplicationStatus;
use Modules\Recruit\Entities\RecruitCandidateDatabase;
use Modules\Recruit\Entities\RecruitCandidateFollowUp;
use Modules\Recruit\Entities\RecruitEmailNotificationSetting;
use Modules\Recruit\Entities\Recruiter;
use Modules\Recruit\Entities\RecruitFooterLink;
use Modules\Recruit\Entities\RecruitInterviewEvaluation;
use Modules\Recruit\Entities\RecruitInterviewSchedule;
use Modules\Recruit\Entities\RecruitInterviewStage;
use Modules\Recruit\Entities\RecruitJob;
use Modules\Recruit\Entities\RecruitJobAddress;
use Modules\Recruit\Entities\RecruitJobApplication;
use Modules\Recruit\Entities\RecruitJobCategory;
use Modules\Recruit\Entities\RecruitJobOfferLetter;
use Modules\Recruit\Entities\RecruitJobSubCategory;
use Modules\Recruit\Entities\RecruitJobType;
use Modules\Recruit\Entities\RecruitRecommendationStatus;
use Modules\Recruit\Entities\RecruitSalaryStructure;
use Modules\Recruit\Entities\RecruitSelectedSalaryComponent;
use Modules\Recruit\Entities\RecruitSkill;
use Modules\Recruit\Entities\RecruitWorkExperience;
use Modules\Recruit\Events\CandidateFollowUpReminderEvent;
use Modules\Recruit\Events\CandidateInterviewRescheduleEvent;
use Modules\Recruit\Events\CandidateInterviewScheduleEvent;
use Modules\Recruit\Events\HostInterviewEvent;
use Modules\Recruit\Events\InterviewRescheduleEvent;
use Modules\Recruit\Events\InterviewScheduleEvent;
use Modules\Recruit\Events\JobApplicationStatusChangeEvent;
use Modules\Recruit\Events\JobOfferStatusChangeEvent;
use Modules\Recruit\Events\NewJobApplicationEvent;
use Modules\Recruit\Events\NewJobEvent;
use Modules\Recruit\Events\OfferLetterEvent;
use Modules\Recruit\Events\RecruitJobAlertEvent;
use Modules\Recruit\Events\RecruitJobAlertUpdateEvent;
use Modules\Recruit\Events\SendOfferLetterReminderEvent;
use Modules\Recruit\Events\UpdateInterviewScheduleEvent;
use Modules\Recruit\Events\UpdateJobApplicationEvent;
use Modules\Recruit\Events\UpdateJobEvent;
use Modules\Recruit\Events\UpdateOfferLetterEvent;
use Modules\Recruit\Listeners\CandidateFollowUpReminderListener;
use Modules\Recruit\Listeners\CandidateInterviewRescheduleListener;
use Modules\Recruit\Listeners\CandidateInterviewScheduleListener;
use Modules\Recruit\Listeners\CompanyCreatedListener;
use Modules\Recruit\Listeners\HostInterviewListener;
use Modules\Recruit\Listeners\InterviewRescheduleListener;
use Modules\Recruit\Listeners\InterviewScheduleListener;
use Modules\Recruit\Listeners\JobApplicationStatusChangeListener;
use Modules\Recruit\Listeners\JobOfferStatusChangeListener;
use Modules\Recruit\Listeners\NewJobApplicationListener;
use Modules\Recruit\Listeners\NewJobListener;
use Modules\Recruit\Listeners\OfferLetterListener;
use Modules\Recruit\Listeners\RecruitJobAlertListener;
use Modules\Recruit\Listeners\RecruitJobAlertUpdateListener;
use Modules\Recruit\Listeners\SendOfferLetterReminderListener;
use Modules\Recruit\Listeners\UpdateInterviewScheduleListener;
use Modules\Recruit\Listeners\UpdateJobApplicationListener;
use Modules\Recruit\Listeners\UpdateJobListener;
use Modules\Recruit\Listeners\UpdateOfferLetterListener;
use Modules\Recruit\Observers\InterviewScheduleObserver;
use Modules\Recruit\Observers\JobApplicationsObserver;
use Modules\Recruit\Observers\JobsObserver;
use Modules\Recruit\Observers\RecruitApplicationStatusObserver;
use Modules\Recruit\Observers\RecruitCandidateDatabaseObserver;
use Modules\Recruit\Observers\RecruitCandidateFollowUpObserver;
use Modules\Recruit\Observers\RecruitEmailNotificationObserver;
use Modules\Recruit\Observers\RecruiterObserver;
use Modules\Recruit\Observers\RecruitFooterLinkObserver;
use Modules\Recruit\Observers\RecruitInterviewEvaluationObserver;
use Modules\Recruit\Observers\RecruitInterviewStagesObserver;
use Modules\Recruit\Observers\RecruitJobAddressObserver;
use Modules\Recruit\Observers\RecruitJobCategoryObserver;
use Modules\Recruit\Observers\RecruitJobOfferLetterObserver;
use Modules\Recruit\Observers\RecruitJobSubCategoryObserver;
use Modules\Recruit\Observers\RecruitJobTypesObserver;
use Modules\Recruit\Observers\RecruitRecommendationStatusObserver;
use Modules\Recruit\Observers\RecruitSalaryStructureObserver;
use Modules\Recruit\Observers\RecruitSelectedSalaryComponentObserver;
use Modules\Recruit\Observers\RecruitSkillObserver;
use Modules\Recruit\Observers\RecruitWorkExperienceObserver;
use Modules\Recruit\Observers\RecruitSourceObserver;


class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        InterviewScheduleEvent::class => [InterviewScheduleListener::class],
        UpdateInterviewScheduleEvent::class => [UpdateInterviewScheduleListener::class],
        CandidateInterviewScheduleEvent::class => [CandidateInterviewScheduleListener::class],
        OfferLetterEvent::class => [OfferLetterListener::class],
        JobOfferStatusChangeEvent::class => [JobOfferStatusChangeListener::class],
        JobApplicationStatusChangeEvent::class => [JobApplicationStatusChangeListener::class],
        CandidateInterviewRescheduleEvent::class => [CandidateInterviewRescheduleListener::class],
        InterviewRescheduleEvent::class => [InterviewRescheduleListener::class],
        HostInterviewEvent::class => [HostInterviewListener::class],
        NewJobEvent::class => [NewJobListener::class],
        UpdateJobEvent::class => [UpdateJobListener::class],
        NewJobApplicationEvent::class => [NewJobApplicationListener::class],
        UpdateJobApplicationEvent::class => [UpdateJobApplicationListener::class],
        UpdateOfferLetterEvent::class => [UpdateOfferLetterListener::class],
        NewCompanyCreatedEvent::class => [CompanyCreatedListener::class],
        SendOfferLetterReminderEvent::class => [SendOfferLetterReminderListener::class],
        RecruitJobAlertEvent::class => [RecruitJobAlertListener::class],
        CandidateFollowUpReminderEvent::class => [CandidateFollowUpReminderListener::class],
        RecruitJobAlertUpdateEvent::class => [RecruitJobAlertUpdateListener::class],

    ];

    protected $observers = [
        RecruitSkill::class => [RecruitSkillObserver::class],
        RecruitJob::class => [JobsObserver::class],
        RecruitInterviewStage::class => [RecruitInterviewStagesObserver::class],
        RecruitWorkExperience::class => [RecruitWorkExperienceObserver::class],
        RecruitJobType::class => [RecruitJobTypesObserver::class],
        RecruitFooterLink::class => [RecruitFooterLinkObserver::class],
        RecruitJobApplication::class => [JobApplicationsObserver::class],
        RecruitInterviewSchedule::class => [InterviewScheduleObserver::class],
        RecruitEmailNotificationSetting::class => [RecruitEmailNotificationObserver::class],
        RecruitCandidateDatabase::class => [RecruitCandidateDatabaseObserver::class],
        RecruitJobOfferLetter::class => [RecruitJobOfferLetterObserver::class],
        RecruitJobCategory::class => [RecruitJobCategoryObserver::class],
        RecruitJobSubCategory::class => [RecruitJobSubCategoryObserver::class],
        RecruitApplicationStatus::class => [RecruitApplicationStatusObserver::class],
        Recruiter::class => [RecruiterObserver::class],
        RecruitInterviewEvaluation::class => [RecruitInterviewEvaluationObserver::class],
        RecruitRecommendationStatus::class => [RecruitRecommendationStatusObserver::class],
        RecruitJobAddress::class => [RecruitJobAddressObserver::class],
        RecruitSelectedSalaryComponent::class => [RecruitSelectedSalaryComponentObserver::class],
        RecruitSalaryStructure::class => [RecruitSalaryStructureObserver::class],
        RecruitCandidateFollowUp::class => [RecruitCandidateFollowUpObserver::class],
        ApplicationSource::class => [RecruitSourceObserver::class],
    ];
}
