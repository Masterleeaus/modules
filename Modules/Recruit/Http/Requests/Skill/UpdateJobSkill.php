<?php

namespace Modules\Recruit\Http\Requests\Skill;

use App\Http\Requests\CoreRequest;

class UpdateJobSkill extends CoreRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function rules()
    {
        return [
            'name' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('recruit::modules.skill.addSkills'),
        ];
    }
}
