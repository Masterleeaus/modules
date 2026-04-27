@include('recruit::import.process-form', [
    'headingTitle' => __('recruit::modules.jobApplication.importJobCandidates'),
    'processRoute' => route('job-applications.import.process'),
    'backRoute' => route('job-applications.index'),
    'backButtonText' => __('recruit::app.jobApplication.backToJobApplications'),
])
