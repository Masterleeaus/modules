<?php

namespace Modules\Recruit\Http\Controllers;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Modules\Recruit\Entities\RecruitCustomQuestion;
use Modules\Recruit\Entities\RecruitSetting;
use Modules\Recruit\Http\Requests\RecruitSetting\StoreCustomQuestionRequest;

class RecruitCustomQuestionController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->activeSettingMenu = 'recruit_settings';
        $this->middleware(function ($request, $next) {
            abort_403(! in_array(RecruitSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Renderable
     */
    public function create()
    {
        $this->types = ['text', 'number', 'password', 'textarea', 'select', 'radio', 'date', 'checkbox', 'file'];

        return view('recruit::recruit-setting.custom-question.create-question-modal', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Renderable
     */
    public function store(StoreCustomQuestionRequest $request)
    {
        $group = [
            'fields' => [
                [
                    'category' => $request->category,
                    'question' => $request->question,
                    'status' => $request->status,
                    'type' => $request->get('type'),
                    'required' => $request->get('required'),
                    'values' => $request->get('value'),
                ],
            ],

        ];

        $this->addCustomField($group);

        return Reply::success('recruit::messages.customQuestionCreated');
    }

    private function addCustomField($group)
    {
        // Add Custom Fields for this group
        foreach ($group['fields'] as $field) {
            $insertData = [
                'category' => $field['category'],
                'question' => $field['question'],
                'status' => $field['status'],
                'type' => $field['type'],
            ];

            if (isset($field['required']) && (in_array(strtolower($field['required']), ['yes', 'on', 1]))) {
                $insertData['required'] = 'yes';

            } else {
                $insertData['required'] = 'no';
            }

            // Single value should be stored as text (multi value JSON encoded)
            if (isset($field['values'])) {
                if (is_array($field['values'])) {
                    $insertData['values'] = \GuzzleHttp\json_encode($field['values']);

                } else {
                    $insertData['values'] = $field['values'];
                }
            }

            RecruitCustomQuestion::create($insertData);

        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function edit($id)
    {
        $this->question = RecruitCustomQuestion::findOrfail($id);
        $this->types = ['text', 'number', 'password', 'textarea', 'select', 'radio', 'date', 'checkbox', 'file'];
        $this->question->values = json_decode($this->question->values);

        return view('recruit::recruit-setting.custom-question.edit-question-modal', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Renderable
     */
    public function update(StoreCustomQuestionRequest $request, $id)
    {
        $question = RecruitCustomQuestion::findOrFail($id);
        $question->category = $question->category;
        $question->question = $request->question;
        $question->status = $request->status;
        $question->required = $request->required;
        $question->save();

        return Reply::success(__('messages.updateSuccess'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function destroy($id)
    {
        RecruitCustomQuestion::destroy($id);

        return Reply::success(__('recruit::messages.CustomDeleted'));
    }

    public function changeQuestionStatus(Request $request)
    {
        $question = RecruitCustomQuestion::findOrFail($request->questionId);
        $question->status = lcfirst($request->status);
        $question->update();

        return Reply::success(__('messages.updateSuccess'));
    }
}
