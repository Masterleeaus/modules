<?php

namespace Modules\Recruit\Http\Requests\FooterLinks;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFooterLinks extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'slug' => 'required|unique:recruit_footer_links,slug,'.$this->route('footer_link'),
            'title' => 'required',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
