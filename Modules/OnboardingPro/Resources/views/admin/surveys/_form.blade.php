<div class="form-group">
    <label>Title <span class="text-danger">*</span></label>
    <input type="text" name="title" class="form-control" value="{{ old('title', $survey->title ?? '') }}" required>
</div>
<div class="form-group">
    <label>Description</label>
    <textarea name="description" class="form-control" rows="2">{{ old('description', $survey->description ?? '') }}</textarea>
</div>
<div class="form-group">
    <label>Role <span class="text-danger">*</span></label>
    <select name="role" class="form-control" required>
        @foreach(['all','admin','employee','client'] as $role)
        <option value="{{ $role }}" @selected(old('role', $survey->role ?? 'all') === $role)>{{ ucfirst($role) }}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label>Trigger <span class="text-danger">*</span></label>
    <select name="trigger" class="form-control" required>
        @foreach(['first_login','post_onboarding','milestone'] as $trigger)
        <option value="{{ $trigger }}" @selected(old('trigger', $survey->trigger ?? 'first_login') === $trigger)>{{ str_replace('_', ' ', ucfirst($trigger)) }}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <div class="custom-control custom-switch">
        <input type="checkbox" class="custom-control-input" id="active" name="active" value="1"
               @checked(old('active', $survey->active ?? true))>
        <label class="custom-control-label" for="active">Active</label>
    </div>
</div>

{{-- Questions builder --}}
<hr>
<h5>Questions</h5>
<div id="questions-container">
    @php $existing = old('questions', $survey->questions ?? []) @endphp
    @foreach($existing as $idx => $q)
    <div class="question-row card mb-3 p-3" data-index="{{ $idx }}">
        <div class="form-group mb-1">
            <label>Question Text</label>
            <input type="text" name="questions[{{ $idx }}][text]" class="form-control" value="{{ $q['text'] ?? '' }}" required>
        </div>
        <div class="form-group mb-1">
            <label>Type</label>
            <select name="questions[{{ $idx }}][type]" class="form-control q-type">
                @foreach(['text','radio','checkbox','scale'] as $t)
                <option value="{{ $t }}" @selected(($q['type'] ?? 'text') === $t)>{{ ucfirst($t) }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group mb-1 options-group" style="{{ in_array($q['type'] ?? '', ['radio','checkbox']) ? '' : 'display:none' }}">
            <label>Options (comma-separated)</label>
            <input type="text" name="questions[{{ $idx }}][options_raw]" class="form-control"
                   value="{{ implode(', ', $q['options'] ?? []) }}">
        </div>
        <div class="form-check mb-1">
            <input type="checkbox" class="form-check-input" name="questions[{{ $idx }}][required]" value="1"
                   @checked($q['required'] ?? false)>
            <label class="form-check-label">Required</label>
        </div>
        <button type="button" class="btn btn-sm btn-outline-danger mt-1 remove-question">Remove</button>
    </div>
    @endforeach
</div>
<button type="button" class="btn btn-outline-secondary mb-3" id="add-question">
    <i class="fa fa-plus"></i> Add Question
</button>

<script>
let qIdx = {{ count($existing ?? []) }};
document.getElementById('add-question').addEventListener('click', function() {
    const container = document.getElementById('questions-container');
    const html = `
    <div class="question-row card mb-3 p-3" data-index="${qIdx}">
        <div class="form-group mb-1">
            <label>Question Text</label>
            <input type="text" name="questions[${qIdx}][text]" class="form-control" required>
        </div>
        <div class="form-group mb-1">
            <label>Type</label>
            <select name="questions[${qIdx}][type]" class="form-control q-type">
                <option value="text">Text</option>
                <option value="radio">Radio</option>
                <option value="checkbox">Checkbox</option>
                <option value="scale">Scale</option>
            </select>
        </div>
        <div class="form-group mb-1 options-group" style="display:none">
            <label>Options (comma-separated)</label>
            <input type="text" name="questions[${qIdx}][options_raw]" class="form-control">
        </div>
        <div class="form-check mb-1">
            <input type="checkbox" class="form-check-input" name="questions[${qIdx}][required]" value="1">
            <label class="form-check-label">Required</label>
        </div>
        <button type="button" class="btn btn-sm btn-outline-danger mt-1 remove-question">Remove</button>
    </div>`;
    container.insertAdjacentHTML('beforeend', html);
    qIdx++;
    bindEvents();
});

function bindEvents() {
    document.querySelectorAll('.q-type').forEach(sel => {
        sel.addEventListener('change', function() {
            const row = this.closest('.question-row');
            const og = row.querySelector('.options-group');
            og.style.display = ['radio','checkbox'].includes(this.value) ? '' : 'none';
        });
    });
    document.querySelectorAll('.remove-question').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.question-row').remove();
        });
    });
}
bindEvents();
</script>
