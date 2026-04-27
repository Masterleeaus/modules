<?php

namespace Modules\Recruit\Http\Requests\Skill;

use Illuminate\Validation\Rule;
use App\Http\Requests\CoreRequest;

class UpdateSkill extends CoreRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function rules()
    {

        return [
            'name' => 'required|unique:recruit_skills,name,' . $this->route('job_skill').',id,company_id,' . company()->id,
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('recruit::modules.skill.addSkills'),
        ];
    }
}
