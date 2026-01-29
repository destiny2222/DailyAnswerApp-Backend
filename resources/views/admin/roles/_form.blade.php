<div class="form-group">
    <label for="name">Name</label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $role->name ?? '') }}" required>
    @error('name')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>

<div class="form-group">
    <label class="py-3">Permissions</label>
    <div class="row">
        @foreach($permissions as $permission)
            <div class="col-md-3">
                <div class="custom-control custom-checkbox">
                    <input class="custom-control-input" type="checkbox" id="permission_{{ $permission->id }}" name="permissions[]" value="{{ $permission->id }}"
                        @if(isset($role) && $role->permissions->contains($permission->id)) checked @endif>
                    <label for="permission_{{ $permission->id }}" class="custom-control-label">{{ $permission->name }}</label>
                </div>
            </div>
        @endforeach
    </div>
</div>

<button type="submit" class="btn btn-primary mt-5">Save</button>
