@extends('layouts.master-2')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" style="line-height: 36px;">Create Role</h3>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-primary float-right">Back</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.roles.store') }}" method="POST">
                        @csrf
                        @include('admin.roles._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
