@extends('layouts.app')
@section('content')
    <section class="py-5 py-md-6">
        <div class="container">
            <div class="row align-items-center justify-content-between g-5">
                <div class="col-lg-6 order-2 order-lg-1">
                        <h1 class="display-5  fw-lighter text-info mb-3 lh-sm">
                            Sobre nós
                        </h1>

                        <p class="lead text-muted mb-4">
                            O Meu Gestor Psic nasceu para iniciantes e foi pensando em quem dedica a vida a cuidar de pessoas. Sabemos que profissionais da área da psicólogia enfrentam rotinas intensas, burocracias e pouco tempo para o que realmente importa: o atendimento humano. Por isso decidimos criar algo simples, objetivo e uma consultoria incluso para se adequar ao mercado. Por isso, criamos um sistema simples, objetivo e acessível, que ajuda a organizar a rotina clínica sem complicações. Evoluímos constantemente com atualizações necessárias, sempre ouvindo quem está na prática todos os dias. Nosso compromisso é facilitar a gestão, para que você possa focar no cuidado.
                        </p>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0 bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                    style="width: 36px; height: 36px;">
                                    <i class="bi bi-calendar-check fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-semibold mb-1">Agenda Inteligente</h6>
                                    <p class="text-muted small mb-0">Lembretes automáticos e redução de faltas</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0 bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center me-3"
                                    style="width: 36px; height: 36px;">
                                    <i class="bi bi-file-earmark-medical fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-semibold mb-1">Prontuário Seguro</h6>
                                    <p class="text-muted small mb-0">LGPD compliant e criptografado</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0 bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center me-3"
                                    style="width: 36px; height: 36px;">
                                    <i class="bi bi-headset fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-semibold mb-1">Suporte Especializado</h6>
                                    <p class="text-muted small mb-0">Psicólogos respondem suas dúvidas</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0 bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center me-3"
                                    style="width: 36px; height: 36px;">
                                    <i class="bi bi-graph-up-arrow fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-semibold mb-1">Gestão Financeira</h6>
                                    <p class="text-muted small mb-0">Relatórios de receita e produtividade</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('form.cadastro') }}" class="btn btn-primary px-4 py-2">
                            <i class="bi bi-rocket-takeoff me-1"></i> Testar grátis por 10 dias
                        </a>
                        <a href="#" class="btn btn-outline-secondary px-4 py-2">
                            Já sou cliente
                        </a>
                    </div>
                </div>
                <div class="col-lg-5 offset-lg-1 order-1 order-lg-2 text-center">
                        <div class="position-relative d-inline-block">
                            <img src="{{ asset('images/about.png') }}" alt="IMAGEM"
                                class="img-fluid rounded-4 shadow-lg" style="max-width: 320px;" loading="lazy">
                            <div
                                class="position-absolute top-0 start-50 translate-middle badge bg-success text-dark px-3 py-2 fw-semibold">
                                <i class="bi bi-shield-check me-1"></i> LGPD
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </section>
@endsection