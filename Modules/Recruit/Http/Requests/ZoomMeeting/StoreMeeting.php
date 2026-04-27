<?php

namespace Modules\Recruit\Http\Requests\ZoomMeeting;

use App\Http\Requests\CoreRequest;
use Modules\Recruit\Rules\CheckInterviewSchedule;

class StoreMeeting extends CoreRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if ($this->interview_type == 'phone') {
            $data = [
                'jobId' => 'required',
                'phone' => 'required|numeric',
                'candidate_id' => ['required', new CheckInterviewSchedule],
                'employee_id.0' => 'required',
                'jobStage' => 'required',
            ];
        } else {
            $data = [
                'jobId' => 'required',
                'candidate_id' => ['required', new CheckInterviewSchedule],
                'employee_id.0' => 'required',
                'jobStage' => 'required',
            ];
        }

        if ($this->interview_type == 'video') {
            if ($this->video_type == 'zoom') {
                $data = [
                    'jobId' => 'required',
                    'meeting_title' => 'required',
                    'candidate_id' => ['required', new CheckInterviewSchedule],
                    'end_date' => 'required|after_or_equal:start_date',
                    'end_time' => 'required',
                    'employee_id.0' => 'required',
                    'jobStage' => 'required',
                ];

                if ($this->start_date == $this->end_date) {
                    $data['end_time'] .= '|after_or_equal:start_time';
                }
                
            } else {
                $data = [
                    'jobId' => 'required',
                    'other_link' => 'required',
                    'candidate_id' => ['required', new CheckInterviewSchedule],
                    'employee_id.0' => 'required',
                    'jobStage' => 'required',
                ];
            }
        }

        return $data;
    }

    public function messages()
    {
        return [
            'employee_id.0.required' => __('recruit::messages.employeeField'),
            'candidate_id.0.required' => __('recruit::messages.candidateField'),
        ];
    }
}
