<div class="col-xl-2 col-md-4 wow fadeInLeft">
    <div class="wsus__dashboard_sidebar">

        <form action="{{ route('instructor.profile.update-avatar') }}" method="POST"  enctype="multipart/form-data">
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
                    <a href="{{ route('instructor.dashboard') }}" class="{{ setFrontendSidebarActive(['instructor.dashboard']) }}">
                        <div class="img">
                            <i class="fas fa-home"></i>
                        </div>
                        Dashboard
                    </a>
                </li>

            @can('view_instructor_profile')
                <li>
                    <a href="{{ route('instructor.profile') }}" class="{{ setFrontendSidebarActive(['instructor.profile']) }}">
                        <div class="img">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        Profile
                    </a>
                </li>
            @endcan

            @can('view_instructor_groups')
                <li>
                    <a href="{{ route('instructor.groups.index') }}" class="{{ setFrontendSidebarActive(['instructor.groups.index','instructor.groups.*']) }}">
                        <div class="img">
                            <i class="fa-solid fa-people-roof"></i>
                        </div>
                        Groups
                    </a>
                </li>
            @endcan

            @can('view_instructor_students')
                <li>
                    <a href="{{ route('instructor.students.index') }}" class="{{ setFrontendSidebarActive(['instructor.students.index']) }}">
                        <div class="img">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        Students
                    </a>
                </li>
            @endcan

            @can('view_instructor_assignments')
                <li>
                    <a href="{{ route('instructor.assignment.index') }}" class="{{ setFrontendSidebarActive(['instructor.assignment.index','instructor.assignment.*']) }}">
                        <div class="img">
                            <i class="fa-solid fa-file"></i>
                        </div>
                        Assignments
                    </a>
                </li>
            @endcan

           {{--  <li>
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