<header-component
    url-profile="{{ route('profile.view') }}"
    url-sair="{{ route('admin.logout') }}" logo="{{ App\Models\Site\SiteLogo::first()->image ?? '' }}"
    url-home="{{ route('home') }}">
</header-component>
