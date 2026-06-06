<tenant-header-component
    url-profile="{{ route('tenant.profile', ['tenant' => session('tenant_domain')]) }}"
    url-logout="{{ route('tenant.logout', ['tenant' => session('tenant_domain')]) }}"
    url-config="{{ route('tenant.configuration.index', ['tenant' => session('tenant_domain')]) }}"
    admin="{{ session('client.admin') }}">
</tenant-header-component>