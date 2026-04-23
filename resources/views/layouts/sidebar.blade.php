<aside class="left-sidebar with-vertical">
      <div><!-- ---------------------------------- -->
        <!-- Start Vertical Layout Sidebar -->
        <!-- ---------------------------------- -->
        <div class="brand-logo d-flex align-items-center justify-content-between">
          <a href="{{ route('admin.home') }}" class="text-nowrap logo-img">
            <img src="/icon.png" class="dark-logo" style="width: 30%;" alt="Logo-Dark">
            <img src="/icon.png" class="light-logo" style="width: 30%;" alt="Logo-light">
          </a>
          <a href="javascript:void(0)" class="sidebartoggler ms-auto text-decoration-none fs-5 d-block d-xl-none">
            <i class="ti ti-x"></i>
          </a>
        </div>


        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
          <ul id="sidebarnav">
            <!-- ---------------------------------- -->
            <!-- Home -->
            <!-- ---------------------------------- -->
            <li class="nav-small-cap">
              <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
              <span class="hide-menu">Apps</span>
            </li>
            <!-- ---------------------------------- -->
            <!-- Dashboard -->
            <!-- ---------------------------------- -->
            
            <li class="sidebar-item">
              <a class="{{ request()->routeIs('admin.home') ? 'sidebar-link active' : 'sidebar-link' }}" href="{{ route('admin.home') }}" aria-expanded="false">
                <span>
                  <i class="ti ti-shopping-cart"></i>
                </span>
                <span class="hide-menu">Analytics</span>
              </a>
            </li>
             @canany(['admins.view', 'admins.create', 'admins.edit', 'admins.delete', 'admins.assign roles'], 'admin')
            <li class="sidebar-item">
              <a class="{{ request()->routeIs('admin.admins.*') ? 'sidebar-link active' : 'sidebar-link' }}" 
                href="{{ route('admin.admins.index') }}"  aria-expanded="false">
                <span> 
                  <i class="ti ti-user-shield"></i>
                </span>
                <span class="hide-menu">Admin Users</span>
              </a>
            </li>
            @endcanany

            @canany(['users.view', 'users.create', 'users.edit', 'users.delete'], 'admin')
            <li class="sidebar-item">
              <a class="{{ request()->routeIs('admin.customer.*') ? 'sidebar-link active' : 'sidebar-link' }}" 
                href="{{ route('admin.customer.index') }}"  aria-expanded="false">
                <span> 
                  <i class="ti ti-users"></i>
                </span>
                <span class="hide-menu">User Management</span>
              </a>
            </li>
            @endcanany
            
            <li class="sidebar-item">
              <a class="{{ request()->routeIs('admin.subscription.*') ? 'sidebar-link active' : 'sidebar-link' }}" 
                href="{{ route('admin.subscription.index') }}"  aria-expanded="false">
                <span> 
                  <i class="ti ti-users"></i>
                </span>
                <span class="hide-menu">Subscription  Management</span>
              </a>
            </li>
            
            @canany(['devotionals.view', 'devotionals.view own'], 'admin')
            <li class="sidebar-item">
              <a class="sidebar-link has-arrow" href="#devotionalMenu" data-bs-toggle="collapse" aria-expanded="false">
                <span class="d-flex">
                  <i class="ti ti-book"></i>
                </span>
                <span class="hide-menu">Devotionals</span>
              </a>
              <ul id="devotionalMenu" aria-expanded="false" class="collapse first-level">
                <li class="sidebar-item">
                  <a href="{{ route('admin.devotionals.index') }}" class="sidebar-link {{ request()->routeIs('admin.devotionals.index') ? 'active' : '' }}">
                    <div class="round-16 d-flex align-items-center justify-content-center">
                      <i class="ti ti-circle"></i>
                    </div>
                    <span class="hide-menu">All Devotionals</span>
                  </a>
                </li>
                @can('create devotionals', 'admin')
                <li class="sidebar-item">
                  <a href="{{ route('admin.devotionals.create') }}" class="sidebar-link {{ request()->routeIs('admin.devotionals.create') ? 'active' : '' }}">
                    <div class="round-16 d-flex align-items-center justify-content-center">
                      <i class="ti ti-circle"></i>
                    </div>
                    <span class="hide-menu">Create New</span>
                  </a>
                </li>
                @endcan
              </ul>
            </li>
            @endcanany
           
            @canany(['roles.view', 'roles.create', 'roles.edit', 'roles.delete'], 'admin')
            <li class="sidebar-item">
              <a class="{{ request()->routeIs('admin.roles.*') ? 'sidebar-link active' : 'sidebar-link' }}" 
                href="{{ route('admin.roles.index') }}"  aria-expanded="false">
                <span> 
                  <i class="ti ti-lock"></i>
                </span>
                <span class="hide-menu">Roles & Permissions</span>
              </a>
            </li>
            @endcanany

            @canany(['memory verses.view', 'memory verses.create', 'memory verses.edit', 'memory verses.delete'], 'admin')
            <li class="sidebar-item">
              <a class="{{ request()->routeIs('admin.memory_verses.*') ? 'sidebar-link active' : 'sidebar-link' }}" 
                href="{{ route('admin.memory_verses.index') }}"  aria-expanded="false">
                <span> 
                  <i class="ti ti-bookmark"></i>
                </span>
                <span class="hide-menu">Memory Verses</span>
              </a>
            </li>
            @endcanany
          </ul>
        </nav>

        <div class="fixed-profile p-3 mx-4 mb-2 bg-secondary-subtle rounded mt-3">
          <div class="hstack gap-3">
            <div class="john-img">
              <img src="{{ asset('uploads/admins/'.auth('admin')->user()->image) }}" 
                   class="rounded-circle" width="40" height="40" 
                   onerror="this.src='/assets/images/profile/user-1.jpg'"
                   alt="Admin Profile">
            </div>
            <div class="john-title">
              <h6 class="mb-0 fs-4 fw-semibold">{{ auth('admin')->user()->name }}</h6>
              <span class="fs-2 text-capitalize">{{ auth('admin')->user()->roles->first()?->name ?? 'Admin' }}</span>
            </div>
            <form action="{{ route('admin.logout') }}" method="POST" class="ms-auto">
              @csrf
              <button type="submit" class="border-0 bg-transparent text-primary" tabindex="0" aria-label="logout" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="logout">
                <i class="ti ti-power fs-6"></i>
              </button>
            </form>
          </div>
        </div>

        <!-- ---------------------------------- -->
        <!-- Start Vertical Layout Sidebar -->
        <!-- ---------------------------------- -->
      </div>
    </aside>