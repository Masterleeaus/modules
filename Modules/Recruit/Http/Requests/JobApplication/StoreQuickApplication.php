<?php

namespace Modules\Recruit\Http\Requests\JobApplication;

use App\Http\Requests\CoreRequest;
use Modules\Recruit\Entities\RecruitSetting;
use Modules\Recruit\Rules\CheckApplication;

class StoreQuickApplication extends CoreRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function rules()
    {
        $settings = RecruitSetting::select('form_settings')->first();
        $this->formSettings = collect([]);

        if ($settings) {
            $formSettings = $settings->form_settings;

            foreach ($formSettings as $form) {
                if ($form['status'] == true) {
                    $this->formSettings->push($form);
                }
            }

        }

        $this->formFields = $this->formSettings->pluck('name')->toArray();

        $data = [
            'job_id' => 'required',
            'full_name' => 'required',
            'location_id' => 'required',
        ];

        if (in_array('email', $this->formFields)) {
            $data['email'] = ['email', new CheckApplication];
        }

        if (in_array('phone', $this->formFields)) {
            $data['phone'] = 'required';
        }

        return $data;
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            //
        ];
    }
}
