@extends('layouts.master-2')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" style="line-height: 36px;">Memory Verse Details</h3>
                    <a href="{{ route('admin.memory_verses.index') }}" class="btn btn-primary float-right">Back</a>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th width="20%">Verse Text</th>
                                <td>{{ $memoryVerse->verse_text }}</td>
                            </tr>
                            <tr>
                                <th>Date</th>
                                <td>{{ optional($memoryVerse->date)->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <th>Notes</th>
                                <td>{{ $memoryVerse->notes ?? 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
