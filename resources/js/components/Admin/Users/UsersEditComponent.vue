<template>
    <div class="container-fluid px-4 mt-2">
        <div class="card">
            <div class="card-header">
                <h4>Editar Usuário</h4>
            </div>
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col-sm-6">
                        <div v-if="loading" class="d-flex justify-content-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <form v-else method="POST" action="" @submit.prevent="save()" class="col-lg-8"
                            autocomplete="off">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Nome</label>
                                        <input type="text" class="form-control" v-model="user.name" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Perfil</label>
                                        <select class="form-control" v-model="user.role_id" required>
                                            <option v-for="role in this.roles" :value="role.id">{{ role.name }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>E-mail</label>
                                        <input type="text" class="form-control" v-model="user.email"
                                            @input="validateEmail" autocomplete="off" required>
                                        <div style="margin-top: 10px;" v-if="validEmail === false"
                                            class="alert alert-danger" role="alert">
                                            E-mail inválido.
                                        </div>
                                    </div>
                                    <div class="form-group" v-show="changePassword">
                                        <hr>
                                        <div class="row">
                                            <div class="col-sm">
                                                <label>Senha</label>
                                                <input :type="inputPass ? 'text' : 'password'" class="form-control"
                                                    v-model="user.password" @input="passwordCheck" name="password">
                                            </div>
                                            <div class="col-sm">
                                                <label>Confirmar senha</label>
                                                <div class="input-group">
                                                    <input :type="inputPass ? 'text' : 'password'" class="form-control"
                                                        v-model="confirmPassword" name="confirmPassword">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <button class="btn btn-outline-secondary btn-sm"
                                                                type="button" @click="showPassword()"
                                                                id="button-addon2">
                                                                <i class="bi bi-eye"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm" style="margin-top: 5px;">
                                            <div>
                                                <h6>Requisitos mínimos para a senha:</h6>
                                            </div>
                                            <div>
                                                <small style="color: red; margin-bottom: 1px;" v-if="!has_six_chars">
                                                    No mínimo 6 caracteres.</small>
                                                <small style="color: green; margin-bottom: 1px;" v-else>
                                                    No mínimo 6 caracteres.
                                                </small>
                                                <br>
                                                <small style="color: red; margin-bottom: 1px;" v-if="!has_lowercase">
                                                    Conter pelo menos uma letra.
                                                </small>
                                                <small style="color: green; margin-bottom: 1px;" v-else>
                                                    Conter pelo menos uma letra.
                                                </small>
                                                <br>
                                                <small style="color: red; margin-bottom: 1px;" v-if="!has_number">
                                                    Conter pelo menos um número.
                                                </small>
                                                <small style="color: green; margin-bottom: 1px;" v-else>
                                                    Conter pelo menos um número.
                                                </small>
                                                <br>
                                                <small style="color: red; margin-bottom: 1px;" v-if="!has_special">
                                                    Conter pelo menos um caractere especial.
                                                </small>
                                                <small style="color: green; margin-bottom: 1px;" v-else>
                                                    Conter pelo menos um caractere especial.</small>
                                                <br>
                                                <small style="color: red; margin-bottom: 1px;"
                                                    v-if="user.password !== confirmPassword">
                                                    A confirmação de senha precisa ser igual a senha.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="container" style="margin-top: 10px;">
                                        <div class="row">
                                            <div class="col text-end">
                                                <button class="btn btn-primary btn-sm" @click.prevent="changePass()">
                                                    Alterar senha
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-12 col-md-6 text-center text-md-start mb-2 mb-md-0">
                                            <a :href="urlIndexUser"
                                                class="btn btn-secondary btn-sm w-100 w-md-auto">Voltar</a>
                                        </div>
                                        <div class="col-12 col-md-6 text-center text-md-end">
                                            <button class="btn btn-primary btn-sm w-100 w-md-auto"
                                                type="submit">Cadastrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    props: {
        id: String,
        urlIndexUser: String,
    },
    data() {
        return {
            loading: false,
            roles: [],
            user: {
                name: '',
                email: '',
                role_id: '',
                password: '',
                confirmPassword: '',
            },
            confirmPassword: '',
            inputPass: false,
            has_number: '',
            has_lowercase: '',
            has_special: '',
            has_six_chars: '',
            alertStatus: null,
            messages: [],
            changePassword: false,
            validEmail: null,
        };
    },
    mounted() {
        this.getRoles();
        this.find();
    },
    methods: {
        validateEmail() {
            const emailPattern = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
            this.validEmail = emailPattern.test(this.user.user.email);
        },
        find() {
            this.loading = true;
            axios.get('/admin/users/find/' + this.id)
                .then(response => {
                    this.user = response.data.user;
                }).catch(errors => {
                    this.alertDanger(errors);
                }).finally(() => {
                    this.loading = false;
                });
        },
        getRoles() {
            this.loading = true;
            axios.get('/admin/roles/list')
                .then(response => {
                    this.roles = response.data.roles.data;
                }).catch(errors => {
                    this.alertDanger(errors);
                }).finally(() => {
                    this.loading = false;
                });
        },
        save() {

            if (this.changePassword) {
                if (!this.user.password) {
                    this.alertDanger('Campo senha é obrigatório.');
                    return;
                }

                if (this.user.password !== this.confirmPassword) {
                    this.alertDanger('As senhas precisam ser iguais.');
                    return;
                }
            }

            let payload = {
                name: this.user.name,
                email: this.user.email,
                role_id: this.user.role_id,
            };

            if (this.changePassword) {
                payload.password = this.user.password;
                payload.confirmPassword = this.confirmPassword;
            }

            axios.put('/admin/users/update/' + this.id, payload)
                .then(response => {
                    this.alertSuccess('Operação realizada com sucesso!');
                    window.scrollTo(0, 0); 3
                }).catch(errors => {
                    this.alertDanger(errors);
                    window.scrollTo(0, 0); 3
                }).finally(() => {
                    this.loading = false;
                });
        },
        changePass() {
            this.changePassword = !this.changePassword;
            if (!this.changePassword) {
                this.user.password = '';
                this.confirmPassword = '';
            }
        },
        passwordCheck() {
            if (this.user.password) {
                this.has_number = /\d/.test(this.user.password);
                this.has_lowercase = /[a-zA-Z]/.test(this.user.password);
                this.has_special = /[!@#\$%\^\&*\)\(+=._-]/.test(this.user.password);
                this.has_six_chars = this.user.password.length >= 6;

                return true;
            }
        },
        showPassword() {
            this.inputPass = !this.inputPass;
        },
    }
}
</script>
