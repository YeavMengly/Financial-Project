<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="{{ route('dashboard.index') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset('assets/images/DMS.svg') }}" alt="" height="24">
                        {{-- <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                            viewBox="0 0 24 24">
                            <path
                                d="M12 2 1 7v2h22V7L12 2zm1 18h-2v-2h2v2zm4 0h-2v-4h2v4zM7 20H5v-4h2v4zm14-6H3v2h18v-2z" />
                        </svg> --}}
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('assets/images/DMS.svg') }}" alt="" height="24">
                        {{-- <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                            viewBox="0 0 24 24">
                            <path
                                d="M12 2 1 7v2h22V7L12 2zm1 18h-2v-2h2v2zm4 0h-2v-4h2v4zM7 20H5v-4h2v4zm14-6H3v2h18v-2z" />
                        </svg> --}}
                        <span class="logo-txt">ប្រព័ន្ធគ្រប់គ្រង</span>
                    </span>

                </a>
            </div>
            <button type="button" class="btn btn-sm px-3 font-size-16 header-item" id="vertical-menu-btn">
                <i class="fa fa-fw fa-bars"></i>
            </button>
        </div>
        <div class="d-flex">
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item bg-light-subtle border-start border-end"
                    id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user" src="{{ asset('assets/images/users/user.png') }}"
                        alt="Header Avatar">
                    <span class="d-none d-xl-inline-block ms-1 fw-medium">{{ \Auth::user()->fullname }}</span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    <a class="dropdown-item" href="{{ route('profile.index') }}"><i
                            class="mdi mdi mdi-face-man font-size-16 align-middle me-1"></i>
                        {{ __('menus.account.profile') }}</a>
                    <a class="dropdown-item" href="{{ route('profile.password') }}"><i
                            class="mdi mdi mdi-key font-size-16 align-middle me-1"></i>
                        {{ __('buttons.change.password') }}</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ url('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                            class="mdi mdi-logout font-size-16 align-middle me-1"></i>
                        {{ __('menus.account.logout') }}</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>

        </div>
    </div>
</header>
