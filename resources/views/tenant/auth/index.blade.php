@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <!-- Ilustração (à direita em desktop) -->
        <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center">
            <div class="text-center p-4">
                <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.webp"
                     alt="Plataforma Meu Gestor Saúde: agenda e prontuário para psicólogos"
                     class="img-fluid rounded-3"
                     style="max-width: 420px;"
                     loading="lazy">
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5 bg-light">
                    <!-- Logo (opcional, mas reforça identidade) -->
                    <div class="text-center mb-4">
                        @php $logo = App\Models\Site\SiteLogo::first(); @endphp
                        @if ($logo?->image)
                            <img src="{{ asset('storage/' . $logo->image) }}" 
                                 alt="{{ config('app.name') }}" 
                                 height="40" 
                                 class="mb-3">
                        @else
                            <h2 class="fw-bold" style="color: #1e40af;">{{ config('app.name', 'Meu Gestor Saúde') }}</h2>
                        @endif
                        <h3 class="fw-semibold">Acesse sua conta</h3>
                        <p class="text-muted small">Psicólogos e equipes clínicas</p>
                    </div>

                    <!-- Mensagem de erro melhorada -->
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <div class="d-flex">
                                <i class="bi bi-exclamation-triangle-fill me-2 fs-5 mt-0.5"></i>
                                <div>
                                    <strong>Ops! Algo não está certo.</strong>
                                    <ul class="mb-0 mt-2" style="font-size: 0.875rem;">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                        </div>
                    @endif

                    <!-- Formulário -->
                    <form method="POST" action="{{ route('tenant.login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label fw-medium">E-mail profissional</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-envelope fs-5 text-muted"></i>
                                </span>
                                <input type="email" 
                                       id="email" 
                                       name="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email') }}" 
                                       placeholder="ex: nome@clinica.com.br" 
                                       autocomplete="username"
                                       required>
                            </div>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-medium">
                                Senha
                                @if (Route::has('tenant.password.request'))
                                    <a href="{{ route('tenant.password.request') }}" 
                                       class="float-end text-primary text-decoration-none small">
                                        Esqueceu?
                                    </a>
                                @endif
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-lock fs-5 text-muted"></i>
                                </span>
                                <input type="password" 
                                       id="password" 
                                       name="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       placeholder="••••••••" 
                                       autocomplete="current-password"
                                       required>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Lembrar de mim -->
                        <div class="mb-3 form-check">
                            <input type="checkbox" 
                                   class="form-check-input" 
                                   id="remember" 
                                   name="remember" 
                                   {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label text-muted small" for="remember">
                                Manter conectado
                            </label>
                        </div>

                        <!-- Botão principal -->
                        <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Entrar
                        </button>

                        <!-- Selo de segurança -->
                        <!-- <div class="text-center">
                            <small class="text-muted d-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-shield-lock fs-5 text-success"></i>
                                Conexão segura • Dados criptografados • LGPD compliant
                            </small>
                        </div> -->
                    </form>

                    <!-- Link de cadastro (opcional) -->
                    @if (Route::has('register'))
                        <div class="text-center mt-4 pt-3 border-top">
                            <p class="text-muted small mb-0">
                                Primeiro acesso? 
                                <a href="{{ route('form-faca-parte') }}" class="text-primary text-decoration-none">
                                    <strong>Cadastre-se gratuitamente</strong>
                                </a>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection