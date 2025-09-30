<div class="col-xl-2 col-md-4 wow fadeInLeft">
    <div class="wsus__dashboard_sidebar">

        <form action="{{ route('agent.profile.update-avatar') }}" method="POST"  enctype="multipart/form-data">
        @csrf
            <div class="wsus__dashboard_sidebar_top" style="height: auto">
                <div class="dashboard_banner">
                    <img src="{{ asset('frontend/assets/images/single_topic_sidebar_banner.jpg') }}" alt="img"
                        class="img-fluid">
                </div>
        
                <div class="wsus__dashboard_profile wsus__dashboard_profile_avatar">
                    <div class="img">
                        <img src="{{ asset(auth()->user()->image) }}" alt="profile" class="img-fluid w-100">
                        <label for="profile_photo">
                            <img src="{{ asset('frontend/assets/images/dash_camera.png') }}" alt="camera" class="img-fluid w-100">
                        </label>
                        <input type="file" id="profile_photo" name="avatar" hidden="">
                    </div>
                </div>
        
                <h4 class="mt-2">{{ auth()->user()->name }}</h4>
                <p>{{ auth()->user()->mainRole->name }}</p>
            </div>
        </form>

        <ul class="wsus__dashboard_sidebar_menu">
            <li>
                <a href="{{ route('agent.dashboard') }}" class="{{ setFrontendSidebarActive(['agent.dashboard']) }}">
                    <div class="img">
                        <i class="fas fa-home"></i>
                    </div>
                    Dashboard
                </a>
            </li>

            @if(auth()->user()?->canResource('agent_profile','view'))
                <li>
                    <a href="{{ route('agent.profile') }}" class="{{ setFrontendSidebarActive(['agent.profile']) }}">
                        <div class="img">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        Profile
                    </a>
                </li>
            @endif

            @if(auth()->user()?->canResource('agent_registrations','view'))
                <li>
                    <a href="{{ route('agent.registration.index') }}" class="{{ setFrontendSidebarActive(['agent.registration.index']) }}">
                        <div class="img">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        Registrations
                    </a>
                </li>
            @endif

            @if(auth()->user()?->canResource('agent_documents','view'))
                <li>
                    <a href="{{ route('agent.document') }}" class="{{ setFrontendSidebarActive(['agent.document']) }}">
                        <div class="img">
                            <i class="fa-solid fa-file-contract"></i>
                        </div>
                        Documents
                    </a>
                </li>
            @endif

            @if(auth()->user()?->canResource('agent_finance','view'))
                <li>
                    <a href="{{ route('agent.finance') }}" class="{{ setFrontendSidebarActive(['agent.finance']) }}">
                        <div class="img">
                            <i class="fa-solid fa-credit-card"></i>
                        </div>
                        Finance
                    </a>
                </li>
            @endif

            {{-- <li>
                <a href="dashboard_support.html">
                    <div class="img">
                        <i class="fa-solid fa-ticket"></i>
                    </div>
                    Support Tickets
                </a>
            </li>
            <li>
                <a href="dashboard_notification.html">
                    <div class="img">
                        <i class="fa-solid fa-bell"></i>
                    </div>
                    Notifications
                </a>
            </li> --}}

            <li>
                <a href="javascript:;" onclick="event.preventDefault();  $('#logout').submit();">
                    <div class="img">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    </div>
                    Sign Out
                </a>

                <form method="POST" id="logout" action="{{ route('logout') }}">
                    @csrf
                </form>

            </li>
        </ul>
    </div>
</div>

<script>
    document.getElementById('profile_photo').addEventListener('change', function () {
        this.closest('form').submit();
    });
</script>