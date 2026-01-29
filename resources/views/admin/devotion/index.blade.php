@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" style="line-height: 36px;">Devotional List</h3>
                    <a href="{{ route('admin.devotionals.create') }}" class="btn btn-primary float-right">Create Devotional</a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Date</th>
                                <th>Author</th>
                                <th>Status</th>
                                <th width="20%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($devotionals as $devotional)
                                <tr>
                                    <td>{{ $devotional->title }}</td>
                                    <td>{{ $devotional->date->format('M d, Y') }}</td>
                                    <td>{{ $devotional->creator->name }}</td>
                                    <td><span class="badge badge-{{ $devotional->status_color }}">{{ ucfirst($devotional->status) }}</span></td>
                                    <td>
                                        <a href="{{ route('admin.devotionals.show', $devotional->id) }}" class="btn btn-sm btn-success">View</a>
                                        @can('edit', $devotional)
                                            <a href="{{ route('admin.devotionals.edit', $devotional->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                        @endcan
                                        @can('delete', $devotional)
                                            <form action="{{ route('admin.devotionals.destroy', $devotional->id) }}" method="POST" onsubmit="return confirm('Are you sure?');" style="display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No devotionals found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{ $devotionals->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
