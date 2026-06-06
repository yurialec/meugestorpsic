<template>
    <div class="container-fluid px-4 mt-2">
        <div class="card">
            <div class="card-header">
                <h4>Cadastrar novo cliente</h4>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-center">
                    <form method="POST" action="" @submit.prevent="save()" class="col-lg-8" autocomplete="off">
                        <div v-if="equalPasswords === false" class="alert alert-danger alert-dismissible fade show"
                            role="alert">
                            <i class="fa-regular fa-circle-xmark"></i> As senhas precisam ser iguais
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nome</label>
                                    <input type="text" class="form-control" v-model="client.name" required>
                                </div>
                                <div class="form-group">
                                    <label>CPF</label>
                                    <input type="text" class="form-control" v-model="client.cpf" required
                                        v-mask="'###.###.###-##'">
                                </div>
                                <div class="form-group">
                                    <label>E-mail</label>
                                    <input type="text" class="form-control" v-model="client.email"
                                        @input="validateEmail" autocomplete="off" required>
                                    <div v-if="validEmail === false" class="alert alert-danger mt-3" role="alert">E-mail
                                        inválido.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Telefone</label>
                                    <input type="text" class="form-control" v-model="client.phone"
                                        v-mask="['(##) ####-####', '(##) #####-####']" required>
                                </div>
                                <div class="form-group">
                                    <label>Cargo/Função</label>
                                    <input type="text" class="form-control" v-model="client.function" required>
                                </div>
                                <div class="form-group">
                                    <label>CRP</label>
                                    <input type="text" class="form-control" v-model="client.domain" v-mask="'01/#####'"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div clas="form-group">
                                    <label>Planos</label>
                                    <select class="form-control" v-model="client.plan_id">
                                        <option v-for="plan in this.plans" :value="plan.id">{{ plan.name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm">
                                <label>Senha</label>
                                <input :type="inputPass ? 'text' : 'password'" class="form-control"
                                    v-model="client.password" @input="passwordCheck" autocomplete="new-password"
                                    required>
                            </div>
                            <div class="col-sm">
                                <label>Confirmar senha</label>
                                <div class="input-group">
                                    <input :type="inputPass ? 'text' : 'password'" class="form-control"
                                        v-model="confirmPassword" autocomplete="new-password">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <button class="btn btn-outline-secondary btn-sm" type="button"
                                                @click="showPassword()" id="button-addon2">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm mt-3">
                            <h6>Requisitos mínimos para a senha:</h6>
                            <small style="color: red;" v-if="!has_six_chars">No mínimo 6 caracteres.</small>
                            <small style="color: green;" v-else>No mínimo 6 caracteres.</small>
                            <br>
                            <small style="color: red;" v-if="!has_lowercase">Conter pelo menos uma letra.</small>
                            <small style="color: green;" v-else>Conter pelo menos uma letra.</small>
                            <br>
                            <small style="color: red;" v-if="!has_number">Conter pelo menos um número.</small>
                            <small style="color: green;" v-else>Conter pelo menos um número.</small>
                            <br>
                            <small style="color: red;" v-if="!has_special">Conter pelo menos um caractere
                                especial.</small>
                            <small style="color: green;" v-else>Conter pelo menos um caractere especial.</small>
                            <br>
                            <small style="color: red;" v-if="client.password !== confirmPassword">A confirmação de senha
                                precisa ser igual à senha.</small>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12 col-md-6 text-center text-md-start mb-2 mb-md-0">
                                <a :href="urlIndexClient" class="btn btn-secondary btn-sm w-100 w-md-auto">Voltar</a>
                            </div>
                            <div class="col-12 col-md-6 text-center text-md-end">
                                <button class="btn btn-primary btn-sm w-100 w-md-auto" type="submit">Cadastrar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    props: {
        urlIndexClient: String,
    },
    data() {
        return {
            loading: false,
            client: {
                name: '',
                cpf: '',
                email: '',
                domain: '',
                function: '',
                phone: '',
                plan_id: '',
            },
            validEmail: null,
            confirmPassword: '',
            inputPass: false,
            has_number: '',
            has_lowercase: '',
            has_special: '',
            has_six_chars: '',
            alertStatus: null,
            equalPasswords: null,
            messages: [],
            plans: [],
        };
    },
    mounted() {
        this.getPlans();
    },
    methods: {
        sanitizeInput(value) {
            return value.replace(/[^a-zA-Z0-9]/g, '');
        },
        validateEmail() {
            const emailPattern = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
            this.validEmail = emailPattern.test(this.client.email);
        },
        getPlans() {
            this.loading = true;
            let url = '/admin/financial/plan/list';
            axios.get(url)
                .then(response => {
                    this.plans = response.data.plans.data;
                })
                .catch(errors => {
                    this.alertDanger(errors);
                }).finally(() => {
                    this.loading = false
                });
        },
        save() {

            this.client.password == this.confirmPassword ? this.equalPasswords = true : this.equalPasswords = false;

            if (this.equalPasswords === false) {
                this.alertDanger('Senhas não conferem.');
                return;
            }

            let payLoad = {};
            payLoad.cpf = this.client.cpf.replace(/[^\d]/g, '');
            payLoad.email = this.client.email;
            payLoad.function = this.client.function;
            payLoad.name = this.client.name;
            payLoad.phone = this.client.phone.replace(/[^\d]/g, '');
            payLoad.domain = this.client.domain.replace(/[^\d]/g, '');
            payLoad.plan_id = this.client.plan_id;
            payLoad.password = this.client.password;

            axios.post('/admin/clients/store', payLoad)
                .then(response => {
                    this.alertSuccess('Operação realizada com sucesso!');
                    this.clearForm();
                    window.scrollTo(0, 0);
                })
                .catch(errors => {
                    this.alertDanger(errors);
                    window.scrollTo(0, 0); 3
                });
        },
        passwordCheck() {
            if (this.client.password) {
                this.has_number = /\d/.test(this.client.password);
                this.has_lowercase = /[a-zA-Z]/.test(this.client.password);
                this.has_special = /[!@#\$%\^\&*\)\(+=._-]/.test(this.client.password);
                this.has_six_chars = this.client.password.length >= 6;
            }
        },
        showPassword() {
            this.inputPass = !this.inputPass;
        },
        clearForm() {
            this.client.name = '';
            this.client.cpf = '';
            this.client.email = '';
            this.client.domain = '';
            this.client.function = '';
            this.client.phone = '';
            this.client.password = '';
            this.client.plan_id = '';
        },
    }
}
</script>