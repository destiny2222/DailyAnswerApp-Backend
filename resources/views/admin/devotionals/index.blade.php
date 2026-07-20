@extends('layouts.master-2')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title" style="line-height: 36px;">Devotional List</h3>
                        <div class="float-end">
                            <a href="{{ route('admin.devotionals.bulk-create') }}" class="btn btn-success me-2">
                                <i class="ti ti-circle-plus"></i> Bulk Create
                            </a>
                            <a href="{{ route('admin.devotionals.create') }}" class="btn btn-primary">
                                <i class="ti ti-plus"></i> Create Single
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Filter Form -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <form action="{{ route('admin.devotionals.index') }}" method="GET">
                                    <div class="input-group">
                                        <select name="status" class="form-control">
                                            <option value="">All Status</option>
                                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>
                                                Draft</option>
                                            <option value="in_review"
                                                {{ request('status') == 'in_review' ? 'selected' : '' }}>In Review</option>
                                            <option value="published"
                                                {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                                        </select>
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary">Filter</button>
                                            <a href="{{ route('admin.devotionals.index') }}"
                                                class="btn btn-secondary">Reset</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- Bulk Action Form -->
                        <form id="bulk-delete-form" action="{{ route('admin.devotionals.bulk-delete') }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to delete the selected items?');">
                            @csrf
                            @method('DELETE')

                            <div class="mb-3 d-flex align-items-center">
                                <button type="submit" class="btn btn-danger btn-sm" id="bulk-delete-btn" disabled>
                                    <i class="ti ti-trash"></i> Delete Selected
                                </button>
                                <span class="ms-2 text-muted" id="selected-count" style="display: none;">
                                    <span class="count">0</span> items selected
                                </span>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="40px">
                                                <input type="checkbox" id="select-all">
                                            </th>
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
                                                <td>
                                                    <input type="checkbox" name="ids[]" value="{{ $devotional->id }}"
                                                        class="devotional-checkbox">
                                                </td>
                                                <td>{{ $devotional->title }}</td>
                                                <td>{{ optional($devotional->date)->format('M d, Y') }}</td>
                                                <td>{{ $devotional->creator->name }}</td>
                                                <td><span
                                                        class="badge bg-{{ $devotional->status_color }}">{{ ucfirst($devotional->status) }}</span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.devotionals.show', $devotional->id) }}"
                                                        class="btn btn-sm btn-success">View</a>
                                                    @can('devotionals.edit own', $devotional)
                                                        <a href="{{ route('admin.devotionals.edit', $devotional->id) }}"
                                                            class="btn btn-sm btn-primary">Edit</a>
                                                    @endcan
                                                    @can('devotionals.delete', $devotional)
                                                        <form
                                                            action="{{ route('admin.devotionals.destroy', $devotional->id) }}"
                                                            method="POST" onsubmit="return confirm('Are you sure?');"
                                                            style="display: inline-block;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                        </form>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No devotionals found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </form>
                        <div class="mt-3">
                            <div class="row pt-4">
                                <div class="col-12 d-flex justify-content-end">
                                    {!! $devotionals->withQueryString()->links('pagination::bootstrap-5') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const selectAll = $('#select-all');
            const deleteBtn = $('#bulk-delete-btn');
            const selectedCount = $('#selected-count');
            const countSpan = selectedCount.find('.count');

            function updateUI() {
                const checkboxes = $('.devotional-checkbox');
                const checkedCheckboxes = $('.devotional-checkbox:checked');
                const checkedCount = checkedCheckboxes.length;

                countSpan.text(checkedCount);

                if (checkedCount > 0) {
                    deleteBtn.prop('disabled', false);
                    selectedCount.show();
                } else {
                    deleteBtn.prop('disabled', true);
                    selectedCount.hide();
                }

                selectAll.prop('checked', checkboxes.length > 0 && checkedCount === checkboxes.length);
            }

            // Select all functionality
            $(document).on('change', '#select-all', function() {
                $('.devotional-checkbox').prop('checked', $(this).prop('checked'));
                updateUI();
            });

            // Individual checkbox functionality using event delegation
            $(document).on('change', '.devotional-checkbox', function() {
                updateUI();
            });

            // Initialize UI on load
            updateUI();
        });
    </script>
@endpush
