<tenant-sidebar-component
    logo="{{ asset('images/logo_wide.png') }}"
    logo-mobile="{{ asset('images/logo.png') }}"
    url-dashboard="{{ route('tenant.dashboard', ['tenant' => session('tenant_domain')]) }}">
</tenant-sidebar-component>
