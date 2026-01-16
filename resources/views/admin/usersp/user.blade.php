@extends('layouts.master-2')
@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Grid Card -->
        <div class="row py-4">
            <div class="col-xl">
                <h6 class="pb-1 mb-4 text-muted">Users Table</h6>
            </div>
            <div class="col-xl text-end">
                <a href="{{ route('admin.register.form') }}" class="btn btn-primary">
                    <span class="tf-icons bx bx-add-to-queue"></span>&nbsp; Add New User
                </a>
            </div>
        </div>


        <div class="row">
            <!-- Striped Rows -->
            <div class="card">
                <h5 class="card-header">Users</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table ">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone Number</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @if (count($users) != 0)
                            @forelse ($users as $usering)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>
                                    <ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center">
                                        <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" class="avatar avatar-xs pull-up" title="" data-bs-original-title="{{ $usering->name }}">
                                            <img src="{{ asset('profile/'.$usering->profile_picture) }}" alt="Avatar" class="rounded-circle">
                                        </li>
                                    </ul>
                                </td>
                                <td>
                                    {{ $usering->name  }}
                                </td>
                                <td>
                                    {{ $usering->email  }}
                                </td>
                                <td>
                                    {{ $usering->phone ?? 'N/A' }}
                                </td>
                                <td>
                                    <div class="d-flex gap-3">
                                        <a class="btn btn-primary btn-sm" href="{{ route('admin.customer.edit',$usering->id) }}">
                                            <i class="bx bx-edit-alt "></i>
                                            Edit
                                        </a>
                                        <a href="{{ route('admin.customer-delete', $usering->id) }}" class="btn btn-danger btn-sm" 
                                            onclick="event.preventDefault(); document.getElementById('delete-form-{{ $usering->id }}').submit(); return confirm('Are you sure?');">
                                            <i class="bx bxs-trash me-1"></i>
                                            Delete
                                        </a>
                                        <form action="{{ route('admin.customer-delete', $usering->id) }}" method="POST" id="delete-form-{{ $usering->id }}">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
    
                            @endforelse
    
                            @endif
    
                        </tbody>
    
                    </table>
                </div>
            </div>
            <!--/ Striped Rows -->
        </div>
    
        <div class="row pt-5" aria-label="Page navigation">
            <div class="pagination justify-content-end">
                 {!! $users->withQueryString()->links('pagination::bootstrap-5') !!}
            </div>
        </div>



    </div>

    

@endsection
<script>
    ClassicEditor
        .create(document.getElementById('summary-body'))
        .catch(error => {
            console.error(error);
        });
</script>
