@extends('layouts.master-2')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" style="line-height: 36px;">Create Devotional</h3>
                    <a href="{{ route('admin.devotionals.index') }}" class="btn btn-primary float-right">Back</a>
                </div>
                {{-- check error message --}}
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
                    <form action="{{ route('admin.devotionals.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @include('admin.devotionals.partials._form')
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
        .create(document.querySelector('#content'))
        .catch(error => {
            console.error(error);
        });
</script>
<script>
    ClassicEditor
        .create( document.querySelector( '#prayer_note' ) )
        .catch( error => {
            console.error( error );
        } );
</script>
<script>
    ClassicEditor
        .create( document.querySelector( '#application_note' ) )
        .catch( error => {
            console.error( error );
        } );
</script>
<script>
    ClassicEditor
        .create( document.querySelector( '#verses' ) )
        .catch( error => {
            console.error( error );
        } );
</script>
@endpush