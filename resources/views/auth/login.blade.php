<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>{{ __("labels.mlvt") }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset("assets/images/favicon.ico") }}">
    <link rel="stylesheet" href="{{ asset("assets/css/preloader.min.css") }}" type="text/css"/>
    <link href="{{ asset("assets/css/bootstrap.min.css") }}" id="bootstrap-style" rel="stylesheet" type="text/css"/>
    <link href="{{ asset("assets/css/icons.min.css") }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset("assets/css/app.min.css") }}" id="app-style" rel="stylesheet" type="text/css"/>
    <link href="{{ asset("assets/css/custom.css") }}" id="app-style" rel="stylesheet" type="text/css"/>
</head>
<body>
<div class="auth-page">
    <div class="container-fluid p-0">
        <div class="row g-0">
            <div class="col-xxl-3 col-lg-4 col-md-5">
                <div class="auth-full-page-content d-flex p-sm-5 p-4">
                    <div class="w-100">
                        <div class="d-flex flex-column h-100">
                            <div class="mb-4 mb-md-5 text-center">
                                <a href="{{ url("/") }}" class="d-block auth-logo">
                                    <img class="rounded-circle" src="{{ asset("assets/images/DMS.svg") }}" alt=""
                                         height="200">
                                </a>
                            </div>
                            <div class="auth-content my-auto">
                                <div class="text-center">
                                    <h5 class="mb-0">{{ __("labels.mlvt") }}</h5>
                                    <p class="text-muted mt-2"></p>
                                </div>
                                <form id="pristine-valid-example" novalidate class="mt-4 pt-2"
                                      action="{{ url("/login") }}" method="POST">
                                    {{ csrf_field() }}
                                    <input type="hidden"/>
                                    <div class="form-group mb-3">
                                        <label>{{ __("forms.username") }}</label>
                                        <input required data-pristine-required-message="{{ __("messages.required") }}"
                                               type="text"
                                               class="form-control"
                                               name="username"
                                               placeholder="{{ __("forms.username") }} ...">
                                    </div>
                                    <div class="form-group mb-3">
                                        <div class="d-flex align-items-start">
                                            <div class="flex-grow-1">
                                                <label class="form-label">{{ __("forms.password") }}</label>
                                            </div>
                                            <div class="flex-shrink-0">

                                            </div>
                                        </div>

                                        <div class="input-group auth-pass-inputgroup">
                                            <input
                                                required data-pristine-required-message="{{ __("messages.required") }}"
                                                type="password"
                                                name="password"
                                                class="form-control"
                                                placeholder="{{ __("forms.password") }} ..." aria-label="Password"
                                                aria-describedby="password-addon">
                                            <button class="btn btn-light shadow-none ms-0" type="button"
                                                    id="password-addon"><i class="mdi mdi-eye-outline"></i></button>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <button class="btn btn-primary w-100 waves-effect waves-light"
                                                type="submit">{{ __("buttons.login") }}</button>
                                    </div>
                                </form>
                            </div>
                            <div class="mt-4 mt-md-5 text-center">

                            </div>
                        </div>
                    </div>
                </div>
                <!-- end auth full page content -->
            </div>
            <!-- end col -->
            <div class="col-xxl-9 col-lg-8 col-md-7">
                <div class="auth-bg pt-md-5 p-4 d-flex">
                    <div class="bg-overlay bg-primary"></div>
                    <ul class="bg-bubbles">
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                    </ul>
                    <!-- end bubble effect -->
                    <div class="row justify-content-center align-items-center">

                    </div>
                </div>
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
    </div>
    <!-- end container fluid -->
</div>
<!-- JAVASCRIPT -->
<script src="{{ asset("assets/libs/jquery/jquery.min.js") }}"></script>
<script src="{{ asset("assets/libs/bootstrap/js/bootstrap.bundle.min.js") }}"></script>
<script src="{{ asset("assets/libs/metismenu/metisMenu.min.js") }}"></script>
<script src="{{ asset("assets/libs/simplebar/simplebar.min.js") }}"></script>
<script src="{{ asset("assets/libs/node-waves/waves.min.js") }}"></script>
<script src="{{ asset("assets/libs/feather-icons/feather.min.js") }}"></script>
<script src="{{ asset("assets/libs/feather-icons/feather.min.js") }}"></script>
<script src="{{ asset("assets/js/pages/pass-addon.init.js") }}"></script>
<script src="{{ asset("assets/libs/pristinejs/pristine.min.js") }}"></script>
<script src="{{ asset("assets/js/pages/form-validations.init.js") }}"></script>
</body>
</html>
