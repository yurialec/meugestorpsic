<footer class="bg-white border-top py-5" style="--bs-border-opacity: 0.08;">
    <div class="container px-4 px-lg-5">
        <div class="row g-4 mb-4">
            <div class="col-lg-4 col-md-6">
                <a href="{{ url('/') }}" class="d-inline-block mb-3">
                    @php $logo = App\Models\Site\SiteLogo::first(); @endphp
                    @if ($logo?->image)
                        <img src="{{ asset('storage/' . $logo->image) }}" alt="{{ config('app.name', 'Meu Gestor Saúde') }}"
                            width="140" class="img-fluid">
                    @else
                        <span class="h4 fw-bold" style="color: #1e40af;">{{ config('app.name', 'Meu Gestor Saúde') }}</span>
                    @endif
                </a>
                <p class="text-muted mb-3">
                    Plataforma LGPD-compliant para psicólogos: agenda, prontuário e gestão em um só lugar.
                </p>
                <div class="d-flex gap-3">
                    <span class="badge bg-success-subtle text-success">
                        <i class="bi bi-shield-check me-1"></i> LGPD
                    </span>
                    <span class="badge bg-primary-subtle text-primary">
                        <i class="bi bi-cloud-lock me-1"></i> Criptografia
                    </span>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <h6 class="fw-semibold mb-3 text-dark">Empresa</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="{{ route('about') }}"
                            class="text-muted text-decoration-none hover-underline">Sobre</a></li>
                    <li class="mb-2"><a href="{{ route('contact') }}"
                            class="text-muted text-decoration-none hover-underline">Contato</a></li>
                    <li class="mb-2"><a href="{{ route('faca-parte') }}"
                            class="text-muted text-decoration-none hover-underline">Cadastre-se</a></li>
                    <li class="mb-2"><a href="{{ route('tenant.login.form') }}"
                            class="text-muted text-decoration-none hover-underline">Login</a></li>
                    <li class="mb-2"><a href="{{ route('area.restrita') }}"
                            class="text-muted text-decoration-none hover-underline">Área Restrita</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <h6 class="fw-semibold mb-3 text-dark">Recursos</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#" class="text-muted text-decoration-none hover-underline">Agenda
                            Inteligente</a></li>
                    <li class="mb-2"><a href="#" class="text-muted text-decoration-none hover-underline">Prontuário
                            Eletrônico</a></li>
                    <li class="mb-2"><a href="#"
                            class="text-muted text-decoration-none hover-underline">Teleatendimento</a></li>
                    <li class="mb-2"><a href="#" class="text-muted text-decoration-none hover-underline">Faturamento</a>
                    </li>
                    <li><a href="#" class="text-muted text-decoration-none hover-underline">Relatórios</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-12">
                <h6 class="fw-semibold mb-3 text-dark">Fale conosco</h6>

                @if ($contact ?? null)
                    <div class="d-flex flex-column gap-2 mb-3">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-envelope text-primary me-2 mt-1"></i>
                            <span class="text-muted">{{ $contact->email }}</span>
                        </div>
                        <div class="d-flex align-items-start">
                            <i class="bi bi-whatsapp text-success me-2 mt-1"></i>
                            <span class="text-muted">{{ $contact->phone }}</span>
                        </div>
                    </div>
                @else
                    <p class="text-muted text-center py-4">
                        <i class="bi bi-info-circle me-1"></i> Informações em breve.
                    </p>
                @endif

                <div class="d-flex flex-wrap gap-2 mb-3">
                    @forelse($socialmedias as $media)
                        <a href="{{ $media->url }}" target="_blank"
                            class="btn btn-sm btn-outline-secondary rounded-circle p-0 d-flex align-items-center justify-content-center"
                            style="width: 36px; height: 36px;" aria-label="{{ $media->name ?? 'Rede social' }}">
                            <i class="{{ $media->icon }} fs-6"></i>
                        </a>
                    @empty
                        <small class="text-muted">Redes em breve</small>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="border-top mt-4 pt-4">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                    <small class="text-muted">
                        &copy; {{ date('Y') }} {{ config('app.name', 'Meu Gestor Saúde') }}. Todos os direitos
                        reservados.
                    </small>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <small class="text-muted">
                        <a href="#" class="text-muted text-decoration-none mx-2">Política de
                            Privacidade</a>
                        <span class="mx-1">•</span>
                        <a href="#" class="text-muted text-decoration-none mx-2">Termos de Uso</a>
                    </small>
                </div>
            </div>
        </div>
    </div>
</footer>