@extends('layouts.master-2')
@section('content')
    <div class="row">
        <div class="col-xl">
            <h6 class="pb-1 mb-4 text-muted">Edit User</h6>
        </div>
        <div class="col-xl text-end">
            <a href="{{ route('admin.customer.index') }}" class="btn btn-primary">
                <span class="tf-icons bx bx-add-to-queue"></span>&nbsp; Return Back
            </a>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-10 pt-5 ">
            <div class="card p-3  m-auto">
                <form action="{{ route('admin.customer.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('put')
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label" for="basic-default-fullname">{{ __('Name') }}</label>
                            <input type="text" value="{{ $user->name }}" name="name"
                                class="form-control @error('name') is-invalid @enderror" id="basic-default-fullname"
                                placeholder="Full name" />
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="basic-default-fullname">{{ __('Email') }}</label>
                            <input type="text" name="email" value="{{ $user->email }}"
                                class="form-control @error('email') is-invalid @enderror" id="basic-default-fullname"
                                placeholder="Email  address" />
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label" for="basic-default-fullname">{{ __('Username') }}</label>
                            <input type="text" value="{{ $user->username }}" name="username"
                                class="form-control @error('username') is-invalid @enderror" id="basic-default-fullname"
                                placeholder="Username" />
                            @error('username')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label" for="profile_picture">{{ __('Profile Picture') }}</label>
                            <input type="file" value="{{ $user->profile_picture }}" name="profile_picture"
                                class="form-control @error('profile_picture') is-invalid @enderror" id="profile_picture"
                                accept="image/*" />
                            @error('profile_picture')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                        <div class="col-12 mb-3">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_staff" name="is_staff" value="1" {{ $user->is_staff ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="is_staff">Is Staff?</label>
                                <small class="d-block text-muted">Staff members get free access to devotionals.</small>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-0 text-center">
                            <input type="submit" class="btn btn-primary btn-lg w-100" value="Save Change">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
