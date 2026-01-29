@extends('layouts.master-2')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" style="line-height: 36px;">Devotional Details</h3>
                    <a href="{{ route('admin.devotionals.index') }}" class="btn btn-primary float-right">Back</a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Title:</strong> {{ $devotional->title }}</p>
                            <p><strong>Date:</strong> {{ $devotional->date->format('M d, Y') }}</p>
                            <p><strong>Author:</strong> {{ $devotional->creator->name }}</p>
                            <p><strong>Status:</strong> <span class="badge badge-{{ $devotional->status_color }}">{{ ucfirst($devotional->status) }}</span></p>
                            @if($devotional->isPublished())
                                <p><strong>Published By:</strong> {{ $devotional->publisher->name }}</p>
                                <p><strong>Published At:</strong> {{ $devotional->published_at->format('M d, Y H:i A') }}</p>
                            @endif
                        </div>
                    </div>
                    <hr>
                    <h4>Text</h4>
                    <p>{{ $devotional->text }}</p>
                    <hr>
                    <h4>Content</h4>
                    <div>{!! $devotional->content !!}</div>
                </div>
                <div class="card-footer">
                    @can('edit', $devotional)
                        <a href="{{ route('admin.devotionals.edit', $devotional) }}" class="btn btn-primary">Edit</a>
                    @endcan
                    @can('publish', $devotional)
                        @if(!$devotional->isPublished())
                            <form action="{{ route('admin.devotionals.publish', $devotional) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success">Publish</button>
                            </form>
                        @else
                            <form action="{{ route('admin.devotionals.unpublish', $devotional) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-warning">Unpublish</button>
                            </form>
                        @endif
                    @endcan
                    @can('delete', $devotional)
                        <form action="{{ route('admin.devotionals.destroy', $devotional) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this devotional?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
