<template>
    <div class="container-fluid px-4 px-lg-5 py-4">
        <div v-if="loading" class="d-flex justify-content-center py-5">
            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
        </div>

        <div v-else>
            <div class="d-flex align-items-center gap-2 mb-4">
                <a :href="urlListConsultations"
                    class="btn btn-sm btn-light d-flex align-items-center justify-content-center p-0"
                    style="width:30px; height:30px;">
                    <i class="fa-solid fa-arrow-left fa-xs"></i>
                </a>
                <div>
                    <h4 class="mb-0 fw-semibold">Consulta</h4>
                </div>
            </div>

            <div class="card border shadow-none">
                <div class="card-header bg-transparent border-bottom d-flex align-items-center gap-2 py-3">
                    <span class="d-flex align-items-center justify-content-center rounded-2 p-1"
                        style="width:28px; height:28px; background:#EEF2FF;">
                        <i class="fa-solid fa-user fa-xs" style="color:#4F46E5;"></i>
                    </span>
                    <h6 class="mb-0 fw-semibold">Paciente</h6>
                </div>
                <div class="card-body px-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="py-2">
                                <p class="mb-0 fw-medium small mt-1">
                                    <span class="text-muted">Nome:</span> {{ consultation.patient.full_name }}
                                </p>
                                <p class="mb-0 fw-medium small mt-1">
                                    <span class="text-muted">Data Nasc.:</span>
                                    {{ formattedBirthDate(consultation.patient.date_of_birth) }}
                                </p>
                                <p class="mb-0 fw-medium small mt-1">
                                    <span class="text-muted">CPF:</span> {{ consultation.patient.cpf }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="py-2">
                                <p class="mb-0 fw-medium small mt-1">
                                    <span class="text-muted">Telefone:</span> {{ consultation.patient.phone }}
                                </p>
                                <p class="mb-0 fw-medium small mt-1">
                                    <span class="text-muted">Email:</span> {{ consultation.patient.email }}
                                </p>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-12 d-flex flex-column gap-4">

                <!-- Configurações da sessão -->
                <div class="card border shadow-none">
                    <div class="card-header bg-transparent border-bottom d-flex align-items-center gap-2 py-3">
                        <span class="d-flex align-items-center justify-content-center rounded-2 p-1"
                            style="width:28px; height:28px; background:#F0FDF4;">
                            <i class="fa-solid fa-sliders fa-xs" style="color:#059669;"></i>
                        </span>
                        <h6 class="mb-0 fw-semibold">Configurações da sessão</h6>
                    </div>
                    <div class="card-body px-4">
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-semibold text-uppercase text-muted">Local</label>
                                <select class="form-select form-select-sm" v-model="consultation.location"
                                    :disabled="isDisabled">
                                    <option v-for="(label, key) in getLocations" :key="key" :value="key">{{ label }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-semibold text-uppercase text-muted">Estado
                                    emocional</label>
                                <select class="form-select form-select-sm" v-model="consultation.emotional_state"
                                    :disabled="isDisabled">
                                    <option v-for="(label, key) in getEmotionalStates" :key="key" :value="key">{{ label
                                        }}</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-semibold text-uppercase text-muted">Nível de
                                    engajamento</label>
                                <select class="form-select form-select-sm" v-model="consultation.engagement_level"
                                    :disabled="isDisabled">
                                    <option v-for="(label, key) in getEngagementLevel" :key="key" :value="key">{{ label
                                        }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Prontuário clínico -->
                <div class="card border shadow-none">
                    <div class="card-header bg-transparent border-bottom d-flex align-items-center gap-2 py-3">
                        <span class="d-flex align-items-center justify-content-center rounded-2 p-1"
                            style="width:28px; height:28px; background:#EEF2FF;">
                            <i class="fa-regular fa-file-lines fa-xs" style="color:#4F46E5;"></i>
                        </span>
                        <div>
                            <h6 class="mb-0 fw-semibold">Prontuário clínico</h6>
                            <small class="text-muted">Registro da sessão</small>
                        </div>
                    </div>
                    <div class="card-body px-4">

                        <!-- Objetivos e conteúdo -->
                        <p class="small fw-semibold text-uppercase text-muted mb-3 pb-2 border-bottom"
                            style="letter-spacing:.05em; font-size:11px;">Objetivos e conteúdo</p>
                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold text-uppercase text-muted">Objetivos da
                                    sessão</label>
                                <textarea class="form-control form-control-sm" rows="4"
                                    v-model="consultation.objectives" :disabled="isDisabled"></textarea>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold text-uppercase text-muted">Conteúdo
                                    trabalhado</label>
                                <textarea class="form-control form-control-sm" rows="4"
                                    v-model="consultation.content_worked" :disabled="isDisabled"></textarea>
                            </div>
                        </div>

                        <!-- Observações e intervenções -->
                        <p class="small fw-semibold text-uppercase text-muted mb-3 pb-2 border-bottom"
                            style="letter-spacing:.05em; font-size:11px;">Observações e intervenções</p>
                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold text-uppercase text-muted">Observações
                                    clínicas</label>
                                <textarea class="form-control form-control-sm" rows="4"
                                    v-model="consultation.clinical_observations" :disabled="isDisabled"></textarea>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold text-uppercase text-muted">Intervenções e
                                    técnicas</label>
                                <textarea class="form-control form-control-sm" rows="4"
                                    v-model="consultation.interventions" :disabled="isDisabled"></textarea>
                            </div>
                        </div>

                        <!-- Planejamento e tarefas -->
                        <p class="small fw-semibold text-uppercase text-muted mb-3 pb-2 border-bottom"
                            style="letter-spacing:.05em; font-size:11px;">Planejamento e tarefas</p>
                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold text-uppercase text-muted">Planejamento para
                                    próximas sessões</label>
                                <textarea class="form-control form-control-sm" rows="4" v-model="consultation.planning"
                                    :disabled="isDisabled"></textarea>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold text-uppercase text-muted">Tarefas /
                                    exercícios propostos</label>
                                <textarea class="form-control form-control-sm" rows="4" v-model="consultation.homework"
                                    :disabled="isDisabled"></textarea>
                            </div>
                        </div>

                        <!-- Insights -->
                        <p class="small fw-semibold text-uppercase text-muted mb-3 pb-2 border-bottom"
                            style="letter-spacing:.05em; font-size:11px;">Insights</p>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-uppercase text-muted">Insights da
                                    sessão</label>
                                <textarea class="form-control form-control-sm" rows="4" v-model="consultation.insights"
                                    :disabled="isDisabled"></textarea>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <!-- ============================= -->

            <div class="card border shadow-none">
                <div class="card-header bg-transparent border-bottom py-3">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="d-flex align-items-center justify-content-center rounded-2 p-1"
                                style="width:28px; height:28px; background:#FEF3C7;">
                                <i class="fa-regular fa-calendar fa-xs" style="color:#D97706;"></i>
                            </span>
                            <h6 class="mb-0 fw-semibold">Sessão</h6>
                        </div>
                        <div class="d-flex flex-column flex-md-row gap-3 text-md-start">
                            <div>
                                <p class="mb-0 text-muted small text-uppercase" style="letter-spacing:.05em;">
                                    Data agendada
                                </p>
                                <p class="mb-0 fw-medium small mt-1">
                                    {{ formatDate(consultation.scheduled_at) }}
                                </p>
                            </div>
                            <div>
                                <p class="mb-0 text-muted small text-uppercase" style="letter-spacing:.05em;">
                                    Status
                                </p>
                                <div class="mt-1">
                                    <span class="badge rounded-pill" :class="{
                                        'text-bg-success': consultation.status === 'done',
                                        'text-bg-danger': consultation.status === 'canceled',
                                        'text-bg-warning': consultation.status === 'open'
                                    }">
                                        {{ sanitizeString(consultation.status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body px-4">
                    <div class="d-flex flex-column flex-md-row gap-2">
                        <button
                            class="btn btn-sm btn-success d-flex align-items-center justify-content-center gap-1 w-100"
                            @click="startSession" :disabled="isDisabled || consultation.started_at">
                            <i class="fa-regular fa-clock fa-xs"></i> Iniciar
                        </button>
                        <button
                            class="btn btn-sm btn-primary d-flex align-items-center justify-content-center gap-1 w-100"
                            @click="openPaymentModal" :disabled="isDisabled">
                            <i class="fa-solid fa-check fa-xs"></i> Finalizar
                        </button>
                        <button
                            class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center gap-1 w-100"
                            @click="cancelSession" :disabled="isDisabled">
                            <i class="fa-solid fa-xmark fa-xs"></i> Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="paymentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Registrar pagamento da consulta</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            @click="closePaymentModal"></button>
                    </div>
                    <div class="btn-group w-100 mb-3" role="group">
                        <button type="button" class="btn btn-outline-success btn-sm flex-fill"
                            :class="tabname == 'paid' ? 'active' : ''" @click="typePayment('paid')">
                            <i class="fa-solid fa-dollar-sign me-1"></i> Pagou
                        </button>
                        <button type="button" class="btn btn-outline-warning btn-sm flex-fill"
                            :class="tabname == 'pending' ? 'active' : ''" @click="typePayment('pending')">
                            <i class="fa-solid fa-circle-exclamation me-1"></i> Pendente
                        </button>
                        <button type="button" class="btn btn-outline-info btn-sm flex-fill"
                            :class="tabname == 'free' ? 'active' : ''" @click="typePayment('free')">
                            <i class="fa-regular fa-circle-check me-1"></i> Sessão Livre
                        </button>
                    </div>
                    <div v-if="tabname === 'paid'" class="bg-light p-3 rounded small">
                        <div class="mb-2">
                            <input type="text" class="form-control form-control-sm" v-model="config.consultation_price"
                                placeholder="Valor">
                        </div>
                        <div class="mb-2">
                            <select class="form-select form-select-sm" v-model="finance.payment_method_id">
                                <option value="" disabled>Forma de pagamento</option>
                                <option v-for="method in getPaymentMethods" :key="method.id" :value="method.id">
                                    {{ method.label }}
                                </option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label for="discountInput" class="form-label fw-medium">
                                Desconto (R$)
                            </label>
                            <div class="input-group input-group-sm">
                                <input id="discountInput" type="text" class="form-control form-control-sm"
                                    v-mask="'####,##'" v-model="finance.discount" placeholder="0,00"
                                    aria-describedby="discountHelp" />
                            </div>
                        </div>

                        <div v-if="finance.payment_method_id == 3" class="mb-4 p-3 bg-light rounded border">
                            <h6 class="fw-semibold text-primary mb-3">Opções de cartão de crédito</h6>

                            <div class="mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="creditType" id="creditTypeCash"
                                        v-model="finance.credit_type" value="vista" />
                                    <label class="form-check-label fw-medium" for="creditTypeCash">
                                        À vista
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="creditType"
                                        id="creditTypeInstallments" v-model="finance.credit_type" value="parcelado" />
                                    <label class="form-check-label fw-medium" for="creditTypeInstallments">
                                        Parcelado
                                    </label>
                                </div>
                            </div>

                            <div v-if="finance.credit_type === 'parcelado'" class="mt-3">
                                <label for="installmentsSelect" class="form-label fw-medium">
                                    Número de parcelas <span class="text-danger">*</span>
                                </label>
                                <select id="installmentsSelect" class="form-select form-select-sm"
                                    v-model="finance.installments">
                                    <option value="" disabled>Selecione</option>
                                    <option v-for="n in 12" :key="n" :value="n">{{ n }}x</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div v-else-if="tabname === 'pending'" class="bg-light p-3 rounded small">
                        <div class="mb-2">
                            <input type="text" class="form-control form-control-sm" v-model="config.consultation_price"
                                placeholder="Valor">
                        </div>
                        <small class="text-muted">
                            A sessão ficará pendente de pagamento.
                        </small>
                    </div>
                    <div v-else-if="tabname === 'free'">
                        <div class="alert alert-info p-2 small mb-0" role="alert">
                            Não registrar cobrança.
                        </div>
                    </div>

                    <div class="d-grid mt-2">
                        <button class="btn btn-sm btn-primary" @click="endSession">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>




<script>
import { Modal } from 'bootstrap';

export default {
    props: {
        id: Number,
        urlListConsultations: String,
    },
    data() {
        return {
            loading: false,
            consultation: {
                patient: {
                    full_name: '',
                },
                location: '',
            },
            payment_method_id: 1,
            config: {
                consultation_duration: '',
                consultation_price: '',
            },
            finance: {
                amount: '',
                payment_method_id: 1,
                credit_type: '',
                installments: '',
                discount: 0,
            },
            paymentMethods: [],
            modalPaymentInstance: null,
            tabname: 'paid',
        };
    },
    computed: {
        isDisabled() {
            return this.consultation.status === 'done' || this.consultation.status === 'canceled';
        },
        getLocations() {
            return {
                "online": 'Online',
                "in_person": 'Presencial',
            };
        },
        getPaymentMethods() {
            if (!this.paymentMethods)
                this.findPaymentMethods();
            return this.paymentMethods;
        },
        getEmotionalStates() {
            return {
                "joy": 'Alegria',
                "sadness": 'Tristeza',
                "anger": 'Raiva',
                "fear": 'Medo',
                "disgust": 'Nojo',
                "surprise": 'Surpresa',
            };
        },
        getEngagementLevel() {
            return {
                "high": 'Alto',
                "medium": 'Médio',
                "low": 'Baixo',
            };
        }
    },
    mounted() {
        this.find();
        this.findConfig();
        this.findPaymentMethods();
    },
    methods: {
        find() {
            this.loading = true;
            axiosTenant.get(`/consultation/find/${this.id}`)
                .then(response => {
                    this.consultation = response.data.consultation;
                    this.consultation.location = this.consultation.location || 'online'; // Mantem local salvo e usa online apenas como padrao inicial.
                })
                .catch(errors => {
                    this.alertDanger(errors);
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        findConfig() {
            this.loading = true;
            axiosTenant.get('/configuration/get-config')
                .then(response => {
                    this.config = response.data.config;
                })
                .catch(errors => {
                    this.alertDanger(errors);
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        findPaymentMethods() {
            this.loading = true;
            axiosTenant.get('/payment-methods')
                .then(response => {
                    this.paymentMethods = response.data.data;
                })
                .catch(errors => {
                    this.alertDanger(errors);
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        startSession() {
            this.consultation.started_at = new Date().toISOString().slice(0, 19).replace('T', ' ');
        },
        endSession() {
            if (!this.consultation.started_at) {
                this.alertDanger('A sessão não foi iniciada.');
                return;
            }

            if (!this.consultation.location) {
                this.alertDanger('Selecione o local da sessão.');
                return;
            }

            if (this.finance.payment_method_id == 3 && !this.finance.credit_type) {
                this.alertDanger('Selecione o tipo de crédito.');
                return;
            }

            if (this.finance.credit_type == 'parcelado' && !this.finance.installments) {
                this.alertDanger('Selecione a quantidade de parcelas.');
                return;
            }

            this.consultation.ended_at = new Date().toISOString().slice(0, 19).replace('T', ' ');

            const payLoad = {
                started_at: this.consultation.started_at,
                ended_at: this.consultation.ended_at,

                objectives: this.consultation.objectives,
                content_worked: this.consultation.content_worked,
                clinical_observations: this.consultation.clinical_observations,
                interventions: this.consultation.interventions,
                planning: this.consultation.planning,
                homework: this.consultation.homework,
                insights: this.consultation.insights,

                emotional_state: this.consultation.emotional_state,
                engagement_level: this.consultation.engagement_level,

                location: this.consultation.location,
                status: this.consultation.status,

                finance: {
                    credit_type: this.finance.credit_type,
                    installments: this.finance.installments,
                    discount: this.finance.discount
                }
            };

            const paymentConfig = {
                paid: {
                    payment_amount: this.normalizeCurrencyValue(this.config.consultation_price), // Envia numero para a validacao Laravel.
                    payment_method_id: this.finance.payment_method_id,
                    payment_status: 'paid'
                },
                pending: {
                    payment_amount: this.normalizeCurrencyValue(this.config.consultation_price), // Envia numero para a validacao Laravel.
                    payment_method_id: 'pending',
                    payment_status: 'pending'
                },
                free: {
                    payment_amount: 0,
                    payment_method_id: 'free',
                    payment_status: 'free'
                }
            };

            if (paymentConfig[this.tabname]) {
                Object.assign(payLoad.finance, paymentConfig[this.tabname]);
            }

            this.loading = true;
            axiosTenant
                .put(`/consultation/update/${this.id}`, payLoad)
                .then(() => {
                    this.modalPaymentInstance.hide();
                    this.find();
                })
                .catch(errors => {
                    this.alertDanger(errors);
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        openPaymentModal() {
            if (!this.modalPaymentInstance) {
                this.modalPaymentInstance = new Modal(document.getElementById('paymentModal'));
            }
            this.modalPaymentInstance.show();
        },
        formatDate(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            const options = {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
            };
            return date.toLocaleString('pt-BR', options);
        },
        cancelSession() {
            this.confirmYesNo('Tem Certeza que deseja cancelar a sessão?').then(() => {
                this.loading = true;
                this.consultation.status = 'canceled';

                let payLoad = {
                    started_at: this.consultation.started_at,
                    ended_at: this.consultation.ended_at,
                    location: this.consultation.location || 'online',
                    status: this.consultation.status,
                }; // Cancelamento nao envia finance para evitar validacao/criacao indevida de lancamento.

                axiosTenant.put('/consultation/update/' + this.consultation.id, payLoad)
                    .then(response => {
                        this.find();
                    })
                    .catch(errors => {
                        this.alertDanger(errors);
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            });
        },
        sanitizeString(status) {
            switch (status) {
                case 'done':
                    return 'Feito'
                case 'canceled':
                    return 'Cancelado'
                case 'open':
                    return 'Aberto'
            }
        },
        typePayment(tabValue) {
            this.tabname = tabValue;
        },
        closePaymentModal() {
            this.finance.payment_method_id = 1;
            this.finance.credit_type = null;
            this.finance.installments = null;
            this.finance.discount = 0;

            this.modalPaymentInstance.hide();
        },
        normalizeCurrencyValue(value) {
            if (value === null || value === undefined || value === '') return 0;
            const normalized = String(value).includes(',')
                ? String(value).replace(/\./g, '').replace(',', '.')
                : String(value);
            return Number(normalized) || 0;
        },
        formattedBirthDate(birth) {
            if (!this.consultation?.patient?.date_of_birth) return 'N/A';
            const date = new Date(this.consultation.patient.date_of_birth);
            return date.toLocaleDateString('pt-BR');
        }
    },
}
</script>
