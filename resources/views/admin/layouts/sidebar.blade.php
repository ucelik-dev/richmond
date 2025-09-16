<aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu"
            aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand navbar-brand-autodark mt-0 mt-md-2">
            <a href="#">
                <img src="{{ asset('default/logo-horizontal.png') }}" width="200" class="bg-white p-2 rounded">
            </a>
        </h1>
      
        <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3">

                <li class="nav-item">
                    <a class="nav-link {{ setSidebarActive(['admin.dashboard']) }}" href="{{ route('admin.dashboard') }}">
                        <span
                            class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="fa-solid fa-house"></i>
                        </span>
                        <span class="nav-link-title">
                            Home
                        </span>
                    </a>
                </li>

                @can('index_admin_payments')
                    <li class="nav-item dropdown">
                        <a class="nav-link {{ setSidebarActive(['admin.payment.*']) }}" href="{{ route('admin.payment.index') }}">
                            <span
                                class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="fa-solid fa-cart-shopping"></i>
                            </span>
                            <span class="nav-link-title">
                                Payments
                            </span>
                        </a>
                    </li>
                @endcan

                @can('index_admin_commissions')
                    <li class="nav-item dropdown">
                        <a class="nav-link {{ setSidebarActive(['admin.commission.*']) }}" href="{{ route('admin.commission.index') }}">
                            <span
                                class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="fa-solid fa-briefcase"></i>
                            </span>
                            <span class="nav-link-title">
                                Commissions
                            </span>
                        </a>
                    </li>
                @endcan

                @can('index_admin_expenses')
                    <li class="nav-item dropdown">
                        <a class="nav-link {{ setSidebarActive(['admin.expense.*']) }}" href="{{ route('admin.expense.index') }}">
                            <span
                                class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="fa-solid fa-money-bill-trend-up"></i>
                            </span>
                            <span class="nav-link-title">
                                Expenses
                            </span>
                        </a>
                    </li>
                @endcan

                @can('index_admin_incomes')
                    <li class="nav-item dropdown">
                        <a class="nav-link {{ setSidebarActive(['admin.income.*']) }}" href="{{ route('admin.income.index') }}">
                            <span
                                class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="fas fa-hand-holding-usd"></i>
                            </span>
                            <span class="nav-link-title">
                                Incomes
                            </span>
                        </a>
                    </li>
                @endcan

                @can('sidebar_admin_user_management')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ setSidebarActive(['admin.user.*','admin.student.*','admin.instructor.*','admin.agent.*','admin.manager.*']) }}" href="#navbar-base" data-bs-toggle="dropdown"
                            data-bs-auto-close="false" role="button" aria-expanded="false">
                            <span
                                class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="fa-solid fa-users"></i>
                            </span>
                            <span class="nav-link-title">
                                User Management
                            </span>
                        </a>
                        <div class="dropdown-menu {{ setSidebarActive(['admin.user.*','admin.student.*','admin.instructor.*','admin.agent.*','admin.manager.*']) }}">
                            <div class="dropdown-menu-columns">
                                
                                <div class="dropdown-menu-column">
                                    @can('index_admin_users')
                                        <a class="dropdown-item {{ Request::routeIs('admin.user.*') ? 'text-white fw-bold' : '' }}" href="{{ route('admin.user.index') }}">
                                            Users
                                        </a>
                                    @endcan
                                    @can('index_admin_students')
                                        <a class="dropdown-item {{ Request::routeIs('admin.student.*') ? 'text-white fw-bold' : '' }}" href="{{ route('admin.student.index') }}">
                                            Students
                                        </a>
                                    @endcan
                                    @can('index_admin_instructors')
                                        <a class="dropdown-item {{ Request::routeIs('admin.instructor.*') ? 'text-white fw-bold' : '' }}" href="{{ route('admin.instructor.index') }}">
                                            Instructors
                                        </a>
                                    @endcan
                                    @can('index_admin_agents')
                                        <a class="dropdown-item {{ Request::routeIs('admin.agent.*') ? 'text-white fw-bold' : '' }}" href="{{ route('admin.agent.index') }}">
                                            Agents
                                        </a>
                                    @endcan
                                    @can('index_admin_managers')
                                        <a class="dropdown-item {{ Request::routeIs('admin.manager.*') ? 'text-white fw-bold' : '' }}" href="{{ route('admin.manager.index') }}">
                                            Managers
                                        </a>
                                    @endcan

                                </div>
                                
                            </div>
                        </div>
                    </li>
                @endcan

                @can('sidebar_admin_course_management')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ setSidebarActive(['admin.course.*','admin.module.*','admin.lesson.*']) }}" href="#navbar-base" data-bs-toggle="dropdown"
                            data-bs-auto-close="false" role="button" aria-expanded="false">
                            <span
                                class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="fa-solid fa-book"></i>
                            </span>
                            <span class="nav-link-title">
                                Courses Management
                            </span>
                        </a>
                        <div class="dropdown-menu {{ setSidebarActive(['admin.course.*','admin.module.*','admin.lesson.*']) }}">
                            <div class="dropdown-menu-columns">
                                
                                <div class="dropdown-menu-column">
                                    @can('index_admin_courses')
                                        <a class="dropdown-item {{ Request::routeIs('admin.course.*') ? 'text-white fw-bold' : '' }}" href="{{ route('admin.course.index') }}">
                                            Courses
                                        </a>
                                    @endcan
                                    @can('index_admin_modules')
                                        <a class="dropdown-item {{ Request::routeIs('admin.module.*') ? 'text-white fw-bold' : '' }}" href="{{ route('admin.module.index') }}">
                                            Modules
                                        </a>
                                    @endcan
                                    @can('index_admin_lessons')
                                        <a class="dropdown-item {{ Request::routeIs('admin.lesson.*') ? 'text-white fw-bold' : '' }}" href="{{ route('admin.lesson.index') }}">
                                            Lessons
                                        </a>
                                    @endcan
                                </div>
                                
                            </div>
                        </div>
                    </li>
                @endcan

                @can('index_admin_recruitments')
                    <li class="nav-item dropdown">
                        <a class="nav-link {{ setSidebarActive(['admin.recruitment.*']) }}" href="{{ route('admin.recruitment.index') }}">
                            <span
                                class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="fa-solid fa-user-plus"></i>
                            </span>
                            <span class="nav-link-title">
                                Recruitments
                            </span>
                        </a>
                    </li>
                @endcan

                @can('index_admin_graduates')
                    <li class="nav-item dropdown">
                        <a class="nav-link {{ setSidebarActive(['admin.graduate.*']) }}" href="{{ route('admin.graduate.index') }}">
                            <span
                                class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="fa-solid fa-user-graduate"></i>
                            </span>
                            <span class="nav-link-title">
                                Graduates
                            </span>
                        </a>
                    </li>
                @endcan

                @can('index_admin_settings')
                    <li class="nav-item dropdown">
                        <a class="nav-link {{ setSidebarActive(['admin.setting.*','admin.setting-*']) }}" href="{{ route('admin.setting.index') }}">
                            <span
                                class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="fa-solid fa-gear"></i>
                            </span>
                            <span class="nav-link-title">
                                Settings
                            </span>
                        </a>
                    </li>
                @endcan

                @can('send_bulk_emails')
                    <li class="nav-item dropdown">
                        <a class="nav-link {{ setSidebarActive(['admin.bulk-email']) }}" href="{{ route('admin.bulk-email.create') }}">
                            <span
                                class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="fa-solid fa-envelopes-bulk"></i>
                            </span>
                            <span class="nav-link-title">
                                Bulk Emails
                            </span>
                        </a>
                    </li>
                @endcan

                @can('index_admin_email_logs')
                    <li class="nav-item dropdown">
                        <a class="nav-link {{ setSidebarActive(['admin.email-log']) }}" href="{{ route('admin.email-log.index') }}">
                            <span
                                class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="fa-solid fa-envelope"></i>
                            </span>
                            <span class="nav-link-title">
                                Email Logs
                            </span>
                        </a>
                    </li>
                @endcan







                @if(auth()->user()->roles->count() > 1)
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown"
                            data-bs-auto-close="false" role="button" aria-expanded="false">
                            <span
                                class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler-icons.io/i/package -->
                                <i class="fa-solid fa-user-shield"></i>
                            </span>
                            <span class="nav-link-title">
                                Your Roles
                            </span>
                        </a>

                            <div class="dropdown-menu">
                                <div class="dropdown-menu-columns">
                                    <div class="dropdown-menu-column">
                                        @foreach(auth()->user()->roles as $role)
                                            @php
                                                $roleName = strtolower($role->name);
                                                $sharedDashboardRoles = ['admin', 'manager', 'sales'];

                                                // Determine the correct route based on the role
                                                if (in_array($roleName, $sharedDashboardRoles)) {
                                                    $routeName = 'admin.dashboard';
                                                } else {
                                                    $routeName = $roleName . '.dashboard';
                                                }
                                            @endphp

                                            @if(Route::has($routeName))
                                                <a href="{{ route($routeName) }}" class="dropdown-item">
                                                    {{ ucfirst($role->name) }}
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        
                    </li>
                @endif

                
            </ul>
        </div>
    </div>
</aside>
