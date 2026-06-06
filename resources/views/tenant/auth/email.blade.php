@extends('layouts.app')
@section('content')
    <div class="main-content container text-center">
        <!-- @php $logo = App\Models\Site\SiteLogo::first(); @endphp
        @if (isset($logo->image))
            <img src="{{ '/storage/' . $logo->image }}" alt="Logo" class="img-fluid mx-auto mb-4" style="max-width: 300px;">
        @endif -->

        <div class="row align-items-center justify-content-center" style="min-height: 75vh;">
            <div class="col-lg-6 text-center text-lg-start mb-4 mb-lg-0">
                <h1 class="display-5 fw-bold mb-3" style="color: #333333;">Recuperação de Senha</h1>
                <p class="lead text-muted">Informe seu e-mail para recuperar sua senha.</p>
            </div>

            <div class="col-lg-5 offset-lg-1">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card shadow rounded-4 border-0">
                    <div class="card-body p-5">
                        <form method="POST" action="{{ route('tenant.password.email') }}">
                            @csrf
                            <div class="mb-4">
                                <label for="email" class="form-label fw-semibold">E-mail</label>
                                <input type="email" id="email" name="email" class="form-control form-control"
                                    placeholder="E-mail" required>
                            </div>
                            <button type="submit" class="btn btn-outline-primary w-100">
                                Enviar Link de Recuperação
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<style>
    .main-content {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #333;
    }

    .display-5 {
        font-size: 2.5rem;
        line-height: 1.2;
        color: #333333;
    }

    .lead.text-muted {
        font-size: 1.25rem;
        color: #6c757d;
    }

    .form-control-lg {
        border-radius: 0.5rem;
        padding: 0.875rem 1.25rem;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #4A90E2;
        box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.25);
    }

    .btn-outline-primary {
        border-color: #4A90E2;
        color: #4A90E2;
        font-weight: 500;
        font-size: 1rem;
        padding: 0.75rem 1.25rem;
        transition: all 0.3s ease;
    }

    .btn-outline-primary:hover {
        background-color: #E8F0FD;
        color: #357ABD;
        border-color: #357ABD;
        transform: translateY(-2px);
    }

    .card {
        border: none;
    }

    .shadow-sm {
        box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.05);
    }

    .rounded-pill {
        border-radius: 50rem !important;
    }
</style>