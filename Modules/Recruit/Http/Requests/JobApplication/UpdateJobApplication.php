<?php

namespace Modules\Recruit\Http\Requests\JobApplication;

use App\Http\Requests\CoreRequest;
use Modules\Recruit\Entities\RecruitJob;
use Modules\Recruit\Rules\CheckApplication;
use Modules\Recruit\Entities\RecruitJobQuestion;
use Modules\Recruit\Traits\CustomQuestion;

class UpdateJobApplication extends CoreRequest
{
    use CustomQuestion;
    /**
     * Get the validation rules that apply to the request.
     *
     * @return bool
     */
    public function rules()
    {
        $setting = company();

        if (request()->job_id) {
            $jobId = RecruitJob::where('id', request()->job_id)->first();

            $data = ['job_id' => 'required',
                'full_name' => 'required',
                'phone' => 'required',
                'location_id' => 'required', ];

            if (! is_null(request()->email) && request()->application_id != request()->id) {
                $data['email'] = [new CheckApplication];
            }

            if ($jobId->is_gender_require) {
                $data['gender'] = 'required';
            }

            if ($jobId->is_dob_require) {
                $data['date_of_birth'] = 'required|date_format:"'.$setting->date_format.'"|before_or_equal:'.now($setting->timezone)->toDateString();
            }

            if ($jobId->is_photo_require) {
                $data['photo'] = 'required';
            }

            if ($jobId->is_resume_require) {
                $data['resume'] = 'required';
            }

            $job = RecruitJob::with('question')->findOrFail(request()->job_id);
            $selectedQuestions = $job ? RecruitJobQuestion::where('recruit_job_id', request()->job_id)->get() : null;
            $fields = $this->fetchQuestion($selectedQuestions);

            foreach ($fields as $question) {

                foreach ($question as $q) {
                    $fieldKey = "answer.{$q->id}";

                    if ($q->required == 'yes') {
                        if ($q->type === 'file') {
                            $data[$fieldKey] = 'required|file';
                        } elseif ($q->type === 'date') {
                            $data[$fieldKey] = 'required|date_format:"'.$setting->date_format.'"';
                        } else {
                            $data[$fieldKey] = 'required';
                        }
                    }
                }
            }
        } else {
            $data = ['job_id' => 'required',
                'full_name' => 'required',
                'phone' => 'required',
                'location_id' => 'required', ];
        }

        return $data;
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        $messages = [];

        if (request()->job_id) {
            $job = RecruitJob::with('question')->findOrFail(request()->job_id);
            $selectedQuestions = $job ? RecruitJobQuestion::where('recruit_job_id', request()->job_id)->get() : null;
            $fields = $this->fetchQuestion($selectedQuestions);

            foreach ($fields as $question) {
                foreach ($question as $q) {
                    if ($q->required) {
                        $messages["answer.{$q->id}.required"] = __(':attribute is required', ['attribute' => $q->question]);
                    }
                }
            }
        }

        return $messages;
    }
}
