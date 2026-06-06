<nav class="navbar navbar-expand-lg bg-white py-3 shadow-sm" style="--bs-bg-opacity: 0.95;">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
            @php
                $logo = App\Models\Site\SiteLogo::first();
            @endphp
            @if ($logo?->image)
                <img src="{{ asset('storage/' . $logo->image) }}" alt="{{ config('app.name', 'Meu Gestor Saúde') }}"
                    class="d-none d-md-block" height="40" loading="eager">
                <span class="d-block d-md-none fw-bold fs-5">Meu Gestor Saúde</span>
            @else
                <span class="fw-bold" style="color: #2563eb;">Meu Gestor Saúde</span>
            @endif
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Alternar navegação">
            <i class="bi bi-list fs-4"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-1 gap-lg-0">
                <li class="nav-item">
                    <a class="nav-link px-3 py-2" href="{{ route('about') }}">Sobre</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 py-2" href="{{ route('contact') }}">Contato</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 py-2" href="{{ route('tenant.login.form') }}">
                        Entrar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-primary btn-sm px-4" href="{{ route('faca-parte') }}"
                        style="--bs-btn-hover-bg: #1d4ed8; --bs-btn-active-bg: #1e40af;">
                        Testar grátis
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- <nav class="navbar navbar-expand-lg bg-light shadow-lg ">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ url('/') }}">
            @php
                $logo = App\Models\Site\SiteLogo::first();
            @endphp
            @if (isset($logo->image))
                <img class="d-none d-md-block" src="{{ '/storage/' . $logo->image }}" alt="Logo Desktop" height="50">
                <img class="d-block d-md-none" src="{{ asset('images/logo.png') }}" width="50" alt="Ícone Mobile">
            @else
                Home
            @endif
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <div class="d-flex flex-wrap align-items-center gap-2 gap-lg-3 ms-lg-auto">
                <a class="btn btn-outline-primary btn-sm" href="{{ route('about') }}">Sobre</a>
                <a class="btn btn-outline-primary btn-sm" href="{{ route('contact') }}">Contato</a>
                <a class="btn btn-outline-primary btn-sm" href="{{ route('faca-parte') }}">Cadastre-se</a>
                <a class="btn btn-outline-primary btn-sm" href="{{ route('tenant.login.form') }}">Login</a>
            </div>
        </div>
    </div>
</nav> -->