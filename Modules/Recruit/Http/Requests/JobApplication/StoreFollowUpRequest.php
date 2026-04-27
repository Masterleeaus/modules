<?php

namespace Modules\Recruit\Http\Requests\JobApplication;

use App\Http\Requests\CoreRequest;
use Modules\Recruit\Entities\RecruitJobApplication;

class StoreFollowUpRequest extends CoreRequest
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
        $application = RecruitJobApplication::findOrFail($this->candidate_id);
        $setting = company();

        $rules = [];

        if (request()->has('send_reminder')) {
            $rules['remind_time'] = 'required';
        }

        $rules['next_follow_up_date'] = 'required|date_format:"'.$setting->date_format.'"|after_or_equal:'.$application->created_at->format($setting->date_format);

        return $rules;
    }
}
