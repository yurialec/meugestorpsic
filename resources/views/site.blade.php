@extends('layouts.app')

@section('content')
    <div class="container px-4 px-lg-5 py-4">

        <!-- HERO -->
        <section id="hero" class="py-5 rounded d-none d-sm-block">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 col-md-8 col-lg-7 col-xl-6 me-auto pe-md-5 hero-content">
                        @if(isset($mainText) && !empty($mainText))
                            <h1 class="display-4 mb-3 fw-lighter text-white">{{ $mainText->title }}</h1>
                            <p class="lead text-white mb-4">{{ $mainText->text }}</p>
                            <div
                                class="d-flex flex-column flex-sm-row gap-3 mb-4 justify-content-start justify-content-sm-center justify-content-md-start d-none d-sm-block">
                                <div class="bg-light-primary rounded-3 px-4 py-2">
                                    <span class="fw-bold text-primary">A partir de R$ 29,90/mês</span>
                                </div>
                                <div class="d-flex align-items-center gap-2 text-success">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <small>✅ 10 dias grátis • Sem cartão</small>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-3 justify-content-start">
                                <a href="{{ route('faca-parte') }}" class="btn btn-primary btn-lg px-4">
                                    <i class="bi bi-rocket-takeoff me-2"></i> Testar grátis por 10 dias
                                </a>
                                <a href="#planos" class="btn btn-outline-secondary btn-lg px-4">
                                    Ver planos
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <!-- SOBRE NÓS -->
        @if(isset($about) && !empty($about))
            <section class="py-5">
                <div class="container">
                    <div class="row gy-5 gy-lg-0 align-items-center">
                        <div class="col-12 col-lg-5 d-flex justify-content-center justify-content-lg-start">
                            <div class="overflow-hidden rounded-4 shadow-sm"
                                style="max-width: 400px; width: 100%; height: auto;">
                                <img src="{{ asset('storage/' . $about->image) }}" alt="{{ $about->title }}"
                                    class="img-fluid w-100 h-auto" style="object-fit: cover; aspect-ratio: 4 / 3;"
                                    loading="lazy">
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 offset-lg-1">
                            <h2 class="mb-3 fw-light text-info">{{ $about->title }}</h2>
                            <p class="text-muted mb-4">{{ $about->description }}</p>
                            <div class="d-flex flex-column gap-3">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-check2-circle text-success flex-shrink-0 me-2 mt-1"></i>
                                    <span>Plataforma 100% desenvolvida por psicólogos</span>
                                </div>
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-check2-circle text-success flex-shrink-0 me-2 mt-1"></i>
                                    <span>Atualizações mensais baseadas em feedback real</span>
                                </div>
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-check2-circle text-success flex-shrink-0 me-2 mt-1"></i>
                                    <span>Suporte técnico e clínico em português</span>
                                </div>
                            </div>

                            <div class="d-block d-sm-none mt-4">
                                <div class="d-flex flex-column align-items-center gap-2">
                                    <div class="bg-light-primary rounded-3 px-4 py-2 d-flex align-items-center gap-2">
                                        <span class="fw-bold text-primary">A partir de R$ 29,90/mês</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 text-success">
                                        <i class="bi bi-check-circle-fill fs-5"></i>
                                        <small class="fw-medium">10 dias grátis • Sem cartão</small>
                                    </div>
                                </div>
                            </div>

                            <div class="d-block d-sm-none mt-4">
                                <div class="d-flex flex-column gap-3">
                                    <a href="{{ route('faca-parte') }}"
                                        class="btn btn-primary btn-md px-4 py-3 rounded w-100 d-flex align-items-center justify-content-center">
                                        <i class="bi bi-rocket-takeoff me-2"></i>
                                        Testar grátis por 10 dias
                                    </a>
                                    <a href="#planos" class="btn btn-outline-secondary btn-md px-4 py-3 rounded w-100">
                                        Ver planos
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <!-- DIFERENCIAIS -->
        <section class="py-5">
            <div class="row g-4 text-center">
                <div class="col-md-4">
                    <div class="p-3">
                        <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                            style="width: 64px; height: 64px;">
                            <i class="bi bi-shield-lock fs-1"></i>
                        </div>
                        <h5 class="fw-semibold">LGPD desde o início</h5>
                        <p class="text-muted small">Prontuários criptografados, backup diário e auditoria de acesso.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3">
                        <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                            style="width: 64px; height: 64px;">
                            <i class="bi bi-headset fs-1"></i>
                        </div>
                        <h5 class="fw-semibold">Suporte para psicólogos</h5>
                        <p class="text-muted small">Dúvidas técnicas ou clínicas? Nossa equipe responde em até 1h.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3">
                        <div class="bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                            style="width: 64px; height: 64px;">
                            <i class="bi bi-graph-up fs-1"></i>
                        </div>
                        <h5 class="fw-semibold">Relatórios inteligentes</h5>
                        <p class="text-muted small">Acompanhe evolução dos pacientes, faturamento e taxa de adesão.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- DEPOIMENTOS -->
        <section class="py-5 bg-light">
            <div class="text-center mb-5">
                <h2 class=" fw-lighter text-info">Psicólogos que confiam em nós</h2>
                <p class="text-muted col-lg-7 mx-auto">Veja como o Meu Gestor Saúde transformou a rotina clínica deles</p>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <img src="https://ui-avatars.com/api/?name=Dra.+Ana+Silva&background=0D8ABC&color=fff"
                                    class="rounded-circle me-3" width="50" height="50" alt="Dra. Ana Silva">
                                <div>
                                    <h6 class="mb-0">Dra. Ana Silva</h6>
                                    <small class="text-muted">CRP 04/123456 • Belo Horizonte/MG</small>
                                </div>
                            </div>
                            <p class="text-muted fst-italic">
                                “Reduzi 8h/semana de burocracia. Agora foco no que importa: meus pacientes.”
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <img src="https://ui-avatars.com/api/?name=Dr.+Carlos+Mendes&background=2563EB&color=fff"
                                    class="rounded-circle me-3" width="50" height="50" alt="Dr. Carlos Mendes">
                                <div>
                                    <h6 class="mb-0">Dr. Carlos Mendes</h6>
                                    <small class="text-muted">CRP 06/654321 • São Paulo/SP</small>
                                </div>
                            </div>
                            <p class="text-muted fst-italic">
                                “A migração foi tranquila. Em 1 dia já tinha todos os prontuários online, com segurança.”
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- IMAGENS --}}
        @if(isset($carousels) && !empty($carousels))
            <section class="py-5">
                <div class="text-center mb-5">
                    <h2 class="fw-bold">Feito para quem leva a clínica a sério</h2>
                    <p class="text-muted col-lg-7 mx-auto">
                        Veja como o Meu Gestor Saúde simplifica sua rotina — sem complicações, sem curva de aprendizado.
                    </p>
                </div>

                <div class="row g-4 justify-content-center">
                    @foreach($carousels as $index => $carousel)
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="text-center">
                                <div class="rounded-3 overflow-hidden shadow-sm mb-3"
                                    style="aspect-ratio: 16/9; background: #f8fafc;">
                                    <img src="{{ asset('storage/' . $carousel['image']) }}"
                                        alt="Tela {{ $index + 1 }} do Meu Gestor Saúde" class="w-100 h-100 object-fit-cover"
                                        loading="{{ $index < 2 ? 'eager' : 'lazy' }}"
                                        onerror="this.parentElement.style.backgroundColor='#e2e8f0'">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-4">
                    <small class="text-muted">
                        ✅ Interface limpa • ✅ Foco no atendimento • ✅ Atualizações contínuas por feedback de psicólogos
                    </small>
                </div>
            </section>
        @endif

        <!-- PLANOS -->
        <section class="py-5" id="planos">
            <div class="text-center mb-5">
                <h2 class=" fw-lighter text-info mb-3">Escolha o plano ideal para sua prática</h2>
                <p class="text-muted col-md-8 mx-auto">
                    Todos os planos incluem agenda online, prontuário eletrônico, lembretes automáticos e suporte
                    especializado.
                </p>
            </div>

            {{-- <div class="row g-4 justify-content-center">
                @foreach($plans as $plan)
                    @php
                        $isPopular = $plan['id'] === 2;
                        $price = $plan['price'] == '0.00' ? 'Grátis' : 'R$ ' . number_format($plan['price'], 2, ',', '.');
                        $durationLabel = match ($plan['dutarion_type']) {
                            'D' => $plan['duration'] == '10' ? '10 dias' : ($plan['duration'] == '30' ? 'por mês' : 'por ano'),
                            default => ''
                        };
                    @endphp

                    <div class="col-md-6 col-lg-4 d-flex">
                        <div
                            class="card h-100 border {{ $isPopular ? 'border-primary shadow' : 'border-light' }} position-relative">
                            @if($isPopular)
                                <div class="position-absolute top-0 start-50 translate-middle badge bg-success px-3 py-2 z-index-1">
                                    <small class="fw-bold">Mais Escolhido</small>
                                </div>
                            @endif

                            <div class="card-body d-flex flex-column">
                                <div class="mb-3">
                                    <h5 class="card-title fw-bold mb-1">{{ $plan['name'] }}</h5>
                                    @if($plan['price'] !== '0.00')
                                        <div class="d-flex align-items-baseline">
                                            <span class="display-6 fw-bold">{{ $price }}</span>
                                            <span class="ms-2 text-muted">{{ $durationLabel }}</span>
                                        </div>
                                    @endif
                                </div>

                                <p class="text-muted small">{{ $plan['description'] }}</p>

                                <div class="mt-3 mb-4 flex-grow-1">
                                    <ul class="list-unstyled mb-0" style="min-height: 160px;">
                                        @forelse($plan['features'] as $feature)
                                            @php
                                                $isIndividual = $feature['type'] === 'individual';
                                                $badgeClass = $isIndividual ? 'bg-success-subtle text-success' : 'bg-info-subtle text-info';
                                                $icon = $isIndividual ? 'bi-person' : 'bi-building';
                                            @endphp
                                            <li class="mb-3 p-2 rounded bg-light">
                                                <div class="d-flex align-items-start">
                                                    <i class="{{ $icon }} fs-5 me-2 mt-1 flex-shrink-0"></i>
                                                    <div>
                                                        <div class="d-flex justify-content-between">
                                                            <strong>{{ $feature['name'] }}</strong>
                                                            <span class="badge {{ $badgeClass }} rounded-pill px-2 py-1">
                                                                {{ $isIndividual ? 'Individual' : 'Clínica' }}
                                                            </span>
                                                        </div>
                                                        <small class="text-muted d-block mt-1">
                                                            @if($isIndividual)
                                                                Ideal para profissionais autônomos
                                                            @else
                                                                Até <strong>{{ $feature['user_limit'] }}</strong> usuários
                                                            @endif
                                                        </small>
                                                        @if($feature['extra_price'] && number_format($feature['extra_price'], 2) !== number_format($plan['price'], 2))
                                                            <div class="mt-1">
                                                                <small class="fw-bold text-primary">
                                                                    + R$ {{ number_format($feature['extra_price'], 2, ',', '.') }}
                                                                </small>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </li>
                                        @empty
                                            <li class="text-center text-muted py-4">
                                                <i class="bi bi-info-circle me-1"></i> Sem modalidades disponíveis
                                            </li>
                                        @endforelse
                                    </ul>
                                </div>

                                <div class="mt-auto">
                                    @if($plan['price'] == '0.00')
                                        <a href="{{ route('faca-parte') }}" class="btn btn-outline-primary w-100">Testar Grátis</a>
                                    @else
                                        <a href="#" class="btn btn-primary w-100">
                                            {{ $isPopular ? 'Assinar Agora' : 'Escolher' }}
                                        </a>
                                        <small class="text-muted d-block text-center mt-2">
                                            ✅ 10 dias grátis em todos os planos
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div> --}}
        </section>

        <!-- CTA FINAL -->
        <section class="py-5 text-center bg-light">
            <div class="col-lg-8 mx-auto">
                <h2 class=" fw-lighter text-info mb-3">Pronto para transformar sua prática clínica?</h2>
                <p class="text-muted mb-4">
                    Comece hoje com 10 dias grátis. Sem cartão de crédito. Cancelamento fácil.
                </p>
                <div class="d-flex flex-wrap gap-3 justify-content-center">
                    <a href="{{ route('faca-parte') }}" class="btn btn-primary btn-lg px-5">
                        <i class="bi bi-rocket-takeoff me-2"></i> Testar grátis agora
                    </a>
                    <a href="{{ route('tenant.login.form') }}" class="btn btn-outline-secondary btn-lg px-5">
                        Já sou cliente
                    </a>
                </div>
            </div>
        </section>

    </div>
@endsection

<style>
    #hero {
        background-image: url('{{ asset('images/cover.jpg') }}');
        background-size: cover;
        background-position: right center;
        background-repeat: no-repeat;
        position: relative;
        min-height: 60vh;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        margin-left: calc(-1 * var(--bs-gutter-x, 0.75rem));
        margin-right: calc(-1 * var(--bs-gutter-x, 0.75rem));
    }

    #hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 50%;
        height: 100%;
        background: linear-gradient(to right, rgba(0, 0, 0, 0.1), transparent);
        pointer-events: none;
    }

    #hero .container-fluid {
        padding-left: 1rem;
        padding-right: 1rem;
    }

    #hero .hero-content {
        max-width: 600px;
        text-align: left;
    }
</style>