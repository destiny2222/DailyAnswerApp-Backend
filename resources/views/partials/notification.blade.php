{{-- @php
$notification = get_notify_details();
@endphp
 <!-- Start::header-link|dropdown-toggle -->
                <a href="javascript:void(0);" class="header-link dropdown-toggle" data-bs-toggle="dropdown"
                    data-bs-auto-close="outside" id="messageDropdown" aria-expanded="false">
                    <i class="bx bx-bell header-link-icon"></i>
                    <span class="badge bg-secondary rounded-pill header-icon-badge pulse pulse-secondary"
                        id="notification-icon-badge">5</span>
                </a>
                <!-- End::header-link|dropdown-toggle -->
                <!-- Start::main-header-dropdown -->


<div class="main-header-dropdown dropdown-menu dropdown-menu-end" data-popper-placement="none">
    <div class="p-3">
        <div class="d-flex align-items-center justify-content-between">
            <p class="mb-0 fs-17 fw-semibold">Notifications</p>
            <span class="badge bg-secondary-transparent" id="notifiation-data">5 Unread</span>
        </div>
    </div>
    <div class="dropdown-divider"></div>
    <ul class="list-unstyled mb-0" id="header-notification-scroll">
        @foreach ($notification as $notifications)
        <li class="dropdown-item">
            <div class="d-flex align-items-start">
                <div class="pe-2">
                    <span class="avatar avatar-md bg-primary-transparent avatar-rounded"><i
                            class="ti ti-gift fs-18"></i></span>
                </div>
                <div class="flex-grow-1 d-flex align-items-center justify-content-between">
                    <div>
                        <p class="mb-0 fw-semibold">
                            <a href="notifications.html">
                               {{ $notifications['data']['user_name'] }}
                            </a>
                        </p>
                        <span class="text-muted fw-normal fs-12 header-notification-text">Order
                            No: 123456 Has Shipped To Your Delivery
                            Address
                        </span>
                    </div>
                    <div>
                        <a href="{{ route('markAsRead', $notifications->id) }}"
                            class="min-w-fit-content text-muted me-1 dropdown-item-close1">
                            <i class="ti ti-x fs-16"></i>
                        </a>
                    </div>
                </div>
            </div>
        </li>
        @endforeach
    </ul>
    <div class="p-3 empty-header-item1 border-top">
        <div class="d-grid">
            <a href="notifications.html" class="btn btn-primary">View All</a>
        </div>
    </div>
    <div class="p-5 empty-item1 d-none">
        <div class="text-center">
            <span class="avatar avatar-xl avatar-rounded bg-secondary-transparent">
                <i class="ri-notification-off-line fs-2"></i>
            </span>
            <h6 class="fw-semibold mt-3">No New Notifications</h6>
        </div>
    </div>
</div> --}}