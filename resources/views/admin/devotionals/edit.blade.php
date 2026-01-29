@extends('layouts.master-2')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" style="line-height: 36px;">Edit Devotional</h3>
                    <a href="{{ route('admin.devotionals.index') }}" class="btn btn-primary float-right">Back</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.devotionals.update', $devotional->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        @include('admin.devotionals.partials._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
