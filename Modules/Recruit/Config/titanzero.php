<?php

return [
    'module' => 'recruit',
    'capabilities' => [
        [
            'key' => 'recruit.help.explain_page',
            'label' => 'Recruit: Explain this page',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'titanzero.intent.explain_page',
            'voice_phrases' => [
                'what is this page',
                'explain this',
                'help me'
            ]
        ],
        [
            'key' => 'recruit.application-file.download',
            'label' => 'Recruit: Download',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'application-file.download',
            'voice_phrases' => [
                'download'
            ]
        ],
        [
            'key' => 'recruit.candidate-follow-up.change_follow_up_status',
            'label' => 'Recruit: Change Follow Up Status',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'candidate-follow-up.change_follow_up_status',
            'voice_phrases' => [
                'change follow up status'
            ]
        ],
        [
            'key' => 'recruit.custom-question-settings.change_status',
            'label' => 'Recruit: Change Status',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'custom-question-settings.change_status',
            'voice_phrases' => [
                'change status'
            ]
        ],
        [
            'key' => 'recruit.footer-settings.change_status',
            'label' => 'Recruit: Change Status',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'footer-settings.change_status',
            'voice_phrases' => [
                'change status'
            ]
        ],
        [
            'key' => 'recruit.front.accept_offer',
            'label' => 'Recruit: Accept Offer',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'front.accept_offer',
            'voice_phrases' => [
                'accept offer'
            ]
        ],
        [
            'key' => 'recruit.front.custom-page',
            'label' => 'Recruit: Custom Page',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'front.custom-page',
            'voice_phrases' => [
                'custom page'
            ]
        ],
        [
            'key' => 'recruit.front.job-offer.accept',
            'label' => 'Recruit: Accept',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'front.job-offer.accept',
            'voice_phrases' => [
                'accept'
            ]
        ],
        [
            'key' => 'recruit.front.jobOffer',
            'label' => 'Recruit: Joboffer',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'front.jobOffer',
            'voice_phrases' => [
                'joboffer'
            ]
        ],
        [
            'key' => 'recruit.front.job_alert',
            'label' => 'Recruit: Job Alert',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'front.job_alert',
            'voice_phrases' => [
                'job alert'
            ]
        ],
        [
            'key' => 'recruit.front.job_alert_store',
            'label' => 'Recruit: Job Alert Store',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'front.job_alert_store',
            'voice_phrases' => [
                'job alert store'
            ]
        ],
        [
            'key' => 'recruit.front.job_alert_unsubscribe',
            'label' => 'Recruit: Job Alert Unsubscribe',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'front.job_alert_unsubscribe',
            'voice_phrases' => [
                'job alert unsubscribe'
            ]
        ],
        [
            'key' => 'recruit.front.job_details_modal',
            'label' => 'Recruit: Job Details Modal',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'front.job_details_modal',
            'voice_phrases' => [
                'job details modal'
            ]
        ],
        [
            'key' => 'recruit.front.thankyou-page',
            'label' => 'Recruit: Thankyou Page',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'front.thankyou-page',
            'voice_phrases' => [
                'thankyou page'
            ]
        ],
        [
            'key' => 'recruit.get_job_sub_categories',
            'label' => 'Recruit: Get Job Sub Categories',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'get_job_sub_categories',
            'voice_phrases' => [
                'get job sub categories'
            ]
        ],
        [
            'key' => 'recruit.interview-rounds.delete',
            'label' => 'Recruit: Delete',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'interview-rounds.delete',
            'voice_phrases' => [
                'delete'
            ]
        ],
        [
            'key' => 'recruit.interview-schedule.apply_quick_action',
            'label' => 'Recruit: Apply Quick Action',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'interview-schedule.apply_quick_action',
            'voice_phrases' => [
                'apply quick action'
            ]
        ],
        [
            'key' => 'recruit.interview-schedule.change_interview_status',
            'label' => 'Recruit: Change Interview Status',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'interview-schedule.change_interview_status',
            'voice_phrases' => [
                'change interview status'
            ]
        ],
        [
            'key' => 'recruit.interview-schedule.employee_response',
            'label' => 'Recruit: Employee Response',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'interview-schedule.employee_response',
            'voice_phrases' => [
                'employee response'
            ]
        ],
        [
            'key' => 'recruit.interview-schedule.reschedule',
            'label' => 'Recruit: Reschedule',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'interview-schedule.reschedule',
            'voice_phrases' => [
                'reschedule'
            ]
        ],
        [
            'key' => 'recruit.interview-schedule.reschedule.store',
            'label' => 'Recruit: Store',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'interview-schedule.reschedule.store',
            'voice_phrases' => [
                'store'
            ]
        ],
        [
            'key' => 'recruit.interview-schedule.response',
            'label' => 'Recruit: Response',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'interview-schedule.response',
            'voice_phrases' => [
                'response'
            ]
        ],
        [
            'key' => 'recruit.interview-schedule.table_view',
            'label' => 'Recruit: Table View',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'interview-schedule.table_view',
            'voice_phrases' => [
                'table view'
            ]
        ],
        [
            'key' => 'recruit.interview-schedule.update_occurrence',
            'label' => 'Recruit: Update Occurrence',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'interview-schedule.update_occurrence',
            'voice_phrases' => [
                'update occurrence'
            ]
        ],
        [
            'key' => 'recruit.interview_files.download',
            'label' => 'Recruit: Download',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'interview_files.download',
            'voice_phrases' => [
                'download'
            ]
        ],
        [
            'key' => 'recruit.job-appboard.add-skills',
            'label' => 'Recruit: Add Skills',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-appboard.add-skills',
            'voice_phrases' => [
                'add skills'
            ]
        ],
        [
            'key' => 'recruit.job-appboard.add-status',
            'label' => 'Recruit: Add Status',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-appboard.add-status',
            'voice_phrases' => [
                'add status'
            ]
        ],
        [
            'key' => 'recruit.job-appboard.application_remark',
            'label' => 'Recruit: Application Remark',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-appboard.application_remark',
            'voice_phrases' => [
                'application remark'
            ]
        ],
        [
            'key' => 'recruit.job-appboard.application_remark_store',
            'label' => 'Recruit: Application Remark Store',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-appboard.application_remark_store',
            'voice_phrases' => [
                'application remark store'
            ]
        ],
        [
            'key' => 'recruit.job-appboard.collapse_column',
            'label' => 'Recruit: Collapse Column',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-appboard.collapse_column',
            'voice_phrases' => [
                'collapse column'
            ]
        ],
        [
            'key' => 'recruit.job-appboard.fetch-status-model-label',
            'label' => 'Recruit: Fetch Status Model Label',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-appboard.fetch-status-model-label',
            'voice_phrases' => [
                'fetch status model label'
            ]
        ],
        [
            'key' => 'recruit.job-appboard.interview',
            'label' => 'Recruit: Interview',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-appboard.interview',
            'voice_phrases' => [
                'interview'
            ]
        ],
        [
            'key' => 'recruit.job-appboard.interview_store',
            'label' => 'Recruit: Interview Store',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-appboard.interview_store',
            'voice_phrases' => [
                'interview store'
            ]
        ],
        [
            'key' => 'recruit.job-appboard.load_more',
            'label' => 'Recruit: Load More',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-appboard.load_more',
            'voice_phrases' => [
                'load more'
            ]
        ],
        [
            'key' => 'recruit.job-appboard.offer_letter',
            'label' => 'Recruit: Offer Letter',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-appboard.offer_letter',
            'voice_phrases' => [
                'offer letter'
            ]
        ],
        [
            'key' => 'recruit.job-appboard.offer_letter_store',
            'label' => 'Recruit: Offer Letter Store',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-appboard.offer_letter_store',
            'voice_phrases' => [
                'offer letter store'
            ]
        ],
        [
            'key' => 'recruit.job-appboard.rejected_remark',
            'label' => 'Recruit: Rejected Remark',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-appboard.rejected_remark',
            'voice_phrases' => [
                'rejected remark'
            ]
        ],
        [
            'key' => 'recruit.job-appboard.rejected_remark_store',
            'label' => 'Recruit: Rejected Remark Store',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-appboard.rejected_remark_store',
            'voice_phrases' => [
                'rejected remark store'
            ]
        ],
        [
            'key' => 'recruit.job-appboard.store-status',
            'label' => 'Recruit: Store Status',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-appboard.store-status',
            'voice_phrases' => [
                'store status'
            ]
        ],
        [
            'key' => 'recruit.job-appboard.update_index',
            'label' => 'Recruit: Update Index',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-appboard.update_index',
            'voice_phrases' => [
                'update index'
            ]
        ],
        [
            'key' => 'recruit.job-applications.apply_quick_action',
            'label' => 'Recruit: Apply Quick Action',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-applications.apply_quick_action',
            'voice_phrases' => [
                'apply quick action'
            ]
        ],
        [
            'key' => 'recruit.job-applications.change_status',
            'label' => 'Recruit: Change Status',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-applications.change_status',
            'voice_phrases' => [
                'change status'
            ]
        ],
        [
            'key' => 'recruit.job-applications.get_custom_fields',
            'label' => 'Recruit: Get Custom Fields',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-applications.get_custom_fields',
            'voice_phrases' => [
                'get custom fields'
            ]
        ],
        [
            'key' => 'recruit.job-applications.get_location',
            'label' => 'Recruit: Get Location',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-applications.get_location',
            'voice_phrases' => [
                'get location'
            ]
        ],
        [
            'key' => 'recruit.job-applications.import',
            'label' => 'Recruit: Import',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-applications.import',
            'voice_phrases' => [
                'import'
            ]
        ],
        [
            'key' => 'recruit.job-applications.import.downloadSampleCsv',
            'label' => 'Recruit: Downloadsamplecsv',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-applications.import.downloadSampleCsv',
            'voice_phrases' => [
                'downloadsamplecsv'
            ]
        ],
        [
            'key' => 'recruit.job-applications.import.process',
            'label' => 'Recruit: Process',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-applications.import.process',
            'voice_phrases' => [
                'process'
            ]
        ],
        [
            'key' => 'recruit.job-applications.import.store',
            'label' => 'Recruit: Store',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-applications.import.store',
            'voice_phrases' => [
                'store'
            ]
        ],
        [
            'key' => 'recruit.job-applications.quick_add_form_store',
            'label' => 'Recruit: Quick Add Form Store',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-applications.quick_add_form_store',
            'voice_phrases' => [
                'quick add form store'
            ]
        ],
        [
            'key' => 'recruit.job-detail',
            'label' => 'Recruit: Job Detail',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-detail',
            'voice_phrases' => [
                'job detail'
            ]
        ],
        [
            'key' => 'recruit.job-offer-file.download',
            'label' => 'Recruit: Download',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-offer-file.download',
            'voice_phrases' => [
                'download'
            ]
        ],
        [
            'key' => 'recruit.job-offer-letter.apply_quick_action',
            'label' => 'Recruit: Apply Quick Action',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-offer-letter.apply_quick_action',
            'voice_phrases' => [
                'apply quick action'
            ]
        ],
        [
            'key' => 'recruit.job-offer-letter.change_letter_status',
            'label' => 'Recruit: Change Letter Status',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-offer-letter.change_letter_status',
            'voice_phrases' => [
                'change letter status'
            ]
        ],
        [
            'key' => 'recruit.job-offer-letter.create_designation',
            'label' => 'Recruit: Create Designation',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-offer-letter.create_designation',
            'voice_phrases' => [
                'create designation'
            ]
        ],
        [
            'key' => 'recruit.job-offer-letter.create_employee',
            'label' => 'Recruit: Create Employee',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-offer-letter.create_employee',
            'voice_phrases' => [
                'create employee'
            ]
        ],
        [
            'key' => 'recruit.job-offer-letter.employee-store',
            'label' => 'Recruit: Employee Store',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-offer-letter.employee-store',
            'voice_phrases' => [
                'employee store'
            ]
        ],
        [
            'key' => 'recruit.job-offer-letter.fetch-job-application',
            'label' => 'Recruit: Fetch Job Application',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-offer-letter.fetch-job-application',
            'voice_phrases' => [
                'fetch job application'
            ]
        ],
        [
            'key' => 'recruit.job-offer-letter.fetch_component',
            'label' => 'Recruit: Fetch Component',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-offer-letter.fetch_component',
            'voice_phrases' => [
                'fetch component'
            ]
        ],
        [
            'key' => 'recruit.job-offer-letter.fetched-currency',
            'label' => 'Recruit: Fetched Currency',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-offer-letter.fetched-currency',
            'voice_phrases' => [
                'fetched currency'
            ]
        ],
        [
            'key' => 'recruit.job-offer-letter.get-salary',
            'label' => 'Recruit: Get Salary',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-offer-letter.get-salary',
            'voice_phrases' => [
                'get salary'
            ]
        ],
        [
            'key' => 'recruit.job-offer-letter.send-offer-letter',
            'label' => 'Recruit: Send Offer Letter',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-offer-letter.send-offer-letter',
            'voice_phrases' => [
                'send offer letter'
            ]
        ],
        [
            'key' => 'recruit.job-offer-letter.store-designation',
            'label' => 'Recruit: Store Designation',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-offer-letter.store-designation',
            'voice_phrases' => [
                'store designation'
            ]
        ],
        [
            'key' => 'recruit.job-offer-letter.withdraw-offer-letter',
            'label' => 'Recruit: Withdraw Offer Letter',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-offer-letter.withdraw-offer-letter',
            'voice_phrases' => [
                'withdraw offer letter'
            ]
        ],
        [
            'key' => 'recruit.job-opening.fetch_job',
            'label' => 'Recruit: Fetch Job',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-opening.fetch_job',
            'voice_phrases' => [
                'fetch job'
            ]
        ],
        [
            'key' => 'recruit.job-skills.addSkill',
            'label' => 'Recruit: Addskill',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-skills.addSkill',
            'voice_phrases' => [
                'addskill'
            ]
        ],
        [
            'key' => 'recruit.job-skills.apply_quick_action',
            'label' => 'Recruit: Apply Quick Action',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-skills.apply_quick_action',
            'voice_phrases' => [
                'apply quick action'
            ]
        ],
        [
            'key' => 'recruit.job-skills.storeSkill',
            'label' => 'Recruit: Storeskill',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-skills.storeSkill',
            'voice_phrases' => [
                'storeskill'
            ]
        ],
        [
            'key' => 'recruit.job-skills.updateSkill',
            'label' => 'Recruit: Updateskill',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job-skills.updateSkill',
            'voice_phrases' => [
                'updateskill'
            ]
        ],
        [
            'key' => 'recruit.jobOffer.download',
            'label' => 'Recruit: Download',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'jobOffer.download',
            'voice_phrases' => [
                'download'
            ]
        ],
        [
            'key' => 'recruit.job_apply',
            'label' => 'Recruit: Job Apply',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job_apply',
            'voice_phrases' => [
                'job apply'
            ]
        ],
        [
            'key' => 'recruit.job_detail_page',
            'label' => 'Recruit: Job Detail Page',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job_detail_page',
            'voice_phrases' => [
                'job detail page'
            ]
        ],
        [
            'key' => 'recruit.job_files.download',
            'label' => 'Recruit: Download',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job_files.download',
            'voice_phrases' => [
                'download'
            ]
        ],
        [
            'key' => 'recruit.job_opening',
            'label' => 'Recruit: Job Opening',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'job_opening',
            'voice_phrases' => [
                'job opening'
            ]
        ],
        [
            'key' => 'recruit.jobreport.chart',
            'label' => 'Recruit: Chart',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'jobreport.chart',
            'voice_phrases' => [
                'chart'
            ]
        ],
        [
            'key' => 'recruit.jobs.addRecruiter',
            'label' => 'Recruit: Addrecruiter',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'jobs.addRecruiter',
            'voice_phrases' => [
                'addrecruiter'
            ]
        ],
        [
            'key' => 'recruit.jobs.apply_quick_action',
            'label' => 'Recruit: Apply Quick Action',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'jobs.apply_quick_action',
            'voice_phrases' => [
                'apply quick action'
            ]
        ],
        [
            'key' => 'recruit.jobs.change_job_status',
            'label' => 'Recruit: Change Job Status',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'jobs.change_job_status',
            'voice_phrases' => [
                'change job status'
            ]
        ],
        [
            'key' => 'recruit.jobs.fetch_job',
            'label' => 'Recruit: Fetch Job',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'jobs.fetch_job',
            'voice_phrases' => [
                'fetch job'
            ]
        ],
        [
            'key' => 'recruit.recruit',
            'label' => 'Recruit: Recruit',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'recruit',
            'voice_phrases' => [
                'recruit'
            ]
        ],
        [
            'key' => 'recruit.save_application',
            'label' => 'Recruit: Save Application',
            'risk' => 'low',
            'requires' => [],
            'handler' => 'save_application',
            'voice_phrases' => [
                'save application'
            ]
        ]
    ],
    'go_enabled' => true,
    'zero_enabled' => true
];
