<template>
    <div class="container-fluid px-4 px-lg-5 py-4">

        <div v-if="loading" class="d-flex justify-content-center py-5">
            <div class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="visually-hidden">Carregando...</span>
            </div>
        </div>

        <template v-else>

            <!-- Alert de configuração -->
            <div v-if="!consultation_duration"
                class="alert border d-flex align-items-start gap-2 py-2 px-3 mb-4"
                style="background:#FFFBEB; border-color:#FDE68A !important;">
                <i class="fa-solid fa-triangle-exclamation fa-sm mt-1" style="color:#D97706;"></i>
                <span class="small" style="color:#92400E;">
                    Por favor, <a :href="urlConfiguration" class="fw-medium" style="color:#4F46E5;">configure a duração das sessões</a> para usar a agenda.
                </span>
            </div>

            <template v-else>

                <!-- Card calendário -->
                <div class="card border shadow-none">

                    <!-- Header: navegação de mês -->
                    <div class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between py-3 px-4">
                        <button class="btn btn-sm btn-light d-flex align-items-center justify-content-center p-0"
                            style="width:30px; height:30px;" @click="prevMonth">
                            <i class="fa-solid fa-chevron-left fa-xs"></i>
                        </button>
                        <div class="text-center">
                            <h6 class="mb-0 fw-semibold">{{ formatMonthYear() }}</h6>
                            <div class="d-flex flex-wrap justify-content-center gap-1 mt-1">
                                <span v-for="day in translateAvailableDays(availableDays)" :key="day"
                                    class="badge rounded-pill"
                                    style="background:#EEF2FF; color:#4F46E5; font-size:11px; font-weight:500;">
                                    {{ day }}
                                </span>
                            </div>
                            <small class="text-muted" style="font-size:11px;">
                                {{ schedule.start_time }} às {{ schedule.end_time }}
                            </small>
                        </div>
                        <button class="btn btn-sm btn-light d-flex align-items-center justify-content-center p-0"
                            style="width:30px; height:30px;" @click="nextMonth">
                            <i class="fa-solid fa-chevron-right fa-xs"></i>
                        </button>
                    </div>

                    <!-- Calendário -->
                    <div class="card-body p-0">
                        <div class="cal-grid">

                            <!-- Cabeçalho dias da semana -->
                            <div class="cal-header">
                                <div v-for="(label, idx) in daysOfWeekLabels" :key="idx"
                                    class="cal-header-cell small fw-semibold text-uppercase text-muted text-center">
                                    {{ label }}
                                </div>
                            </div>

                            <!-- Células dos dias -->
                            <div class="cal-body">
                                <div v-for="(dayObj, idx) in calendarDays" :key="dayObj?.date || idx"
                                    class="cal-day"
                                    :class="{
                                        'cal-today':     dayObj && isToday(dayObj.date),
                                        'cal-past':      dayObj && isPastDay(dayObj.date),
                                        'cal-available': dayObj && !isPastDay(dayObj.date) && isAvailableDay(dayObj.date),
                                        'cal-empty':     !dayObj,
                                    }"
                                    @click="dayObj && !isPastDay(dayObj.date) && openModal(dayObj.day, dayObj.date)">

                                    <span v-if="dayObj" class="cal-day-number small fw-semibold">
                                        {{ dayObj.day }}
                                    </span>

                                    <!-- Indicador de agendamento -->
                                    <span v-if="dayObj && hasAppointment(dayObj.date)"
                                        class="cal-dot bg-success rounded-circle">
                                    </span>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </template>
        </template>

        <!-- Modal de agendamento -->
        <div class="modal fade" id="modalDay" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border shadow-none">

                    <!-- Tab: horários -->
                    <template v-if="tabName === 'listHours'">
                        <div class="modal-header border-bottom">
                            <h6 class="modal-title fw-semibold d-flex align-items-center gap-2">
                                <i class="fa-regular fa-clock fa-sm text-muted"></i>
                                Horários disponíveis
                            </h6>
                            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div v-if="availableTimes.length > 0" class="row g-2">
                                <div v-for="time in validateAvailableHours(availableTimes, selectedDate)"
                                    :key="time" class="col-4">
                                    <button @click="selectTime(time, 'listPatients')"
                                        class="btn btn-sm w-100 d-flex align-items-center justify-content-center gap-1"
                                        :class="isTimeBusy(time) ? 'btn-outline-success' : 'btn-outline-primary'">
                                        {{ time }}
                                        <i v-if="isTimeBusy(time)" class="fa-solid fa-user fa-xs"></i>
                                    </button>
                                </div>
                            </div>
                            <div v-else class="text-center py-3">
                                <i class="fa-regular fa-calendar-xmark fa-2x text-muted opacity-25 mb-2"></i>
                                <p class="text-muted small mb-0">Nenhum horário disponível para este dia.</p>
                            </div>
                        </div>
                        <div class="modal-footer border-top">
                            <button type="button" class="btn btn-sm btn-light px-4" @click="closeModal">Fechar</button>
                        </div>
                    </template>

                    <!-- Tab: pacientes -->
                    <template v-else-if="tabName === 'listPatients'">
                        <div class="modal-header border-bottom">
                            <h6 class="modal-title fw-semibold d-flex align-items-center gap-2">
                                <i class="fa-solid fa-user fa-sm text-muted"></i>
                                Selecionar paciente
                            </h6>
                            <button type="button" class="btn-close btn-close-sm"
                                :disabled="sendingEmail" @click="closeModal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div v-if="sendingEmail" class="d-flex justify-content-center py-3">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Confirmando...</span>
                                </div>
                            </div>
                            <div v-else>
                                <label class="form-label small fw-semibold text-uppercase text-muted">Paciente</label>
                                <div class="d-flex align-items-center gap-2">
                                    <select class="form-select form-select-sm flex-grow-1"
                                        v-model="patientSelected" :disabled="isPatientConfirmed" required>
                                        <option disabled value="">Selecione um paciente</option>
                                        <option v-for="patient in patients" :key="patient.id" :value="patient.id">
                                            {{ patient.full_name }}
                                        </option>
                                    </select>
                                    <button type="button" class="btn btn-sm btn-light"
                                        style="width:32px; height:32px; padding:0;"
                                        @click="changeTabName('registerPatient')"
                                        :disabled="isPatientConfirmed" title="Cadastrar novo paciente">
                                        <i class="fa-solid fa-plus fa-xs"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-light"
                                        style="width:32px; height:32px; padding:0;"
                                        @click="removePatient(patientSelected, selectedDate, selectedTime)"
                                        :disabled="!isPatientConfirmed" title="Remover paciente">
                                        <i class="fa-solid fa-xmark fa-xs"></i>
                                    </button>
                                </div>
                                <div v-if="!isPatientConfirmed" class="mt-4">
                                    <label class="form-label small fw-semibold text-uppercase text-muted">Pagamento</label>
                                    <div class="btn-group w-100 mb-3" role="group">
                                        <button type="button" class="btn btn-outline-success btn-sm flex-fill"
                                            :class="paymentTabName === 'paid' ? 'active' : ''"
                                            @click="typePayment('paid')">
                                            <i class="fa-solid fa-dollar-sign me-1"></i> Pagou
                                        </button>
                                        <button type="button" class="btn btn-outline-warning btn-sm flex-fill"
                                            :class="paymentTabName === 'pending' ? 'active' : ''"
                                            @click="typePayment('pending')">
                                            <i class="fa-solid fa-circle-exclamation me-1"></i> Pendente
                                        </button>
                                        <button type="button" class="btn btn-outline-info btn-sm flex-fill"
                                            :class="paymentTabName === 'free' ? 'active' : ''"
                                            @click="typePayment('free')">
                                            <i class="fa-regular fa-circle-check me-1"></i> Livre
                                        </button>
                                    </div>

                                    <div v-if="paymentTabName === 'paid'" class="bg-light p-3 rounded small">
                                        <input type="text" class="form-control form-control-sm mb-2"
                                            v-model="config.consultation_price" placeholder="Valor">
                                        <select class="form-select form-select-sm mb-2"
                                            v-model.number="finance.payment_method_id">
                                            <option value="" disabled>Forma de pagamento</option>
                                            <option v-for="method in paymentMethods" :key="method.id" :value="method.id">
                                                {{ method.label }}
                                            </option>
                                        </select>
                                        <input type="text" class="form-control form-control-sm mb-2"
                                            v-mask="'####,##'" v-model="finance.discount" placeholder="Desconto">

                                        <div v-if="finance.payment_method_id == 3" class="mt-3 p-3 bg-white rounded border">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" name="appointmentCreditType"
                                                    id="appointmentCreditCash" v-model="finance.credit_type" value="vista">
                                                <label class="form-check-label fw-medium" for="appointmentCreditCash">
                                                    A vista
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="appointmentCreditType"
                                                    id="appointmentCreditInstallments" v-model="finance.credit_type"
                                                    value="parcelado">
                                                <label class="form-check-label fw-medium" for="appointmentCreditInstallments">
                                                    Parcelado
                                                </label>
                                            </div>
                                            <select v-if="finance.credit_type === 'parcelado'"
                                                class="form-select form-select-sm mt-3" v-model.number="finance.installments">
                                                <option value="" disabled>Parcelas</option>
                                                <option v-for="n in 12" :key="n" :value="n">{{ n }}x</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div v-else-if="paymentTabName === 'pending'" class="bg-light p-3 rounded small">
                                        <input type="text" class="form-control form-control-sm mb-2"
                                            v-model="config.consultation_price" placeholder="Valor">
                                        <small class="text-muted">O financeiro recebera uma conta pendente vinculada a este paciente.</small>
                                    </div>
                                    <div v-else class="alert alert-info p-2 small mb-0">
                                        Nao registrar cobranca para este agendamento.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top d-flex justify-content-between">
                            <button class="btn btn-sm btn-light px-3" @click="changeTabName('listHours')">
                                <i class="fa-solid fa-arrow-left fa-xs me-1"></i> Voltar
                            </button>
                            <button v-if="!isPatientConfirmed"
                                class="btn btn-sm btn-primary px-4"
                                @click="confirmAppointment()" :disabled="isPatientConfirmed">
                                Confirmar consulta
                            </button>
                            <button v-else class="btn btn-sm btn-success px-4"
                                @click="initiateConsultation(patientSelected)">
                                Iniciar atendimento
                            </button>
                        </div>
                    </template>

                    <!-- Tab: cadastro de paciente -->
                    <template v-else>
                        <div class="modal-header border-bottom">
                            <h6 class="modal-title fw-semibold d-flex align-items-center gap-2">
                                <i class="fa-solid fa-user-plus fa-sm text-muted"></i>
                                Cadastrar paciente
                            </h6>
                            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4" style="max-height:65vh; overflow-y:auto;">
                            <form @submit.prevent="savePatient">

                                <p class="small fw-semibold text-uppercase text-muted mb-3 pb-2 border-bottom"
                                    style="letter-spacing:.05em; font-size:11px;">Dados pessoais</p>

                                <div class="row g-3 mb-4">
                                    <div class="col-12 col-md-8">
                                        <label class="form-label small fw-semibold text-uppercase text-muted">Nome completo</label>
                                        <input type="text" class="form-control form-control-sm"
                                            v-model="newPatient.full_name" required>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="form-label small fw-semibold text-uppercase text-muted">Idade</label>
                                        <input type="number" class="form-control form-control-sm"
                                            v-model.number="newPatient.age" min="0" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small fw-semibold text-uppercase text-muted">Faixa etária</label>
                                        <select class="form-select form-select-sm" v-model="newPatient.group" required>
                                            <option value="" disabled>Selecione</option>
                                            <option value="child">Criança</option>
                                            <option value="teen">Adolescente</option>
                                            <option value="adult">Adulto</option>
                                            <option value="elderly">Idoso</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small fw-semibold text-uppercase text-muted">Gênero</label>
                                        <select class="form-select form-select-sm" v-model="newPatient.gender" required>
                                            <option value="" disabled>Selecione</option>
                                            <option value="F">Feminino</option>
                                            <option value="M">Masculino</option>
                                            <option value="other">Outro</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small fw-semibold text-uppercase text-muted">CPF</label>
                                        <input type="text" class="form-control form-control-sm"
                                            v-model="newPatient.cpf" maxlength="14" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small fw-semibold text-uppercase text-muted">E-mail</label>
                                        <input type="email" class="form-control form-control-sm"
                                            v-model="newPatient.email" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small fw-semibold text-uppercase text-muted">Telefone</label>
                                        <input type="text" class="form-control form-control-sm"
                                            v-model="newPatient.phone" maxlength="20" required>
                                    </div>
                                </div>

                                <p class="small fw-semibold text-uppercase text-muted mb-3 pb-2 border-bottom"
                                    style="letter-spacing:.05em; font-size:11px;">Contatos</p>

                                <div class="row g-3 mb-4">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small fw-semibold text-uppercase text-muted">Nome do responsável</label>
                                        <input type="text" class="form-control form-control-sm"
                                            v-model="newPatient.guardian_name">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small fw-semibold text-uppercase text-muted">Telefone do responsável</label>
                                        <input type="text" class="form-control form-control-sm"
                                            v-model="newPatient.guardian_phone" maxlength="20">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small fw-semibold text-uppercase text-muted">Contato de emergência</label>
                                        <input type="text" class="form-control form-control-sm"
                                            v-model="newPatient.emergency_contact" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small fw-semibold text-uppercase text-muted">Telefone de emergência</label>
                                        <input type="text" class="form-control form-control-sm"
                                            v-model="newPatient.emergency_phone" maxlength="20" required>
                                    </div>
                                </div>

                                <p class="small fw-semibold text-uppercase text-muted mb-3 pb-2 border-bottom"
                                    style="letter-spacing:.05em; font-size:11px;">Observações</p>

                                <textarea class="form-control form-control-sm" v-model="newPatient.notes" rows="3"></textarea>

                            </form>
                        </div>
                        <div class="modal-footer border-top d-flex justify-content-between">
                            <button type="button" class="btn btn-sm btn-light px-3"
                                @click="changeTabName('listPatients')">
                                <i class="fa-solid fa-arrow-left fa-xs me-1"></i> Voltar
                            </button>
                            <button type="button" class="btn btn-sm btn-primary px-4" @click="savePatient">
                                Salvar paciente
                            </button>
                        </div>
                    </template>

                </div>
            </div>
        </div>

    </div>
