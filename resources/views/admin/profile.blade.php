@extends('layouts.master-2')
@section('content')
          <!-- Content -->

          <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Account Settings /</span> Account</h4>

            <div class="row">
              <div class="col-md-12">
                <div class="card mb-4">
                  <h5 class="card-header">Profile Details</h5>
                  <!-- Account -->
                  <hr class="my-0" />
                  <div class="card-body">
                      <div class="row">
                          <div class="col-lg-6 col-12">
                            <form id="" method="POST" action="{{ route('admin.update-profile-page', $admin->id) }}">
                              @csrf
                              @method('put')
                              <div class="row">
                                <div class="mb-3 col-md-12">
                                  <label for="firstName" class="form-label">Full Name</label>
                                  <input class="form-control" type="text"  name="name" value="{{ $admin->name }}" />
                                </div>
                                <div class="mb-3 col-md-12">
                                  <label for="email" class="form-label">E-mail</label>
                                  <input class="form-control" type="email" name="email" value="{{ $admin->email }}"/>
                                </div>
                                <div class="mb-3 col-md-12">
                                  <label class="form-label" for="phoneNumber">Phone Number</label>
                                  <div class="input-group input-group-merge">
                                    <input type="text"  name="phone" value="{{ $admin->phone }}" class="form-control" />
                                  </div>
                                </div>
                              </div>
                              <div class="mt-2">
                                <input type="submit" class="btn btn-primary me-2" value="Save Change">
                              </div>
                            </form>
                          </div>
                          <div class="col-lg-6 col-12">
                            {{-- <h2>Changes Password</h2> --}}
                            <form id="formAccountSettings" method="POST" action="{{ route('admin.change-password-page') }}">
                              @csrf
                              @method('put')
                              <div class="row">
                                <div class="mb-3 col-md-12">
                                  <label for="firstName" class="form-label">Current Password</label>
                                  <input class="form-control" type="text" name="current_password"/>
                                </div>
                                <div class="mb-3 col-md-12">
                                  <label for="email" class="form-label">New Password</label>
                                  <input class="form-control" type="text"  name="new_password" />
                                </div>
                                <div class="mb-3 col-md-12">
                                  <label for="email" class="form-label">Repeat New Password</label>
                                  <input class="form-control" type="text"  name="Confirm_password" placeholder="" />
                                </div>
                              </div>
                              <div class="mt-2">
                                <input type="submit" class="btn btn-primary me-2" value="Save Change">
                              </div>
                            </form>
                          </div>
                      </div>
                  </div>
                  <!-- /Account -->
                </div>
              </div>
            </div>
          </div>
          <!-- / Content -->

@endsection


          