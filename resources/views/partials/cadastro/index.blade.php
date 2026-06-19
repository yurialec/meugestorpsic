@extends('layouts.app')
@section('content')
    <div class="container py-4 py-lg-5">
        <div class="row justify-content-center align-items-start g-4 g-lg-5">
            <div class="col-12 col-lg-5">
                <h1 class="h2 h-lg-1 fw-light text-info mb-3">
                    Comece hoje, sem compromisso
                </h1>
                <p class="text-muted mb-4">
                    Cadastre-se em 2 minutos e experimente <strong>10 dias grátis</strong> — sem cartão de crédito.
                </p>
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex">
                        <i class="bi bi-check2-circle text-success fs-5 me-2"></i>
                        <div>
                            <strong class="d-block">Histórico completo do paciente</strong>
                            <small class="text-muted">Acompanhe evolução e sessões em um só lugar.</small>
                        </div>
                    </div>
                    <div class="d-flex">
                        <i class="bi bi-check2-circle text-success fs-5 me-2"></i>
                        <div>
                            <strong class="d-block">Atendimento personalizado</strong>
                            <small class="text-muted">Dados estruturados para intervenções precisas.</small>
                        </div>
                    </div>
                    <div class="d-flex">
                        <i class="bi bi-check2-circle text-success fs-5 me-2"></i>
                        <div>
                            <strong class="d-block">Gestão financeira clara</strong>
                            <small class="text-muted">Relatórios automáticos de receitas.</small>
                        </div>
                    </div>
                    <div class="d-flex">
                        <i class="bi bi-check2-circle text-success fs-5 me-2"></i>
                        <div>
                            <strong class="d-block">LGPD desde o primeiro dia</strong>
                            <small class="text-muted">Prontuários criptografados e backup diário.</small>
                        </div>
                    </div>
                </div>
                <div class="mt-4 p-3 bg-light rounded">
                    <small class="text-muted">
                        <i class="bi bi-shield-check text-success me-1"></i>
                        <strong>Seguro e confiável:</strong> mais de XXX psicólogos já usam
                        {{ config('app.name', 'Meu Gestor Saúde') }}
                    </small>
                </div>
            </div>
            <div class="col-12 col-md-8 col-lg-6">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <strong>Verifique os dados:</strong>
                        <ul class="mb-0 mt-2 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                <div class="card shadow-sm border-0">
                    <div class="card-body p-3 p-md-4">
                        <form action="{{ route('cadastro') }}" method="POST">
                            @csrf
                            <input type="hidden" name="type" value="individual">
                            <input type="hidden" name="user_limit" value="individual">
                            <div class="mb-3">
                                <input name="name" type="text" class="form-control" placeholder="Nome" required
                                    maxlength="250">
                            </div>
                            <div class="mb-3">
                                <input name="email" type="email" class="form-control" placeholder="E-mail" required
                                    maxlength="250">
                            </div>
                            <div class="mb-3">
                                <input name="cpf" type="text" class="form-control" placeholder="CPF/CNPJ" maxlength="18"
                                    data-mask="cpf-cnpj" inputmode="numeric">
                            </div>
                            <div class="mb-3">
                                <input name="crp" type="text" class="form-control" placeholder="CRP" required maxlength="9"
                                    data-mask="crp" inputmode="numeric">
                            </div>
                            <div class="mb-3">
                                <input name="phone" type="text" class="form-control" placeholder="WhatsApp" required
                                    maxlength="15" data-mask="phone" inputmode="numeric">
                            </div>
                            <div class="mb-3">
                                <input name="domain" type="text" class="form-control" placeholder="Domínio" required
                                    maxlength="50" data-mask="domain">
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <span class="badge bg-light border text-dark small">
                                        Verificar disponibilidade
                                    </span>
                                    <span class="badge bg-light border text-dark small">
                                        Não alterável
                                    </span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <input name="password" type="password" class="form-control" placeholder="Senha" required
                                    maxlength="250">
                            </div>
                            <div class="mb-3">
                                <small class="text-muted">
                                    <span>
                                        <strong>
                                            Ao criar conta você concorda com os
                                        </strong>
                                    </span>
                                    <a href="{{ asset('termos/Termos_de_Uso_e_Politica_de_Privacidade_Meu_Gestor_Saude.pdf') }}"
                                        target="_blank">
                                        <strong>
                                            Termos de Uso
                                        </strong>
                                    </a>.
                                </small>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2">
                                Criar conta gratuita
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const onlyDigits = (value) => value.replace(/\D/g, '');

            const masks = {
                'cpf-cnpj': (value) => {
                    const digits = onlyDigits(value).slice(0, 14);

                    if (digits.length <= 11) {
                        return digits
                            .replace(/(\d{3})(\d)/, '$1.$2')
                            .replace(/(\d{3})(\d)/, '$1.$2')
                            .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                    }

                    return digits
                        .replace(/^(\d{2})(\d)/, '$1.$2')
                        .replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3')
                        .replace(/\.(\d{3})(\d)/, '.$1/$2')
                        .replace(/(\d{4})(\d{1,2})$/, '$1-$2');
                },
                crp: (value) => onlyDigits(value)
                    .slice(0, 7)
                    .replace(/^(\d{2})(\d)/, '$1/$2'),
                phone: (value) => {
                    const digits = onlyDigits(value).slice(0, 11);

                    if (digits.length <= 10) {
                        return digits
                            .replace(/^(\d{2})(\d)/, '($1) $2')
                            .replace(/(\d{4})(\d{1,4})$/, '$1-$2');
                    }

                    return digits
                        .replace(/^(\d{2})(\d)/, '($1) $2')
                        .replace(/(\d{5})(\d{1,4})$/, '$1-$2');
                },
                domain: (value) => value
                    .toLowerCase()
                    .replace(/[^a-z0-9-]/g, '')
                    .replace(/-{2,}/g, '-')
                    .slice(0, 50),
            };

            document.querySelectorAll('[data-mask]').forEach((input) => {
                input.addEventListener('input', () => {
                    input.value = masks[input.dataset.mask](input.value);
                });
            });
        });
    </script>
@endsection
