<header class="navbar navbar-expand-md d-none d-lg-flex d-print-none">
    <div class="container-xl">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu"
            aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="navbar-nav flex-row order-md-last">
            <div class="d-none d-md-flex">
                <a href="?theme=dark" class="nav-link px-0 hide-theme-dark" title="Enable dark mode"
                    data-bs-toggle="tooltip" data-bs-placement="bottom">
                    <i class="fa-regular fa-moon"></i>
                </a>
                <a href="?theme=light" class="nav-link px-0 hide-theme-light" title="Enable dark mode"
                    data-bs-toggle="tooltip" data-bs-placement="bottom">
                    <i class="fa-regular fa-sun"></i>
                </a>

                <div class="nav-item dropdown d-none d-md-flex me-3">
                    <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1" aria-label="Show notifications">
                        <i class="fa-regular fa-bell"></i>
                        <span class="badge bg-red"></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Last updates</h3>
                            </div>
                            <div class="list-group list-group-flush list-group-hoverable">
                                <div class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col-auto"><span class="status-dot status-dot-animated bg-red d-block"></span></div>
                                        <div class="col text-truncate">
                                            <a href="#" class="text-body d-block">Example 1</a>
                                            <div class="d-block text-secondary text-truncate mt-n1">
                                                Change deprecated html tags to text decoration classes (#29604)
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <a href="#" class="list-group-item-actions">
                                                <i class="fa fa-star"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col-auto"><span class="status-dot status-dot-animated bg-green d-block"></span></div>
                                        <div class="col text-truncate">
                                            <a href="#" class="text-body d-block">Example 4</a>
                                            <div class="d-block text-secondary text-truncate mt-n1">
                                                Regenerate package-lock.json (#29730)
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <a href="#" class="list-group-item-actions">
                                                <i class="fa fa-star"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- /.list-group -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown"
                    aria-label="Open user menu">
                    <span class="avatar avatar-sm" style="background-image: url({{ asset(auth()->user()->image) }});"></span>
                    <div class="d-none d-xl-block ps-2">
                        <div>{{ auth()->user()->name }}</div>
                        <div class="mt-1 small text-secondary">{{ ucwords(auth()->user()->mainRole->name) }}</div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    @if(auth()->user()?->canResource('admin_profile','view'))
                        <a href="{{ route('admin.profile.edit') }}" class="dropdown-item">Profile</a>
                    @endif
                    <a href="#" onclick="event.preventDefault(); getElementById('logout').submit();" class="dropdown-item">Logout</a>
                    <form method="POST" id="logout" action="{{ route('logout') }}">
                        @csrf
                    </form>
                </div>
            </div>
        </div>

        <div class="collapse navbar-collapse" id="navbar-menu">
            <div class="d-none d-md-flex align-items-center ms-0 me-3">
                <div class="header-login-stats badge bg-secondary-lt text-secondary px-3 py-2 rounded-pill d-flex align-items-center gap-2">
                    <span class="d-flex align-items-center gap-0">
                        <i class="fa-solid fa-hashtag me-1 fa-sm"></i>
                        <span class="fw-semibold">{{ number_format(auth()->user()->login_count ?? 0) }}</span>
                    </span>
                    <span class="opacity-50">|</span>
                    <span class="d-flex align-items-center gap-2">
                        <i class="fa-regular fa-clock"></i>
                        <span>
                            {{ auth()->user()->last_login_at
                                ? \Illuminate\Support\Carbon::parse(auth()->user()->last_login_at)->format('d-m-Y H:i')
                                : '—' }}
                        </span>
                    </span>
                    <span class="opacity-50">|</span>
                    <span class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-globe"></i>
                        <span>
                            {{ auth()->user()->last_login_ip  }}
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <style>
        .header-login-stats{font-size:.85rem;}
        .header-login-stats i{opacity:.7;}
    </style>
</header>
