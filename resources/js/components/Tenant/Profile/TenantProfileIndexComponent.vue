<template>
    <div class="container-fluid py-4 px-4 px-lg-5">
        <div v-if="loading" class="d-flex justify-content-center my-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Carregando...</span>
            </div>
        </div>

        <template v-else>
            <!-- Page Header -->
            <div class="mb-4">
                <h4 class="mb-1 fw-semibold">Configurações</h4>
            </div>

            <div class="row g-4">

                <!-- Informações pessoais -->
                <div class="col-12 col-lg-6">
                    <div class="card border shadow-none h-100">
                        <div class="card-header bg-transparent border-bottom d-flex align-items-center gap-2 py-3">
                            <span class="d-flex align-items-center justify-content-center rounded-2 bg-light p-1">
                                <i class="fa-regular fa-user fa-sm text-muted"></i>
                            </span>
                            <div>
                                <h6 class="mb-0 fw-semibold">Informações pessoais</h6>
                            </div>
                        </div>
                        <div class="card-body">
                            <form @submit.prevent="updateProfile">
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold text-uppercase text-muted ls-1">Nome
                                        completo</label>
                                    <input type="text" class="form-control form-control-sm" v-model="profile.name"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold text-uppercase text-muted">E-mail</label>
                                    <input type="email" class="form-control form-control-sm" v-model="profile.email"
                                        required>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label
                                            class="form-label small fw-semibold text-uppercase text-muted">CPF</label>
                                        <input type="text" class="form-control form-control-sm" v-model="profile.cpf"
                                            disabled v-mask="'###.###.###-##'">
                                    </div>
                                    <div class="col-6">
                                        <label
                                            class="form-label small fw-semibold text-uppercase text-muted">CRP</label>
                                        <input type="text" class="form-control form-control-sm" v-model="profile.crp"
                                            disabled v-mask="'##/#######'">
                                    </div>
                                    <div class="col-6">
                                        <label
                                            class="form-label small fw-semibold text-uppercase text-muted">Telefone</label>
                                        <input type="tel" class="form-control form-control-sm" v-model="profile.phone"
                                            @input="formatPhoneInput">
                                    </div>
                                    <div class="col-6">
                                        <label
                                            class="form-label small fw-semibold text-uppercase text-muted">Domínio</label>
                                        <input type="text" class="form-control form-control-sm"
                                            v-model="profile.tenant.domain" disabled>
                                    </div>
                                </div>

                                <hr class="my-3">

                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small fw-semibold">Alterar senha</span>
                                    <button type="button"
                                        class="btn btn-link btn-sm p-0 text-primary text-decoration-none"
                                        @click="togglePasswordChange">
                                        {{ showPasswordChange ? 'Recolher' : 'Expandir' }}
                                    </button>
                                </div>

                                <div v-if="showPasswordChange" class="mt-3">
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-uppercase text-muted">Nova
                                            senha</label>
                                        <div class="input-group input-group-sm">
                                            <input :type="showPassword ? 'text' : 'password'" class="form-control"
                                                :class="{ 'is-invalid': passwordErrors.length > 0 }"
                                                v-model="passwordData.password" @input="validatePassword">
                                            <button class="btn btn-outline-secondary" type="button"
                                                @click="showPassword = !showPassword">
                                                <i class="fa-solid"
                                                    :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                                            </button>
                                        </div>
                                        <small v-for="(error, i) in passwordErrors" :key="i"
                                            class="text-danger d-block mt-1">{{ error }}</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-uppercase text-muted">Confirmar
                                            senha</label>
                                        <div class="input-group input-group-sm">
                                            <input :type="showPassword ? 'text' : 'password'" class="form-control"
                                                :class="{ 'is-invalid': confirmPasswordError }"
                                                v-model="passwordData.password_confirmation"
                                                @input="validatePasswordConfirmation">
                                            <button class="btn btn-outline-secondary" type="button"
                                                @click="showPassword = !showPassword">
                                                <i class="fa-solid"
                                                    :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                                            </button>
                                        </div>
                                        <small v-if="confirmPasswordError" class="text-danger d-block mt-1">{{
                                            confirmPasswordError }}</small>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Endereço + Logo -->
                <div class="col-12 col-lg-6">
                    <div class="card border shadow-none h-100">
                        <div class="card-header bg-transparent border-bottom d-flex align-items-center gap-2 py-3">
                            <span class="d-flex align-items-center justify-content-center rounded-2 bg-light p-1">
                                <i class="fa-regular fa-map fa-sm text-muted"></i>
                            </span>
                            <div>
                                <h6 class="mb-0 fw-semibold">Endereço</h6>
                            </div>
                        </div>
                        <div class="card-body">
                            <form autocomplete="off">
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold text-uppercase text-muted">Rua</label>
                                    <input type="text" class="form-control form-control-sm" v-model="address.street">
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label
                                            class="form-label small fw-semibold text-uppercase text-muted">Número</label>
                                        <input type="text" class="form-control form-control-sm"
                                            v-model="address.number">
                                    </div>
                                    <div class="col-6">
                                        <label
                                            class="form-label small fw-semibold text-uppercase text-muted">Complemento</label>
                                        <input type="text" class="form-control form-control-sm"
                                            v-model="address.complement">
                                    </div>
                                    <div class="col-6">
                                        <label
                                            class="form-label small fw-semibold text-uppercase text-muted">Bairro</label>
                                        <input type="text" class="form-control form-control-sm"
                                            v-model="address.neighborhood">
                                    </div>
                                    <div class="col-6">
                                        <label
                                            class="form-label small fw-semibold text-uppercase text-muted">Cidade</label>
                                        <input type="text" class="form-control form-control-sm" v-model="address.city">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-semibold text-uppercase text-muted">UF</label>
                                        <input type="text" class="form-control form-control-sm" v-model="address.state"
                                            maxlength="2">
                                    </div>
                                    <div class="col-8">
                                        <label
                                            class="form-label small fw-semibold text-uppercase text-muted">CEP</label>
                                        <input type="text" class="form-control form-control-sm"
                                            v-model="address.postal_code" placeholder="00000-000">
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="card border shadow-none">
                        <div class="card-header bg-transparent border-bottom d-flex align-items-center gap-2 py-3">
                            <span class="d-flex align-items-center justify-content-center rounded-2 bg-light p-1">
                                <i class="fa-regular fa-font-awesome fa-sm text-muted"></i>
                            </span>
                            <div>
                                <div class="mb-1">
                                    <p class="small fw-semibold mb-0">Logo do consultório</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="d-flex align-items-start gap-3 mt-2">
                                        <div class="border rounded-2 d-flex align-items-center justify-content-center overflow-hidden flex-shrink-0"
                                            style="width: 72px; height: 72px; background: #f8f9fa;">
                                            <img v-if="previewLogo" :src="previewLogo" class="img-fluid"
                                                style="max-height: 72px; object-fit: contain;">
                                            <img v-else-if="profile.tenant.logo"
                                                :src="'/storage/' + profile.tenant.logo" class="img-fluid"
                                                style="max-height: 72px; object-fit: contain;">
                                            <i v-else class="fa-regular fa-image fa-lg text-muted"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <input type="file" class="form-control form-control-sm" @change="loadImage"
                                                accept="image/png,image/jpeg">
                                            <small class="text-muted">PNG ou JPG, até 2MB. Recomendado
                                                400×400px.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configurações de consulta -->
                <div class="col-12 col-lg-6">
                    <div class="card border shadow-none">
                        <div class="card-header bg-transparent border-bottom d-flex align-items-center gap-2 py-3">
                            <span class="d-flex align-items-center justify-content-center rounded-2 bg-light p-1">
                                <i class="fa-regular fa-clock fa-sm text-muted"></i>
                            </span>
                            <div>
                                <h6 class="mb-0 fw-semibold">Configurações de consulta</h6>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="form-label small fw-semibold text-uppercase text-muted">Duração
                                        (min)</label>
                                    <input type="text" class="form-control form-control-sm"
                                        v-model="profile.config.consultation_duration" v-mask="'##'">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-semibold text-uppercase text-muted">Valor da
                                        sessão (R$)</label>
                                    <input type="number" class="form-control form-control-sm"
                                        v-model="profile.config.consultation_price">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Meus horários -->
                <div class="col-12 col-lg-6">
                    <div class="card border shadow-none">
                        <div class="card-header bg-transparent border-bottom d-flex align-items-center gap-2 py-3">
                            <span class="d-flex align-items-center justify-content-center rounded-2 bg-light p-1">
                                <i class="fa-regular fa-calendar fa-sm text-muted"></i>
                            </span>
                            <div>
                                <h6 class="mb-0 fw-semibold">Meus horários</h6>
                                <small class="text-muted">Disponibilidade para agendamentos</small>
                            </div>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-pills nav-fill bg-light rounded-2 p-1 mb-4">
                                <li class="nav-item">
                                    <a class="nav-link active rounded-2 py-1 small" data-bs-toggle="tab" href="#manual"
                                        @click="changeMyScheduleValue">Manual</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link rounded-2 py-1 small" data-bs-toggle="tab" href="#automatico"
                                        @click="changeMyScheduleValue">Automático</a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="manual">
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-uppercase text-muted">Dias da
                                            semana</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            <div v-for="(day, key) in daysOfWeek" :key="key">
                                                <input type="checkbox" class="btn-check" :id="'day-' + key"
                                                    v-model="schedule.selectedDays" :value="key" autocomplete="off">
                                                <label class="btn btn-outline-primary btn-sm rounded px-3"
                                                    :for="'day-' + key">{{ day }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label
                                                class="form-label small fw-semibold text-uppercase text-muted">Início</label>
                                            <input type="time" class="form-control form-control-sm"
                                                v-model="schedule.start_time">
                                        </div>
                                        <div class="col-6">
                                            <label
                                                class="form-label small fw-semibold text-uppercase text-muted">Término</label>
                                            <input type="time" class="form-control form-control-sm"
                                                v-model="schedule.end_time">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small fw-semibold text-uppercase text-muted">Início
                                                da pausa</label>
                                            <input type="time" class="form-control form-control-sm"
                                                v-model="schedule.start_break_time">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small fw-semibold text-uppercase text-muted">Fim da
                                                pausa</label>
                                            <input type="time" class="form-control form-control-sm"
                                                v-model="schedule.end_break_time">
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="automatico">
                                    <div class="alert alert-info border-0 small d-flex gap-2 align-items-start"
                                        role="alert">
                                        <i class="fa-solid fa-circle-info mt-1 flex-shrink-0"></i>
                                        <span>A agenda automática permite o agendamento de consultas 24 horas por dia, 7
                                            dias por semana, sem restrição de horário.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button class="btn btn-sm btn-primary d-grid align-items-center justify-content-center gap-1 px-4"
                        type="button" @click="saveAll()">
                        Salvar Configurações
                    </button>
                </div>

            </div>
        </template>
    </div>
</template>
<script>
export default {
    data() {
        return {
            loading: false,
            showPasswordChange: false,
            showPassword: false,
            myScheduleValue: 'manual',
            previewLogo: null,
            logoFile: null,
            profile: {
                name: '',
                email: '',
                phone: '',
                cpf: '',
                crp: '',
                config: {
                    consultation_duration: '',
                    consultation_price: '',
                },
                tenant: {
                    domain: '',
                    logo: null,
                    address: [],
                },
                schedules: [],
            },
            address: {
                street: '',
                number: '',
                complement: '',
                neighborhood: '',
                city: '',
                postal_code: '',
                state: '',
            },
            schedule: {
                start_time: '',
                end_time: '',
                start_break_time: '',
                end_break_time: '',
                selectedDays: [],
            },
            passwordData: {
                password: '',
                password_confirmation: '',
            },
            passwordErrors: [],
            confirmPasswordError: '',
        };
    },

    created() {
    },
    computed: {
        daysOfWeek() {
            return {
                "monday": "Segunda",
                "tuesday": "Terca",
                "wednesday": "Quarta",
                "thursday": "Quinta",
                "friday": "Sexta",
                "saturday": "Sabado",
                "sunday": "Domingo",
            }
        },
    },
    mounted() {
        this.find();
    },
    methods: {
        isFilled(value) {
            return String(value ?? '').trim() !== '';
        },
        validateRequiredFields() {
            if (!this.isFilled(this.profile.name)) {
                this.alertDanger('O campo Nome completo é obrigatório.');
                return false;
            }

            if (!this.isFilled(this.profile.email)) {
                this.alertDanger('O campo E-mail é obrigatório.');
                return false;
            }

            if (!this.isFilled(this.profile.phone)) {
                this.alertDanger('O campo Telefone é obrigatório.');
                return false;
            }

            if (!this.isFilled(this.address.street)) {
                this.alertDanger('O campo Rua é obrigatório.');
                return false;
            }

            if (!this.isFilled(this.address.number)) {
                this.alertDanger('O campo Número é obrigatório.');
                return false;
            }

            if (!this.isFilled(this.address.complement)) {
                this.alertDanger('O campo Complemento é obrigatório.');
                return false;
            }

            if (!this.isFilled(this.address.neighborhood)) {
                this.alertDanger('O campo Bairro é obrigatório.');
                return false;
            }

            if (!this.isFilled(this.address.city)) {
                this.alertDanger('O campo Cidade é obrigatório.');
                return false;
            }

            if (!this.isFilled(this.address.state)) {
                this.alertDanger('O campo UF é obrigatório.');
                return false;
            }

            if (!this.isFilled(this.address.postal_code)) {
                this.alertDanger('O campo CEP é obrigatório.');
                return false;
            }

            if (!this.isFilled(this.profile.config.consultation_duration)) {
                this.alertDanger('O campo Duração é obrigatório.');
                return false;
            }

            if (!this.isFilled(this.profile.config.consultation_price)) {
                this.alertDanger('O campo Valor da sessão é obrigatório.');
                return false;
            }

            if (this.myScheduleValue === 'manual') {
                if (!this.schedule.selectedDays.length) {
                    this.alertDanger('O campo Dias da semana é obrigatório.');
                    return false;
                }

                if (!this.isFilled(this.schedule.start_time)) {
                    this.alertDanger('O campo Início é obrigatório.');
                    return false;
                }

                if (!this.isFilled(this.schedule.end_time)) {
                    this.alertDanger('O campo Término é obrigatório.');
                    return false;
                }

                if (!this.isFilled(this.schedule.start_break_time)) {
                    this.alertDanger('O campo Início da pausa é obrigatório.');
                    return false;
                }

                if (!this.isFilled(this.schedule.end_break_time)) {
                    this.alertDanger('O campo Fim da pausa é obrigatório.');
                    return false;
                }
            }

            if (this.showPasswordChange) {
                if (!this.isFilled(this.passwordData.password)) {
                    this.alertDanger('O campo Nova senha é obrigatório.');
                    return false;
                }

                if (!this.isFilled(this.passwordData.password_confirmation)) {
                    this.alertDanger('O campo Confirmar senha é obrigatório.');
                    return false;
                }
            }

            return true;
        },
        find() {
            this.loading = true;
            axiosTenant.get('/view-profile')
                .then(({ data }) => {
                    this.profile = data.profile;
                    this.loadAddress();
                    this.loadScheduleData();
                })
                .catch(e => this.alertDanger(e))
                .finally(() => { this.loading = false; });
        },
        loadAddress() {
            const addr = this.profile.tenant?.address;
            if (!addr) return;
            this.address = {
                street: addr.street ?? '',
                number: addr.number ?? '',
                complement: addr.complement ?? '',
                neighborhood: addr.neighborhood ?? '',
                city: addr.city ?? '',
                postal_code: addr.postal_code ?? '',
                state: addr.state ?? '',
            };
        },
        loadScheduleData() {
            const schedules = this.profile.schedules;
            if (!Array.isArray(schedules) || !schedules.length) return;

            const first = schedules[0];
            this.schedule.selectedDays = schedules.map(s => s.day_of_week);
            this.schedule.start_time = this.formatTime(first.start_time);
            this.schedule.end_time = this.formatTime(first.end_time);
            this.schedule.start_break_time = this.formatTime(first.start_break_time);
            this.schedule.end_break_time = this.formatTime(first.end_break_time);
        },
        formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('pt-BR', { timeZone: 'UTC' });
        },
        formatPrice(price) {
            if (!price) return 'N/A';
            return `R$ ${parseFloat(price).toFixed(2).replace('.', ',')}`;
        },
        formatCpf(cpf) {
            if (cpf) {
                return `${cpf.slice(0, 3)}.${cpf.slice(3, 6)}.${cpf.slice(6, 9)}-${cpf.slice(9, 11)}`;
            }
        },
        formatPhone(phone) {
            if (phone) {
                return `(${phone.slice(0, 2)}) ${phone.slice(2, 7)}-${phone.slice(7, 11)}`;
            }
        },
        formatDayOfWeek(day) {
            const daysMap = {
                'sunday': 'Domingo',
                'monday': 'Segunda-feira',
                'tuesday': 'Terca-feira',
                'wednesday': 'Quarta-feira',
                'thursday': 'Quinta-feira',
                'friday': 'Sexta-feira',
                'saturday': 'Sabado'
            };
            return daysMap[day] || day;
        },
        formatTime(timeString) {
            if (!timeString) return '';
            return timeString.slice(0, 5);
        },
        formatCpfInput(event) {
            let value = event.target.value.replace(/\D/g, '');
            if (value.length > 11) value = value.substring(0, 11);

            if (value.length > 9) {
                value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
            } else if (value.length > 6) {
                value = value.replace(/(\d{3})(\d{3})(\d{3})/, '$1.$2.$3');
            } else if (value.length > 3) {
                value = value.replace(/(\d{3})(\d{3})/, '$1.$2');
            }

            this.profile.cpf = value;
        },
        formatPhoneInput(event) {
            let value = event.target.value.replace(/\D/g, '');
            if (value.length > 11) value = value.substring(0, 11);

            if (value.length > 6) {
                value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
            } else if (value.length > 2) {
                value = value.replace(/(\d{2})(\d{4})/, '($1) $2');
            } else if (value.length > 0) {
                value = `(${value}`;
            }

            this.profile.phone = value;
        },
        saveAll() {
            if (!this.validateRequiredFields()) {
                return;
            }

            if (this.showPasswordChange) {
                this.validatePassword();
                this.validatePasswordConfirmation();
                if (this.passwordErrors.length || this.confirmPasswordError) {
                    this.alertDanger('Corrija os erros da senha antes de salvar.');
                    return;
                }
            }

            // Schedule payload
            const scheduleData = this.myScheduleValue === 'automatico'
                ? {
                    selectedDays: ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
                    start_time: '00:00', end_time: '23:59',
                    start_break_time: '12:00', end_break_time: '12:01',
                }
                : {
                    selectedDays: this.schedule.selectedDays,
                    start_time: this.schedule.start_time,
                    end_time: this.schedule.end_time,
                    start_break_time: this.schedule.start_break_time,
                    end_break_time: this.schedule.end_break_time,
                };

            const formData = new FormData();

            formData.append('name', this.profile.name);
            formData.append('email', this.profile.email);
            formData.append('phone', this.profile.phone?.replace(/\D/g, '') ?? '');

            if (this.showPasswordChange) {
                formData.append('password', this.passwordData.password);
                formData.append('password_confirmation', this.passwordData.password_confirmation);
            }

            formData.append('address', JSON.stringify(this.address));

            formData.append('consultation_duration', this.profile.config.consultation_duration);
            formData.append('consultation_price', this.profile.config.consultation_price);

            formData.append('schedule', JSON.stringify(scheduleData));

            if (this.logoFile) {
                formData.append('logo', this.logoFile);
            }

            formData.append('_method', 'PUT');

            this.loading = true;
            axiosTenant.post('/configuration/update', formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            })
                .then(({ data }) => {
                    if (data.profile) {
                        this.profile = data.profile;
                        this.loadAddress();
                        this.loadScheduleData();
                    }
                    this.alertSuccess('Configurações salvas com sucesso!');
                    this.showPasswordChange = false;
                    this.passwordData = { password: '', password_confirmation: '' };
                    this.logoFile = null;
                    this.previewLogo = null;
                })
                .catch(e => this.alertDanger(e))
                .finally(() => { this.loading = false; });
        },
        togglePasswordChange() {
            this.showPasswordChange = !this.showPasswordChange;
        },
        validatePassword() {
            const p = this.passwordData.password;
            this.passwordErrors = [];
            if (!p) { this.passwordErrors.push('A senha é obrigatória.'); return; }
            if (p.length < 8) this.passwordErrors.push('Mínimo de 8 caracteres.');
            if (!/^(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[@$!%*?&])/.test(p))
                this.passwordErrors.push('A senha deve ter letra, número e caractere especial.');
            if (this.passwordData.password_confirmation) this.validatePasswordConfirmation();
        },
        validatePasswordConfirmation() {
            this.confirmPasswordError = this.passwordData.password_confirmation !== this.passwordData.password
                ? 'A confirmação não confere.'
                : '';
        },
        changeMyScheduleValue() {
            this.myScheduleValue = this.myScheduleValue === 'manual' ? 'automatico' : 'manual';
        },
        loadImage(e) {
            const file = e.target.files[0];
            if (!file) return;
            this.previewLogo = URL.createObjectURL(file);
            this.logoFile = file;
        },
    }
}
</script>
