<div class="form-group">
    <label>Title <span class="text-danger">*</span></label>
    <input type="text" name="title" class="form-control" value="{{ old('title', $banner->title ?? '') }}" required>
</div>
<div class="form-group">
    <label>Description</label>
    <textarea name="description" class="form-control" rows="3">{{ old('description', $banner->description ?? '') }}</textarea>
</div>
<div class="form-group">
    <label>Image URL</label>
    <input type="text" name="image" class="form-control" value="{{ old('image', $banner->image ?? '') }}">
</div>
<div class="form-group">
    <label>CTA Text</label>
    <input type="text" name="cta_text" class="form-control" value="{{ old('cta_text', $banner->cta_text ?? '') }}">
</div>
<div class="form-group">
    <label>CTA URL</label>
    <input type="url" name="cta_url" class="form-control" value="{{ old('cta_url', $banner->cta_url ?? '') }}">
</div>
<div class="form-group">
    <label>Role <span class="text-danger">*</span></label>
    <select name="role" class="form-control" required>
        @foreach(['all','admin','employee','client'] as $role)
        <option value="{{ $role }}" @selected(old('role', $banner->role ?? 'all') === $role)>{{ ucfirst($role) }}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label>Display Order <span class="text-danger">*</span></label>
    <input type="number" name="order" class="form-control" value="{{ old('order', $banner->order ?? 0) }}" min="0" required>
</div>
<div class="form-group">
    <div class="custom-control custom-switch">
        <input type="checkbox" class="custom-control-input" id="active" name="active" value="1"
               @checked(old('active', $banner->active ?? true))>
        <label class="custom-control-label" for="active">Active</label>
    </div>
</div>
