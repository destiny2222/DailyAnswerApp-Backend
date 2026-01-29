@extends('layouts.master-2')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" style="line-height: 36px;">Memory Verse List</h3>
                    <a href="{{ route('admin.memory_verses.create') }}" class="btn btn-primary float-right">Create Memory Verse</a>
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
                                <th>Verse</th>
                                <th>Date</th>
                                <th>Notes</th>
                                <th width="20%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($memoryVerses as $memoryVerse)
                                <tr>
                                    <td>{{ Str::limit($memoryVerse->verse_text, 80) }}</td>
                                    <td>{{ optional($memoryVerse->date)->format('M d, Y') }}</td>
                                    <td>{!! Str::limit($memoryVerse->notes, 80) !!}</td>
                                    <td>
                                        <a href="{{ route('admin.memory_verses.show', $memoryVerse->id) }}" class="btn btn-sm btn-success">View</a>
                                        <a href="{{ route('admin.memory_verses.edit', $memoryVerse->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                        <form action="{{ route('admin.memory_verses.destroy', $memoryVerse->id) }}" method="POST" onsubmit="return confirm('Are you sure?');" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No memory verses found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{ $memoryVerses->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
