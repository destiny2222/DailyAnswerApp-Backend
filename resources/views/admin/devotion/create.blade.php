@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" style="line-height: 36px;">Create Devotional</h3>
                    <a href="{{ route('admin.devotionals.index') }}" class="btn btn-primary float-right">Back</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.devotionals.store') }}" method="POST">
                        @csrf
                        @include('admin.devotion.partials._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
    
</div>
@endsection
