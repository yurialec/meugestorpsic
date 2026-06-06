<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/sass/tenant_admin.scss', 'resources/js/app.js'])
    <script>
        window.tenant_domain = "{{ session('tenant_domain') }}";
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <!-- AdminLTE -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://assets.pagseguro.com.br/checkout-sdk-js/rc/dist/browser/pagseguro.min.js"></script></head>

<body class="hold-transition sidebar-mini">
    <div id="app">
        <div class="wrapper" style="background-color: #F4F6F9">
            @include('tenant.theme.header')
            @include('tenant.theme.sidebar')
            <div class="content-wrapper">
                <!-- Alert para plan_warning -->
                @if(session('plan_warning'))
                <div class="alert alert-warning alert-dismissible fade show m-3" role="alert">
                    <i class="icon fas fa-exclamation-triangle mr-2"></i>
                    {{ session('plan_warning') }} <a class="text-primary f-bold"
                        href="{{ route('tenant.subscription.index', session('tenant_domain')) }}">Aqui</a>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif
                @yield('content')
            </div>
        </div>
        @include('tenant.theme.footer')
    </div>

    <script src="https://assets.pagseguro.com.br/checkout-sdk-js/rc/dist/browser/pagseguro.min.js"></script>
</body>

</html>