<?php

namespace Modules\Recruit\Http\Requests\OfferLetter;

use App\Http\Requests\CoreRequest;
use Modules\Recruit\Entities\RecruitCustomQuestion;

class StoreAcceptRequest extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $data = [];

        if (request()->get('answer')) {
            $fields = request()->get('answer');

            foreach ($fields as $key => $value) {

                $customField = RecruitCustomQuestion::findOrFail($key);

                if ($customField->required == 'yes' && (is_null($value) || $value == '')) {
                    $data['answer['.$key.']'] = 'required';
                }
            }
        }

        return $data;

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

    public function messages()
    {
        return [
            //
        ];
    }

    public function attributes()
    {
        $attributes = [];

        if (request()->get('answer')) {
            $fields = request()->get('answer');

            foreach ($fields as $key => $value) {

                $customField = RecruitCustomQuestion::findOrFail($key);

                if ($customField->required == 'yes') {
                    $attributes['answer['.$key.']'] = $customField->question;
                }
            }
        }

        return $attributes;
    }
}
