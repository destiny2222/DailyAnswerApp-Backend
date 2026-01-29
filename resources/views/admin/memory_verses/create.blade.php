@extends('layouts.master-2')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" style="line-height: 36px;">Create Memory Verse</h3>
                    <a href="{{ route('admin.memory_verses.index') }}" class="btn btn-primary float-right">Back</a>
                </div>
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="card-body">
                    <form action="{{ route('admin.memory_verses.store') }}" method="POST">
                        @csrf
                        @include('admin.memory_verses.partials._form', ['buttonText' => 'Create'])
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    ClassicEditor
        .create( document.querySelector( '.ckeditor' ) )
        .catch( error => {
            console.error( error );
        } );
</script>
@endpush