</template>

<script>
import { Modal } from 'bootstrap';

export default {
    name: 'CalendarSchedule',
    props: {
        urlConfiguration: String,
        urlOpenConsultation: String,
    },
    data() {
        return {
            loading: false,
            sendingEmail: false,
            currentYear: new Date().getFullYear(),
            currentMonth: new Date().getMonth(),
            selectedDay: null,
            selectedDate: null,
            modalInstance: null,
            availableDays: [],
            schedule: {
                start_time: '',
                end_time: '',
                start_break_time: '',
                end_break_time: '',
            },
            availableTimes: [],
            tabName: 'listHours',
            patients: [],
            selectedTime: null,
            patientSelected: '',
            busyAppointments: [],
            newPatient: {
                group: '',
                gender: '',
                age: '',
                full_name: '',
                cpf: '',
                email: '',
                phone: '',
                guardian_name: '',
                guardian_phone: '',
                emergency_contact: '',
                emergency_phone: '',
                notes: ''
            },
            isPatientConfirmed: false,
            consultation_duration: '',
            selectedAppointment: null,
            config: {},
            paymentTabName: 'paid',
            paymentMethods: [],
            finance: {
                payment_method_id: 1,
                credit_type: '',
                installments: '',
                discount: 0,
            },
        };
    },
    computed: {
        daysOfWeekLabels() {
            return ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
        },
        monthDays() {
            const days = [];
            const date = new Date(this.currentYear, this.currentMonth, 1);
            while (date.getMonth() === this.currentMonth) {
                const yyyy = date.getFullYear();
                const mm = String(date.getMonth() + 1).padStart(2, '0');
                const dd = String(date.getDate()).padStart(2, '0');
                days.push(`${yyyy}-${mm}-${dd}`);
                date.setDate(date.getDate() + 1);
            }
            return days;
        },
        calendarDays() {
            const firstDate = this.monthDays[0] ? this.parseDate(this.monthDays[0]) : null;
            const firstDay = firstDate ? firstDate.getDay() : 0;
            const daysArray = this.monthDays.map(d => {
                const dateObj = this.parseDate(d);
                return { day: dateObj.getDate(), date: d };
            });
            return [...Array(firstDay).fill(null), ...daysArray];
        }
    },
    mounted() {
        this.findPatients();
        this.findAppointments();
        this.initModal();
        this.loadMySchedule();
        this.getTenantConfig();
        this.findPaymentMethods();
    },
    methods: {
        getTenantConfig() {
            this.loading = true;
            axiosTenant.get('/configuration/get-config')
                .then((response) => {
                    this.consultation_duration = response.data.config.consultation_duration;
                    this.config = response.data.config;
                })
                .catch((errros) => {
                    this.alertDanger(errros);
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        parseDate(str) {
            const [year, month, day] = str.split('-').map(Number);
            return new Date(year, month - 1, day);
        },
        prevMonth() {
            if (this.currentMonth === 0) {
                this.currentMonth = 11;
                this.currentYear--;
            } else this.currentMonth--;
        },
        nextMonth() {
            if (this.currentMonth === 11) {
                this.currentMonth = 0;
                this.currentYear++;
            } else this.currentMonth++;
        },
        formatMonthYear() {
            const months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
            return `${months[this.currentMonth]} ${this.currentYear}`;
        },
        translateAvailableDays(days) {
            const map = {
                monday: 'Segunda',
                tuesday: 'Terça',
                wednesday: 'Quarta',
                thursday: 'Quinta',
                friday: 'Sexta',
                saturday: 'Sábado',
                sunday: 'Domingo'
            };
            return days.map(d => map[d]);
        },
        initModal() {
            this.modalInstance = new Modal(document.getElementById('modalDay'));
        },
        findPatients() {
            this.loading = true;
            axiosTenant.get('/appointments/get-patients')
                .then(res => (this.patients = res.data.patients))
                .catch(err => console.error(err))
                .finally(() => (this.loading = false));
        },
        findAppointments() {
            this.loading = true;
            axiosTenant.get('/appointments/list')
                .then(res => {
                    this.busyAppointments = res.data.busyAppointments
                        ?? res.data.busyAppointiments
                        ?? [];
                })
                .catch(err => console.error(err))
                .finally(() => (this.loading = false));
        },
        findPaymentMethods() {
            axiosTenant.get('/payment-methods')
                .then(response => {
                    this.paymentMethods = response.data.data;
                })
                .catch(errors => {
                    this.alertDanger(errors);
                });
        },
        loadMySchedule() {
            this.loading = true;
            axiosTenant.get('/configuration/schedule/list')
                .then(res => {
                    const sched = res.data.schedule[0] || {};
                    this.availableDays = res.data.schedule.map(s => s.day_of_week.toLowerCase());
                    Object.assign(this.schedule, sched);
                })
                .catch(err => {
                    console.error(err);
                    this.availableDays = [];
                })
                .finally(() => (this.loading = false));
        },
        isToday(d) {
            return this.parseDate(d).toDateString() === new Date().toDateString();
        },
        isPastDay(d) {
            const date = this.parseDate(d);
            date.setHours(0, 0, 0, 0);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            return date < today;
        },
        isAvailableDay(d) {
            const labels = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
            return this.availableDays.includes(labels[this.parseDate(d).getDay()]);
        },
        openModal(day, date) {
            if (this.isPastDay(date) || !this.isAvailableDay(date)) return;
            this.selectedDay = day;
            this.selectedDate = date;
            this.calculateAvailableTimes();
            this.modalInstance.show();
        },
        calculateAvailableTimes() {
            this.availableTimes = [];
            if (!this.consultation_duration) return;
            if (!this.schedule.start_time || !this.schedule.end_time) return;

            const duration = parseInt(this.consultation_duration);

            const interval = duration;
            let start = this.timeToMinutes(this.schedule.start_time);
            const end = this.timeToMinutes(this.schedule.end_time);
            const breakStart = this.schedule.start_break_time ? this.timeToMinutes(this.schedule.start_break_time) : 0;
            const breakEnd = this.schedule.end_break_time ? this.timeToMinutes(this.schedule.end_break_time) : 0;
            while (start < end) {
                if (breakStart && start >= breakStart && start < breakEnd) {
                    start += interval;
                    continue;
                }
                this.availableTimes.push(this.minutesToTime(start));
                start += interval;
            }
        },
        timeToMinutes(t) {
            const [h, m] = t.split(':').map(Number);
            return h * 60 + m;
        },
        minutesToTime(m) {
            const h = String(Math.floor(m / 60)).padStart(2, '0');
            const min = String(m % 60).padStart(2, '0');
            return `${h}:${min}`;
        },
        selectTime(time, nextTab) {
            this.selectedTime = time;
            this.tabName = nextTab;
            const existing = this.busyAppointments.find(
                a => a.day === this.selectedDate && a.hour.startsWith(time)
            );
            this.selectedAppointment = existing || null; // Guarda o ID para cancelar sem depender do formato HH:mm/HH:mm:ss.
            this.patientSelected = existing ? existing.patient_id : '';
            this.isPatientConfirmed = !!existing;
        },
        isTimeBusy(time) {
            return this.busyAppointments.some(
                a => a.day === this.selectedDate && a.hour.startsWith(time)
            );
        },
        validateAvailableHours(times, date) {
            // agora mantém todos os horários, apenas filtra horários já passados no dia atual
            let filtered = [...times];
            if (date === new Date().toISOString().split('T')[0]) {
                const now = new Date();
                const curr = `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;
                filtered = filtered.filter(t => t >= curr);
            }
            return filtered;
        },
        closeModal() {
            this.tabName = 'listHours';
            this.selectedAppointment = null;
            this.modalInstance.hide();
        },
        changeTabName(name) {
            this.tabName = name;
        },
        confirmAppointment() {
            if (!this.patientSelected)
                return;

            if (!this.validateAppointmentPayment()) return;

            this.sendingEmail = true;
            axiosTenant.post('/appointments/create', {
                patient_id: this.patientSelected,
                day: this.selectedDate,
                hour: this.selectedTime,
                status: 'Open',
                finance: this.buildAppointmentFinancePayload(), // Estrutura pronta para contabilizar no modulo financeiro.
            }).then(() => {
                this.findAppointments();
                this.resetPaymentForm();
                this.closeModal();
            }).catch(e => console.log(e))
                .finally(() => (this.sendingEmail = false));
        },
        removePatient(id, day, time) {
            this.loading = true;
            axiosTenant
                .post('/appointments/remove-patient', {
                    appointment_id: this.selectedAppointment?.id, // Corrige cancelamento buscando pelo ID quando o horario salvo esta como HH:mm:ss.
                    patient_id: id,
                    day,
                    hour: time
                })
                .then(() => {
                    this.findAppointments();
                    this.closeModal();
                })
                .catch(err => console.error(err))
                .finally(() => (this.loading = false));
        },
        typePayment(tabValue) {
            this.paymentTabName = tabValue;
        },
        buildAppointmentFinancePayload() {
            const amount = this.normalizeCurrencyValue(this.config.consultation_price);
            const paymentConfig = {
                paid: {
                    payment_amount: amount,
                    payment_method_id: this.finance.payment_method_id,
                    payment_status: 'paid',
                },
                pending: {
                    payment_amount: amount,
                    payment_method_id: 'pending',
                    payment_status: 'pending',
                },
                free: {
                    payment_amount: 0,
                    payment_method_id: 'free',
                    payment_status: 'free',
                },
            };

            return {
                ...paymentConfig[this.paymentTabName],
                credit_type: this.finance.credit_type,
                installments: this.finance.installments,
                discount: this.normalizeCurrencyValue(this.finance.discount),
            };
        },
        validateAppointmentPayment() {
            if (this.paymentTabName === 'paid' && !this.finance.payment_method_id) {
                this.alertDanger('Selecione a forma de pagamento.');
                return false;
            }

            if (this.paymentTabName === 'paid' && this.finance.payment_method_id == 3 && !this.finance.credit_type) {
                this.alertDanger('Selecione o tipo de credito.');
                return false;
            }

            if (this.paymentTabName === 'paid' && this.finance.credit_type === 'parcelado' && !this.finance.installments) {
                this.alertDanger('Selecione a quantidade de parcelas.');
                return false;
            }

            return true;
        },
        normalizeCurrencyValue(value) {
            if (value === null || value === undefined || value === '') return 0;
            const normalized = String(value).includes(',')
                ? String(value).replace(/\./g, '').replace(',', '.')
                : String(value);
            return Number(normalized) || 0;
        },
        resetPaymentForm() {
            this.paymentTabName = 'paid';
            this.finance = {
                payment_method_id: 1,
                credit_type: '',
                installments: '',
                discount: 0,
            };
        },
        savePatient() {
            this.loading = true;
            axiosTenant
                .post('/patients/store', this.newPatient)
                .then(() => {
                    this.findPatients();
                    this.resetNewPatientForm();
                    this.changeTabName('listPatients');
                })
                .catch(err => console.error(err))
                .finally(() => (this.loading = false));
        },
        resetNewPatientForm() {
            this.newPatient = {
                group: '',
                gender: '',
                age: '',
                full_name: '',
                cpf: '',
                email: '',
                phone: '',
                guardian_name: '',
                guardian_phone: '',
                emergency_contact: '',
                emergency_phone: '',
                notes: ''
            };
        },
        hasAppointment(date) {
            return this.busyAppointments.some(a => a.day === date);
        },
        initiateConsultation(selectedAppointment) {
            this.loading = true;

            axiosTenant.get(`/appointments/find-for-consultation/${selectedAppointment}/${this.selectedDate}/${this.selectedTime}`)
                .then((response) => {
                    if (typeof response.data.appointment === 'string' || response.data.appointment === false) {
                        this.alertDanger(response.data.appointment || 'Você não tem permissão para acessar este agendamento.');
                        this.loading = false;
                        return Promise.reject();
                    } else {
                        const appointment = response.data.appointment;

                        const consultationData = {
                            tenant_id: appointment.tenant_id,
                            client_appointment_id: appointment.id,
                            patient_id: appointment.patient_id,
                            employee_id: appointment.employee_id ?? null,
                            day: appointment.day,
                            hour: appointment.hour,
                        };

                        return axiosTenant.post('/consultation/store', consultationData);
                    }
                })
                .then((response) => {
                    if (!response) return;

                    const id = response.data.consultation.id ?? '#';
                    const url = this.urlOpenConsultation.replace('_id', id);
                    window.location.href = url;
                })
                .catch((errors) => {
                    if (errors && errors.message !== '') {
                        this.alertDanger(errors.response?.data?.message || 'Erro ao iniciar consulta.');
                    }
                })
                .finally(() => {
                    this.loading = false;
                });
        },
    },
};
</script>

<style scoped>
/* Grid do calendário — não tem equivalente Bootstrap */
.cal-grid {
    border-radius: 0 0 12px 12px;
    overflow: hidden;
}

.cal-header,
.cal-body {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
}

.cal-header-cell {
    padding: 10px 4px;
    font-size: 11px;
    background: var(--bs-secondary-bg, #f8f9fa);
    border-bottom: 1px solid var(--bs-border-color, #dee2e6);
}

.cal-body {
    grid-auto-rows: minmax(80px, auto);
}

.cal-day {
    position: relative;
    border: 0.5px solid var(--bs-border-color, #dee2e6);
    padding: 6px 8px;
    background: #fff;
    transition: background 0.15s;
}

.cal-day-number {
    font-size: 13px;
    color: var(--bs-body-color);
}

/* Estados */
.cal-available {
    cursor: pointer;
    background: #EEF2FF;
    border-color: #C7D2FE;
}

.cal-available:hover {
    background: #E0E7FF;
}

.cal-today {
    background: #F0FDF4 !important;
    border-color: #86EFAC !important;
}

.cal-today .cal-day-number {
    color: #16A34A;
    font-weight: 700;
}

.cal-past {
    opacity: 0.45;
    cursor: not-allowed;
    background: #fafafa;
}

.cal-empty {
    background: #fafafa;
    border-color: transparent;
}

/* Indicador de agendamento */
.cal-dot {
    position: absolute;
    bottom: 8px;
    left: 8px;
    width: 7px;
    height: 7px;
}
</style>
