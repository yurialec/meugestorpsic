@extends('layouts.app')

@section('content')
    <section class="py-5 py-md-6">
        <div class="container">
            <div class="text-center mb-5">
                <h1 class="display-5  fw-lighter text-info mb-3">Estamos aqui para você</h1>
                <p class="lead text-muted col-lg-8 mx-auto">
                    Dúvidas sobre o plano? Precisa de ajuda para migrar seus pacientes?
                    Fale com nosso time — <strong>psicólogos respondem suas perguntas</strong>.
                </p>
            </div>
            <div class="row g-5 justify-content-center">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h3 class="fw-semibold mb-4 text-center text-lg-start">Nosso escritório</h3>

                            @if ($contact ?? null)
                                <div class="d-flex flex-column gap-4">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0 bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                            style="width: 40px; height: 40px;">
                                            <i class="bi bi-telephone fs-5"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-semibold mb-0">Telefone</h6>
                                            <p class="text-muted mb-0">{{ $contact->phone }}</p>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0 bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center me-3"
                                            style="width: 40px; height: 40px;">
                                            <i class="bi bi-whatsapp fs-5"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-semibold mb-0">WhatsApp</h6>
                                            <p class="text-muted mb-0">{{ $contact->phone }}</p>
                                            <small class="text-success">Resposta em até 1h (dias úteis)</small>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0 bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center me-3"
                                            style="width: 40px; height: 40px;">
                                            <i class="bi bi-envelope fs-5"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-semibold mb-0">Email</h6>
                                            <p class="text-muted mb-0">{{ $contact->email }}</p>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0 bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center me-3"
                                            style="width: 40px; height: 40px;">
                                            <i class="bi bi-geo-alt fs-5"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-semibold mb-0">Endereço</h6>
                                            <p class="text-muted mb-1">{{ $contact->address }}</p>
                                            <p class="text-muted mb-0">{{ $contact->city }}/{{ $contact->state }} •
                                                {{ $contact->zipcode }}</p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <p class="text-muted text-center py-4">
                                    <i class="bi bi-info-circle me-1"></i> Informações em breve.
                                </p>
                            @endif
                            <div class="mt-4 pt-3 border-top">
                                <h6 class="fw-semibold mb-2">Horário de atendimento</h6>
                                <ul class="list-unstyled text-muted small mb-0">
                                    <li class="d-flex justify-content-between">
                                        <span>Segunda a Sexta</span>
                                        <span>9h às 18h</span>
                                    </li>
                                    <li class="d-flex justify-content-between">
                                        <span>Sábados</span>
                                        <span>9h às 12h</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h3 class="fw-semibold mb-4 text-center text-lg-start">Envie sua mensagem</h3>
                            <p class="text-muted small mb-4">
                                Responderemos em até <strong>24h úteis</strong>. Para suporte rápido, prefira o WhatsApp.
                            </p>

                            <form action="#" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="name" class="form-label visually-hidden">Nome</label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Seu nome"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label visually-hidden">E-mail</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="seu@email.com" required>
                                </div>
                                <div class="mb-3">
                                    <label for="subject" class="form-label visually-hidden">Assunto</label>
                                    <select class="form-select" id="subject" name="subject" required>
                                        <option value="" disabled selected>Selecione um assunto</option>
                                        <option value="demo">Agendar demonstração</option>
                                        <option value="support">Suporte técnico</option>
                                        <option value="plan">Dúvidas sobre planos</option>
                                        <option value="migration">Migrar meus dados</option>
                                        <option value="other">Outro</option>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label for="message" class="form-label visually-hidden">Mensagem</label>
                                    <textarea class="form-control" id="message" name="message" rows="4"
                                        placeholder="Conte-nos como podemos ajudar..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 py-2">
                                    <i class="bi bi-send me-1"></i> Enviar mensagem
                                </button>
                            </form>
                            <div class="mt-4 pt-3 border-top text-center">
                                <small class="text-muted">
                                    <i class="bi bi-shield-lock me-1"></i>
                                    Seus dados são protegidos conforme a LGPD. Não compartilhamos com terceiros.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection