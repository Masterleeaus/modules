@foreach ($fields as $q)
    @foreach ($q as $question)

        @if ($question->type == 'text')
            <x-forms.text
                fieldId="answer[{{ $question->id }}]"
                :fieldLabel="($question->question)"
                fieldName="answer[{{ $question->id }}]"
                :fieldPlaceholder="__('recruit::modules.setting.enterTextHere')"
                :fieldRequired="($question->required == 'yes') ? 'true' : 'false'"
                :fieldValue="$values->where('recruit_job_question_id', $question->id)->first()->answer ?? ''">
            </x-forms.text>

        @elseif($question->type == 'password')
            <x-forms.password
                fieldId="answer[{{ $question->id }}]"
                :fieldLabel="($question->question)"
                fieldName="answer[{{ $question->id }}]"
                :fieldPlaceholder="__('recruit::modules.setting.enterPasswordHere')"
                :fieldRequired="($question->required === 'yes') ? true : false"
                :fieldValue="$values->where('recruit_job_question_id', $question->id)->first()->answer ?? ''">
            </x-forms.password>

        @elseif($question->type == 'number')
            <x-forms.number
                fieldId="answer[{{ $question->id }}]"
                :fieldLabel="($question->question)"
                fieldName="answer[{{ $question->id }}]"
                :fieldPlaceholder="__('recruit::modules.setting.enterNumberHere')"
                :fieldRequired="($question->required === 'yes') ? true : false"
                :fieldValue="$values->where('recruit_job_question_id', $question->id)->first()->answer ?? ''">
            </x-forms.number>

        @elseif($question->type == 'textarea')
            <x-forms.textarea :fieldLabel="($question->question)"
                            fieldName="answer[{{ $question->id }}]"
                            fieldId="answer[{{ $question->id }}]"
                            :fieldRequired="($question->required === 'yes') ? true : false"
                            :fieldPlaceholder="__('recruit::modules.setting.enterTextHere')"
                            :fieldValue="$values->where('recruit_job_question_id', $question->id)->first()->answer ?? ''">
            </x-forms.textarea>

        @elseif($question->type == 'radio')
            <div class="form-group">
                <x-forms.label
                    fieldId="answer[{{ $question->id }}]"
                    :fieldLabel="($question->question)"
                    :fieldRequired="($question->required === 'yes') ? true : false">
                </x-forms.label>
                <div class="d-flex">
                    <input type="hidden" name="answer[{{ $question->id }}]"
                        id="{{$question->field_name.'_'.$question->id}}"/>
                    @foreach ($question->values as $key => $value)
                        <x-forms.radio
                            fieldId="optionsRadios{{ $key . $question->id }}"
                            :fieldLabel="$value"
                            fieldName="answer[{{ $question->id }}]"
                            :fieldValue="$value"
                            :checked="optional($values->where('recruit_job_question_id', $question->id)->first())->answer == $value ? true : false"
                        />
                    @endforeach
                </div>
            </div>

        @elseif($question->type == 'select')
            <div class="form-group">
                <x-forms.label
                    fieldId="answer[{{ $question->id }}]"
                    :fieldLabel="($question->question)"
                    :fieldRequired="($question->required === 'yes') ? true : false">
                </x-forms.label>
                {!! Form::select('answer[' . $question->id . ']', $question->values, $values->where('recruit_job_question_id', $question->id)->first()->answer ?? '', ['class' => 'form-control select-picker']) !!}
            </div>

        @elseif($question->type == 'date')
            <x-forms.datepicker custom="true"
                            fieldId="answer[{{ $question->id }}]"
                            :fieldRequired="($question->required === 'yes') ? true : false"
                            :fieldLabel="($question->question)"
                            fieldName="answer[{{ $question->id }}]"
                            :fieldPlaceholder="__('recruit::modules.setting.selectDateHere')"
                            :fieldValue="$values->where('recruit_job_question_id', $question->id)->first()->answer ?? ''">
            </x-forms.datepicker>

        @elseif($question->type == 'checkbox')
            <div class="form-group">
                <x-forms.label
                    fieldId="answer[{{ $question->id }}]"
                    :fieldLabel="($question->question)"
                    :fieldRequired="($question->required === 'yes') ? true : false">
                </x-forms.label>
                <div class="d-flex checkbox-{{$question->id}}">
                    @php
                        $checkedValues = '';
                        $questionValues = is_array($question->values) ? $question->values : json_decode($question->values, true);

                        $answerObj = $values->where('recruit_job_question_id', $question->id)->first();
                        $answer = $answerObj ? $answerObj->answer : '';

                        foreach ($questionValues as $key => $value) {
                            if ($answer != '' && in_array($value, explode(', ', $answer))) {
                                $checkedValues .= ($checkedValues == '') ? $value : ', '.$value;
                            }
                        }
                    @endphp

                    <input type="hidden" name="answer[{{$question->id}}]"
                        id="{{$question->id}}"
                        value="{{ $checkedValues }}">

                    @foreach ($question->values as $key => $value)
                        @php
                            $answerObj = $values->where('recruit_job_question_id', $question->id)->first();
                            $answer = $answerObj ? $answerObj->answer : '';
                            $isChecked = $answer != '' && in_array($value, explode(', ', $answer));
                        @endphp
                        <x-forms.checkbox fieldId="optionsRadios{{ $key . $question->id }}"
                                        :fieldLabel="$value"
                                        :fieldName="$question->id.'[]'"
                                        :fieldValue="$value"
                                        :checked="$isChecked"
                                        :fieldRequired="($question->required === 'yes') ? true : false"
                                        onchange="checkboxChange('checkbox-{{$question->id}}', '{{$question->id}}')"
                        />
                    @endforeach
                </div>
            </div>

        @elseif($question->type == 'file')
            <div class="form-group">
                <x-forms.label class="" :fieldRequired="($question->required === 'yes') ? true : false" fieldId="$question->question"
                            :fieldLabel="($question->question)"
                ></x-forms.label>
                <div class="form-group custom-file">
                    <input type="file" class="custom-file-input" name="answer[{{ $question->id }}]"
                        accept="image/jpeg, image/jpg, image/png"
                        value="$values->where('recruit_job_question_id', $question->id)->first()->answer ?? ''">
                    <x-forms.label fieldId="answer[{{ $question->id }}]" fieldRequired="false"
                                :fieldLabel="__('app.dragDrop')"
                                class="custom-file-label"></x-forms.label>
                </div>
            </div>
        @endif

    @endforeach
@endforeach
