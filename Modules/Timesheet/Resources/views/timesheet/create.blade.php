{{ Form::open(array('url' => 'timesheet')) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
                <div class="d-flex radio-check">
                    @if(module_is_active('Hrm'))
                        <div class="form-check form-check-inline form-group col-md-4">
                            <input type="radio" id="clockin" value="clock in/clock out"
                                name="type" class="form-check-input code"
                                data-repeater-create="">
                            <label class="custom-control-label "
                                for="clockin">{{ __('Clock In/Clock Out Wise') }}</label>
                        </div>
                    @endif
                    @if(module_is_active('Taskly'))
                        <div class="form-check form-check-inline form-group col-lg-3">
                            <input type="radio" id="project" value="project"
                                name="type" class="form-check-input code" data-repeater-create="">
                            <label class="custom-control-label"
                                for="project">{{ __('Project Wise') }}</label>
                        </div>
                    @endif
                    <div class="form-check form-check-inline form-group col-lg-3">
                        <input type="radio" id="manually" value="manually"
                            name="type" class="form-check-input code" data-repeater-create="">
                        <label class="custom-control-label"
                            for="manually">{{ __('Manually') }}</label>
                    </div>
                </div>
        </div>
        @if(\Auth::user()->type == 'company')
        <div class="col-6">
            <div class="form-group">
                {{ Form::label('user_id',__('User'),['class'=>'col-form-label']) }}
                {{ Form::select('user_id', $user, null,array('class' => 'form-control user','placeholder' => 'Select User','required'=>'required')) }}
            </div>
        </div>
        @endif
        @if (module_is_active('Taskly'))
            <div class="col-md-6 project_div d-none">
                <div class="form-group">
                    {{ Form::label('project_id', __('Project'), ['class' => 'col-form-label']) }}
                    {{ Form::select('project_id', $project, null, ['class' => 'form-control get_tax multi-select','id' => 'project_id', 'placeholder' => 'Select project']) }}
                </div>
            </div>
            <div class="col-6 project_div d-none">
                <div class="form-group">
                    {{ Form::label('task_id',__('Task'),['class'=>'col-form-label']) }}
                    {{ Form::select('task_id', [], null,array('class' => 'form-control','placeholder' => 'Select Task')) }}
                </div>
            </div>
        @endif
        <div @if(\Auth::user()->type == 'company')class="col-6" @else class="col-4" @endif>
            <div class="form-group">
                {{ Form::label('date',__('Date'),['class'=>'col-form-label']) }}
                {{ Form::date('date', $date,array('class' => 'form-control date','placeholder' => 'Date','required'=>'required')) }}
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                {{ Form::label('hours',__('Hours'),['class'=>'col-form-label']) }}
                {{ Form::select('hours',[0], null, array('class' => 'form-control hours','required'=>'required')) }}
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                {{ Form::label('minutes',__('Minutes'),['class'=>'col-form-label']) }}
                {{ Form::select('minutes', [0],null, array('class' => 'form-control minutes','required'=>'required')) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('notes', __('Notes'),['class'=>'col-form-label']) }}
                {{ Form::textarea('notes', '', array('class' => 'form-control notes')) }}
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
    <button type="submit" class="btn  btn-primary">{{__('Create')}}</button>

</div>
{{ Form::close() }}

<script>
    $(document).ready(function () {
            $(".code").first().trigger('click');
        });
    $(document).on('click', '.code', function() {

        $(".hours").val(0);
        $(".minutes").val(0);
        $(".user").val("");
        $(".notes").val("");

    });

    $(document).on('change', "[name='type']", function() {
        var val = $(this).val();
        $('.date').trigger('change');
        if (val == 'project') {
            $(".project_div").removeClass('d-none');
        }
        else  {
            $(".project_div").addClass('d-none');
        }
    });
    $(document).on('change', 'select[name=user_id]', function() {

        var user_id = $(this).val();
        var date = $('#date').val();
        $.ajax({
            url: '{{ route('totalhours') }}',
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': jQuery('#token').val()
            },
            data: {
                'user_id': user_id,
                'date' : date,
            },
            cache: false,
            success: function(data) {

                if( $('#clockin').prop('checked') )
                {
                    const myArray = data.split(":");
                    $('#hours').empty();
                    for(var i = 0; i <= myArray[0]; i++){
                        $('#hours').append('<option>'+i+'</option>');
                    }
                    $('#minutes').empty();
                    for(var j = 0; j <= myArray[1]; j+=15){
                        $('#minutes').append('<option>'+j+'</option>');
                    }
                }
                else{
                    $('#hours').empty();
                    for(var i = 0; i <= 12; i++){
                        $('#hours').append('<option>'+i+'</option>');
                    }
                    $('#minutes').empty();
                    for(var j = 0; j < 60; j+=15){
                        $('#minutes').append('<option>'+j+'</option>');
                    }
                }
            }
        });
    });
    $(document).on('change', '.date', function() {

        var user_id = $('#user_id').val();
        var date = $(this).val();

        $.ajax({
            url: '{{ route('totalhours') }}',
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': jQuery('#token').val()
            },
            data: {
                'user_id': user_id,
                'date' : date,
            },
            cache: false,
            success: function(data) {
                if( $('#clockin').prop('checked') )
                {
                    const myArray = data.split(":");
                    $('#hours').empty();
                    for(var i = 0; i<= myArray[0]; i++){
                        $('#hours').append('<option>'+i+'</option>');
                    }
                    $('#minutes').empty();
                    for(var j = 0 ; j <= myArray[1]; j+=15){
                        $('#minutes').append('<option>'+j+'</option>');
                    }
                }
                else{
                    $('#hours').empty();
                    for(var i = 0; i <= 12; i++){
                        $('#hours').append('<option>'+i+'</option>');
                    }
                    $('#minutes').empty();
                    for(var j = 0; j < 60; j+=15){
                        $('#minutes').append('<option>'+j+'</option>');
                    }
                }
            }
        });
    });

    $(document).ready(function(){

        var user_id = $('#user_id').val();
        var date = '{{$date}}';

        $.ajax({
            url: '{{ route('totalhours') }}',
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': jQuery('#token').val()
            },
            data: {
                'user_id': user_id,
                'date' : date,
            },
            cache: false,
            success: function(data) {
                if( $('#clockin').prop('checked') )
                {
                    const myArray = data.split(":");
                    $('#hours').empty();
                    for(var i = 0; i<= myArray[0]; i++){
                        $('#hours').append('<option>'+i+'</option>');
                    }
                    $('#minutes').empty();
                    for(var j = 0 ; j <= myArray[1]; j+=15){
                        $('#minutes').append('<option>'+j+'</option>');
                    }
                }
                else{
                    $('#hours').empty();
                    for(var i = 0; i <= 12; i++){
                        $('#hours').append('<option>'+i+'</option>');
                    }
                    $('#minutes').empty();
                    for(var j = 0; j < 60; j+=15){
                        $('#minutes').append('<option>'+j+'</option>');
                    }
                }
            }
        });
    });

    @if(module_is_active('Taskly'))
        $(document).on('change', 'select[name=project_id]', function() {
            var project_id = $(this).val();
            gettask(project_id);
        });

        function gettask(did) {
        $.ajax({
            url: '{{ route('gettask') }}',
            type: 'POST',
            data: {
                "project_id": did,
                "_token": "{{ csrf_token() }}",
            },
            success: function(data) {
                $('#task_id').empty();
                $('#task_id').append(
                    '<option value="">{{ __('Select Project') }}</option>');
                $.each(data, function(key, value) {
                    $('#task_id').append('<option value="' + key + '">' + value +
                        '</option>');
                });
            }
        });
        }
    @endif

</script>
