<template>
    <div v-if="loading" class="d-flex justify-content-center text-primary mt-5">
        <div class="spinner-border spinner-border-sm" role="status">
            <span class="visually-hidden"></span>
        </div>
    </div>

    <div v-else class="container-fluid px-4 px-lg-5 py-4">

        <!-- Page Header -->
        <div class="mb-4">
            <h4 class="mb-1 fw-semibold">Visão geral</h4>
        </div>

        <!-- Alert de configuração -->
        <div v-if="!config.consultation_duration" class="alert border d-flex align-items-start gap-2 py-2 px-3 mb-4"
            style="background:#FFFBEB; border-color:#FDE68A !important;">
            <i class="fa-solid fa-triangle-exclamation fa-sm mt-1" style="color:#D97706;"></i>
            <span class="small" style="color:#92400E;">
                Por favor, finalize o <a :href="urlConfiguration" class="fw-medium" style="color:#4F46E5;"> cadastro das informações</a> para usar a agenda.
            </span>
        </div>

        <!-- Metric cards -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="rounded-3 p-3 bg-white" style="background: var(--bs-secondary-bg, #f1f3f5);">
                    <p class="text-muted small fw-semibold text-uppercase mb-1"
                        style="letter-spacing:.04em; font-size:11px;">Total de pacientes</p>
                    <h3 class="mb-0 fw-semibold">{{ graphsInfo.amount_patients }}</h3>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="rounded-3 p-3 bg-white" style="background: var(--bs-secondary-bg, #f1f3f5);">
                    <p class="text-muted small fw-semibold text-uppercase mb-1"
                        style="letter-spacing:.04em; font-size:11px;">Agendamentos hoje</p>
                    <h3 class="mb-0 fw-semibold">{{ graphsInfo.today_appointments }}</h3>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="rounded-3 p-3 bg-white" style="background: var(--bs-secondary-bg, #f1f3f5);">
                    <p class="text-muted small fw-semibold text-uppercase mb-1"
                        style="letter-spacing:.04em; font-size:11px;">Sessões feitas</p>
                    <h3 class="mb-0 fw-semibold">{{ graphsInfo.appointments.done }}</h3>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="rounded-3 p-3 bg-white" style="background: var(--bs-secondary-bg, #f1f3f5);">
                    <p class="text-muted small fw-semibold text-uppercase mb-1"
                        style="letter-spacing:.04em; font-size:11px;">Cancelamentos</p>
                    <h3 class="mb-0 fw-semibold">{{ graphsInfo.appointments.canceled }}</h3>
                </div>
            </div>
        </div>

        <!-- Cards grid -->
        <div class="row g-4">

            <!-- Aniversariantes -->
            <div class="col-12 col-md-4">
                <div class="card border shadow-none h-100">
                    <div class="card-header bg-transparent border-bottom d-flex align-items-center gap-2 py-3">
                        <span class="d-flex align-items-center justify-content-center rounded-2 p-1"
                            style="width:28px; height:28px; background:#D1FAE5;">
                            <i class="fa-solid fa-cake-candles fa-xs" style="color:#059669;"></i>
                        </span>
                        <h6 class="mb-0 fw-semibold">Aniversariantes do mês</h6>
                    </div>
                    <div class="card-body px-4">
                        <template v-if="birthdays.length > 0">
                            <div v-for="patient in birthdays" :key="patient.id"
                                class="d-flex align-items-center gap-2 py-2 border-bottom">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width:32px; height:32px; background:#D1FAE5; color:#065F46; font-size:11px; font-weight:600;">
                                    {{ initials(patient.full_name) }}
                                </div>
                                <div>
                                    <p class="mb-0 fw-medium small">{{ patient.full_name }}</p>
                                    <p class="mb-0 text-muted" style="font-size:12px;">{{
                                        formatBirthday(patient.date_of_birth) }}</p>
                                </div>
                            </div>
                        </template>
                        <div v-else class="text-center py-4">
                            <i class="fa-solid fa-cake-candles fa-2x text-muted opacity-25 mb-2"></i>
                            <p class="text-muted small mb-0">Nenhum aniversariante este mês</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Agenda de hoje -->
            <div class="col-12 col-md-4">
                <div class="card border shadow-none h-100">
                    <div class="card-header bg-transparent border-bottom d-flex align-items-center gap-2 py-3">
                        <span class="d-flex align-items-center justify-content-center rounded-2 p-1"
                            style="width:28px; height:28px; background:#EEF2FF;">
                            <i class="fa-regular fa-calendar fa-xs" style="color:#4F46E5;"></i>
                        </span>
                        <h6 class="mb-0 fw-semibold">Agenda de hoje</h6>
                    </div>
                    <div class="card-body px-4">
                        <!-- lista de sessões do dia aqui se houver -->

                        <!-- Próxima sessão -->
                        <div v-if="graphsInfo.next_appointment"
                            class="d-flex align-items-center gap-3 p-3 rounded-3 mt-2"
                            style="background: var(--bs-secondary-bg, #f1f3f5);">
                            <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0"
                                style="width:32px; height:32px; background:#EEF2FF;">
                                <i class="fa-regular fa-clock fa-xs" style="color:#4F46E5;"></i>
                            </div>
                            <div>
                                <p class="mb-0 text-muted"
                                    style="font-size:11px; text-transform:uppercase; letter-spacing:.05em; font-weight:500;">
                                    Próxima
                                    sessão</p>
                                <p class="mb-0 fw-semibold"
                                    :class="{ 'text-danger': new Date().toLocaleTimeString('en-GB') > graphsInfo.next_appointment.hour }">
                                    {{ graphsInfo.next_appointment.hour }}
                                </p>
                            </div>
                        </div>
                        <div v-else class="text-center py-4">
                            <i class="fa-regular fa-calendar-check fa-2x text-muted opacity-25 mb-2"></i>
                            <p class="text-muted small mb-0">Nenhuma sessão restante hoje</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status das sessões -->
            <div class="col-12 col-md-4">
                <div class="card border shadow-none h-100">
                    <div class="card-header bg-transparent border-bottom d-flex align-items-center gap-2 py-3">
                        <span class="d-flex align-items-center justify-content-center rounded-2 p-1"
                            style="width:28px; height:28px; background:#FEF3C7;">
                            <i class="fa-solid fa-list-check fa-xs" style="color:#D97706;"></i>
                        </span>
                        <h6 class="mb-0 fw-semibold">Status das sessões</h6>
                    </div>
                    <div class="card-body px-4">
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div class="d-flex align-items-center gap-2 small">
                                <span class="rounded-circle"
                                    style="width:8px; height:8px; background:#059669; display:inline-block;"></span>
                                Feitas
                            </div>
                            <span class="badge rounded-2" style="background:#D1FAE5; color:#065F46; font-size:12px;">
                                {{ graphsInfo.appointments.done }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div class="d-flex align-items-center gap-2 small">
                                <span class="rounded-circle"
                                    style="width:8px; height:8px; background:#F59E0B; display:inline-block;"></span>
                                Agendadas
                            </div>
                            <span class="badge rounded-2" style="background:#FEF3C7; color:#92400E; font-size:12px;">
                                {{ graphsInfo.appointments.open }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-2">
                            <div class="d-flex align-items-center gap-2 small">
                                <span class="rounded-circle"
                                    style="width:8px; height:8px; background:#DC2626; display:inline-block;"></span>
                                Canceladas
                            </div>
                            <span class="badge rounded-2" style="background:#FEE2E2; color:#991B1B; font-size:12px;">
                                {{ graphsInfo.appointments.canceled }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Welcome Modal -->
    <div class="modal fade" id="welcomeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-hidden="true" ref="welcomeModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border shadow-none">
                <div class="modal-body p-4">
                    <h6 class="fw-semibold mb-3">Seja bem-vindo(a) ao Meu Gestor Psic! 🎉</h6>
                    <p class="small text-muted mb-2">É uma alegria ter você com a gente! Agora ficou muito mais fácil
                        organizar sua agenda e seus relatórios.</p>
                    <p class="small text-muted mb-2">Qualquer dúvida, nossa equipe está por aqui para te ajudar. Em
                        breve
                        vamos te contar como ganhar um bônus indicando amigos. 😉</p>
                    <p class="small text-muted mb-0">— Equipe Meu Gestor Psic</p>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-sm btn-primary px-4"
                        @click="closeModalWelcome">Começar</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Modal } from 'bootstrap';

export default {
    name: 'DashboardSchedule',
    props: {
        urlOpenConsultation: String,
        urlConfiguration: String,
    },
    data() {
        return {
            loading: false,
            birthdays: [],
            graphsInfo: {
                amount_patients: '',
                today_appointments: '',
                next_appointment: { day: '', hour: null },
                appointments: { open: '', done: '', canceled: '' }
            },
            welcomeModalInstance: null,
            config: {
                consultation_duration: '',
                consultation_price: '',
                employee_id: '',
                id: '',
            },
        };
    },
    computed: {

    },
    mounted() {
        this.getTenantConfig();
        this.findGraphsInfo();
        this.birthdaysOfTheMonth();
    },
    methods: {
        initials(name) {
            return name.split(' ').slice(0, 2).map(n => n[0]).join('').toUpperCase();
        },
        formatBirthday(date) {
            if (!date) return '';
            const [yyyy, mm, dd] = date.split('-');
            return `${dd}/${mm}`;
        },
        findGraphsInfo() {
            this.loading = true;
            axiosTenant.get('/dashboard/graphs-info')
                .then((response) => {
                    this.graphsInfo = response.data.graphsInfo;
                })
                .catch(err => this.alertDanger(err))
                .finally(() => (this.loading = false));
        },
        getTenantConfig() {
            this.loading = true;
            axiosTenant.get('/configuration/get-config')
                .then((response) => {
                    this.config = response.data.config;

                    const is_employee = response.data.config.employee_id ? true : false;

                    if (!is_employee) {
                        this.consultation_duration = response.data.config.consultation_duration;
                        if (!response.data.config.first_access) {
                            this.$nextTick(() => {
                                const modalEl = this.$refs.welcomeModal;
                                if (modalEl) {
                                    this.welcomeModalInstance = new Modal(modalEl);
                                    this.welcomeModalInstance.show();
                                } else {
                                    console.warn('welcomeModal não encontrado no DOM.');
                                }
                            });
                        }
                    }
                })
                .catch((errros) => {
                    this.alertDanger(errros);
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        closeModalWelcome() {
            this.loading = true;
            axiosTenant.post('/configuration/confirm-first-access')
                .then((response) => {
                    if (response.data.confirmedMessage) {
                        if (this.welcomeModalInstance) {
                            this.welcomeModalInstance.hide();
                            this.welcomeModalInstance = null;
                        }
                    }
                })
                .catch((errros) => {
                    this.alertDanger(errros)
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        birthdaysOfTheMonth() {
            this.loading = true;
            axiosTenant.get('/birthdays-of-the-month')
                .then((response) => {
                    this.birthdays = response.data.items;
                })
                .catch((errros) => {
                    this.alertDanger(errros);
                })
                .finally(() => {
                    this.loading = false;
                });
        }
    }
};
</script>