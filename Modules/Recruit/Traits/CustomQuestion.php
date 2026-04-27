<?php

namespace Modules\Recruit\Traits;

use Illuminate\Support\Facades\Config;
use Modules\Recruit\Entities\RecruitCustomQuestion;

trait CustomQuestion
{
    public function fetchQuestion($selectedQuestions)
    {
        $fields = [];

        if (is_null($selectedQuestions)) {
            return $fields;
        }

        foreach ($selectedQuestions as $group) {
            $customFields = RecruitCustomQuestion::where('id', $group->recruit_custom_question_id)->get();
            $customFields = collect($customFields);
            $customFields = $customFields->map(function ($item) {
                if ($item->type == 'select' || $item->type == 'radio' || $item->type == 'checkbox') {
                    $item->values = json_decode($item->values);

                    return $item;
                }

                return $item;
            });

            $fields[] = $customFields;
        }

        return $fields;
    }
}
