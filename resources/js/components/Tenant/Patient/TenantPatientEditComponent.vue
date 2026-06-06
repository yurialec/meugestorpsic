<template>
    <div class="container-fluid px-4 px-lg-5 py-4">

        <div v-if="loading" class="d-flex justify-content-center py-5">
            <div class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="visually-hidden"></span>
            </div>
        </div>

        <template v-else>

            <!-- Page Header -->
            <div class="d-flex align-items-center gap-2 mb-4">
                <a :href="urlIndexPatient"
                    class="btn btn-sm btn-light d-flex align-items-center justify-content-center p-0"
                    style="width:30px; height:30px;">
                    <i class="fa-solid fa-arrow-left fa-xs"></i>
                </a>
                <div>
                    <h4 class="mb-0 fw-semibold">Editar paciente</h4>
                </div>
            </div>

            <!-- Card -->
            <div class="card border shadow-none">
                <div class="card-header bg-transparent border-bottom d-flex align-items-center gap-2 py-3">
                    <span class="d-flex align-items-center justify-content-center rounded-2 p-1"
                        style="width:28px; height:28px; background:#EEF2FF;">
                        <i class="fa-solid fa-user-pen fa-xs" style="color:#4F46E5;"></i>
                    </span>
                    <div>
                        <h6 class="mb-0 fw-semibold">Dados pessoais</h6>
                    </div>
                </div>

                <div class="card-body px-4 py-4">
                    <form @submit.prevent="save()" autocomplete="off">

                        <!-- Identificação -->
                        <p class="small fw-semibold text-uppercase text-muted mb-3 pb-2 border-bottom"
                            style="letter-spacing:.05em; font-size:11px;">Identificação</p>

                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold text-uppercase text-muted">Nome
                                    completo</label>
                                <input type="text" class="form-control form-control-sm" v-model="patient.full_name"
                                    required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold text-uppercase text-muted">CPF</label>
                                <input type="text" class="form-control form-control-sm" v-model="patient.cpf"
                                    v-mask="'###.###.###-##'" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold text-uppercase text-muted">E-mail</label>
                                <input type="email" class="form-control form-control-sm" v-model="patient.email"
                                    @input="validateEmail" required>
                                <div v-if="validEmail === false" class="d-flex align-items-center gap-1 mt-1"
                                    style="font-size:12px; color:#991B1B;">
                                    <i class="fa-solid fa-circle-exclamation fa-xs"></i>
                                    E-mail inválido
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold text-uppercase text-muted">Telefone</label>
                                <input type="text" class="form-control form-control-sm" v-model="patient.phone"
                                    v-mask="['(##) ####-####', '(##) #####-####']" required>
                            </div>
                        </div>

                        <!-- Perfil -->
                        <p class="small fw-semibold text-uppercase text-muted mb-3 pb-2 border-bottom"
                            style="letter-spacing:.05em; font-size:11px;">Perfil</p>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold text-uppercase text-muted">Sexo</label>
                                <select class="form-select form-select-sm" v-model="patient.gender" required>
                                    <option value="" disabled>Selecione</option>
                                    <option v-for="(label, key) in getGender" :value="key">{{ label }}</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-semibold text-uppercase text-muted">Grupo</label>
                                <select class="form-select form-select-sm" v-model="patient.group" required>
                                    <option value="" disabled>Selecione</option>
                                    <option v-for="(label, key) in getGroups" :value="key">{{ label }}</option>
                                </select>
                            </div>
                        </div>

                    </form>
                </div>

                <div class="card-footer bg-transparent d-flex flex-column flex-sm-row gap-2 py-3 px-4">
                    <button class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 px-4 ms-auto" type="button"
                        @click="save()">
                        <i class="fa-solid fa-floppy-disk fa-xs"></i> Salvar alterações
                    </button>
                </div>
            </div>

        </template>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    props: {
        id: String,
        urlIndexPatient: String,
    },
    data() {
        return {
            loading: false,
            patient: {
                group: '',
                gender: '',
                age: '',
                full_name: '',
                cpf: '',
                email: '',
                phone: '',
            },
            validEmail: null,
        };
    },
    mounted() {
        this.getPatientById();
    },
    computed: {
        getGroups() {
            return {
                "child": 'Criança',
                "teen": 'Adolescente',
                "adult": 'Adulto',
                "elderly": 'Idoso',
            };
        },
        getGender() {
            return {
                "F": "Feminino",
                "M": "Masculino",
                "other": "Outros",
            }
        },
    },
    methods: {
        validateEmail() {
            const emailPattern = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
            this.validEmail = emailPattern.test(this.patient.email);
        },
        getPatientById() {
            this.loading = true;
            axiosTenant.get(`/patients/get-patient-by-id/${this.id}`)
                .then((response) => {
                    this.patient = response.data.patientById;
                })
                .catch((errors) => {
                    this.alertDanger(errors);
                    window.scrollTo(0, top);
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        save() {
            this.loading = true;
            axiosTenant.put(`/patients/update/${this.id}`, this.patient)
                .then((response) => {
                    this.alertSuccess('Operação realizada com sucesso!');
                    window.scrollTo(0, top);
                })
                .catch((errors) => {
                    this.alertDanger(errors);
                    window.scrollTo(0, top);
                })
                .finally(() => {
                    this.loading = false;
                });
        }
    }
}
</script>
