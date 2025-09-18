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

                @if(auth()->user()?->canResource('admin_payments','view'))
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
                @endif

                @if(auth()->user()?->canResource('admin_commissions','view'))
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
                @endif

                @if(auth()->user()?->canResource('admin_expenses','view'))
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
                @endif

                @if(auth()->user()?->canResource('admin_incomes','view'))
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
                @endif

                @php
                    $canUsers       = auth()->user()?->canResource('admin_users','view');
                    $canStudents    = auth()->user()?->canResource('admin_students','view');
                    $canInstructors = auth()->user()?->canResource('admin_instructors','view');
                    $canAgents      = auth()->user()?->canResource('admin_agents','view');
                    $canManagers    = auth()->user()?->canResource('admin_managers','view');
                    $showUserManagement   = $canUsers || $canStudents || $canInstructors || $canAgents || $canManagers;
                @endphp

                @if($showUserManagement)
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ setSidebarActive(['admin.user.*','admin.student.*','admin.instructor.*','admin.agent.*','admin.manager.*']) }}"
                        href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="fa-solid fa-users"></i>
                        </span>
                        <span class="nav-link-title">User Management</span>
                        </a>
                        <div class="dropdown-menu {{ setSidebarActive(['admin.user.*','admin.student.*','admin.instructor.*','admin.agent.*','admin.manager.*']) }}">
                            <div class="dropdown-menu-columns">
                                <div class="dropdown-menu-column">
                                    @if($canUsers)
                                        <a class="dropdown-item {{ Request::routeIs('admin.user.*') ? 'text-white fw-bold' : '' }}"
                                        href="{{ route('admin.user.index') }}">Users</a>
                                    @endif
                                    @if($canStudents)
                                        <a class="dropdown-item {{ Request::routeIs('admin.student.*') ? 'text-white fw-bold' : '' }}"
                                        href="{{ route('admin.student.index') }}">Students</a>
                                    @endif
                                    @if($canInstructors)
                                        <a class="dropdown-item {{ Request::routeIs('admin.instructor.*') ? 'text-white fw-bold' : '' }}"
                                        href="{{ route('admin.instructor.index') }}">Instructors</a>
                                    @endif
                                    @if($canAgents)
                                        <a class="dropdown-item {{ Request::routeIs('admin.agent.*') ? 'text-white fw-bold' : '' }}"
                                        href="{{ route('admin.agent.index') }}">Agents</a>
                                    @endif
                                    @if($canManagers)
                                        <a class="dropdown-item {{ Request::routeIs('admin.manager.*') ? 'text-white fw-bold' : '' }}"
                                        href="{{ route('admin.manager.index') }}">Managers</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </li>
                @endif


                @php
                    $canCourses = auth()->user()?->canResource('admin_courses','view');
                    $canModules = auth()->user()?->canResource('admin_modules','view');
                    $canLessons = auth()->user()?->canResource('admin_lessons','view');
                    $showCourseManagement = $canCourses || $canModules || $canLessons;
                @endphp

                @if($showCourseManagement)
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ setSidebarActive(['admin.course.*','admin.module.*','admin.lesson.*']) }}"
                        href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="fa-solid fa-book"></i>
                        </span>
                        <span class="nav-link-title">Courses Management</span>
                        </a>
                        <div class="dropdown-menu {{ setSidebarActive(['admin.course.*','admin.module.*','admin.lesson.*']) }}">
                            <div class="dropdown-menu-columns">
                                <div class="dropdown-menu-column">
                                @if($canCourses)
                                    <a class="dropdown-item {{ Request::routeIs('admin.course.*') ? 'text-white fw-bold' : '' }}"
                                    href="{{ route('admin.course.index') }}">Courses</a>
                                @endif
                                @if($canModules)
                                    <a class="dropdown-item {{ Request::routeIs('admin.module.*') ? 'text-white fw-bold' : '' }}"
                                    href="{{ route('admin.module.index') }}">Modules</a>
                                @endif
                                @if($canLessons)
                                    <a class="dropdown-item {{ Request::routeIs('admin.lesson.*') ? 'text-white fw-bold' : '' }}"
                                    href="{{ route('admin.lesson.index') }}">Lessons</a>
                                @endif
                                </div>
                            </div>
                        </div>
                    </li>
                @endif

                @if(auth()->user()?->canResource('admin_recruitments','view'))
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
                @endif

                @if(auth()->user()?->canResource('admin_graduates','view'))
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
                @endif

                @if(auth()->user()?->canResource('admin_settings','view'))
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
                @endif

                @if(auth()->user()?->canResource('admin_send_bulk_emails','view'))
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
                @endif

                @if(auth()->user()?->canResource('admin_email_logs','view'))
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
                @endif







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



                    <li class="nav-item dropdown">
                        <a class="nav-link cursor-pointer" onclick="event.preventDefault(); getElementById('logout').submit();">
                            <span
                                class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="fa-solid fa-right-from-bracket"></i>
                            </span>
                            <span class="nav-link-title">
                                Logout
                            </span>
                        </a>
                        <form method="POST" id="logout" action="{{ route('logout') }}">
                            @csrf
                        </form>
                    </li>
               

                
            </ul>
        </div>
    </div>
</aside>
