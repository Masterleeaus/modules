<?php

namespace Modules\Recruit\Http\Requests\ZoomMeeting;

use Froiden\LaravelInstaller\Request\CoreRequest;

class UpdateMeeting extends CoreRequest
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
        $data = [];
    
        if ($this->interview_type == 'phone') {
            $data['phone'] = 'required|numeric';
        } else {
            $data['candidate_id'] = 'required';
            $data['employee_id.0'] = 'required';
        }
    
        if ($this->interview_type == 'video') {
            if ($this->video_type == 'zoom') {
                $data['meeting_title'] = 'required';
                $data['candidate_id'] = 'required';
                $data['end_date'] = 'required|after_or_equal:scheduleDate';
                $data['end_time'] = 'required';
                $data['employee_id.0'] = 'required';
    
                if ($this->scheduleDate == $this->end_date) {
                    $data['end_time'] .= '|required|after_or_equal:scheduleTime';
                }
            } else {
                $data['other_link'] = 'required';
                $data['candidate_id'] = 'required';
                $data['employee_id.0'] = 'required';
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
