<template>
    <div class="container-fluid py-3">
        <div v-if="loading" class="d-flex justify-content-center align-items-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Carregando...</span>
            </div>
        </div>

        <div v-else class="row g-4">
            <div class="col-12 col-xl-8">
                <div class="bg-white border rounded-3 overflow-hidden h-100">
                    <div class="border-bottom px-4 py-3 bg-light-subtle">
                        <h5 class="mb-1">Cadastrar funcionario</h5>
                        <p class="text-muted small mb-0">Organize os dados de acesso e os dados profissionais em um unico fluxo.</p>
                    </div>

                    <div class="p-4">
                        <form @submit.prevent="save" autocomplete="off">
                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="row g-3">
                                        <div class="col-12 col-md-6">
                                            <label class="form-label">Nome</label>
                                            <input type="text" class="form-control" v-model="employee.name" required>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" v-model="employee.email" required>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label">CPF</label>
                                            <input type="text" class="form-control" v-model="employee.cpf"
                                                v-mask="'###.###.###-##'" required>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label">CRP</label>
                                            <input type="text" class="form-control" v-model="employee.crp"
                                                v-mask="'##/#####'" required>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label">Telefone</label>
                                            <input type="tel" class="form-control" v-model="employee.phone"
                                                v-mask="['(##) ####-####', '(##) #####-####']" required>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label">Funcao</label>
                                            <input type="text" class="form-control" v-model="employee.function" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="border rounded-3 p-3 p-md-4 bg-light-subtle">
                                        <h6 class="mb-1">Acesso ao sistema</h6>
                                        <p class="text-muted small mb-3">Defina uma senha forte para o primeiro acesso.</p>

                                        <div class="row g-3">
                                            <div class="col-12 col-md-6">
                                                <label class="form-label">Senha</label>
                                                <div class="input-group">
                                                    <input :type="showPassword ? 'text' : 'password'" class="form-control"
                                                        v-model="employee.password"
                                                        :class="{ 'is-invalid': passwordErrors.length > 0 }"
                                                        @input="validatePassword" required>
                                                    <button class="btn btn-outline-secondary" type="button"
                                                        @click="showPassword = !showPassword">
                                                        <i class="fa-solid"
                                                            :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                                                    </button>
                                                </div>
                                                <small v-for="(error, i) in passwordErrors" :key="i"
                                                    class="text-danger d-block mt-1">{{ error }}</small>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="form-label">Confirmar senha</label>
                                                <div class="input-group">
                                                    <input :type="showPassword ? 'text' : 'password'" class="form-control"
                                                        v-model="employee.password_confirmation"
                                                        :class="{ 'is-invalid': confirmPasswordError }"
                                                        @input="validatePasswordConfirmation" required>
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
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-column flex-sm-row justify-content-between gap-2 mt-4 pt-3 border-top">
                                <a :href="urlEmployees" class="btn btn-outline-secondary btn-sm px-4">Voltar</a>
                                <button class="btn btn-primary btn-sm px-4" type="submit">
                                    Cadastrar funcionario
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="bg-white border rounded-3 p-4 h-100">
                    <div class="small text-muted text-uppercase mb-2">Resumo</div>
                    <h6 class="mb-3">Padrao de acesso</h6>
                    <div class="text-muted small d-grid gap-2">
                        <span>Use email profissional para recuperar acesso e receber comunicacoes.</span>
                        <span>Mantenha CPF e CRP completos para facilitar validacoes internas.</span>
                        <span>A senha deve atender aos criterios mostrados abaixo do campo.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
export default {
    props: {
        urlEmployees: String,
    },
    data() {
        return {
            loading: false,
            showPassword: false,
            passwordErrors: [],
            confirmPasswordError: '',
            employee: {
                name: '',
                email: '',
                cpf: '',
                crp: '',
                phone: '',
                function: '',
                password: '',
                password_confirmation: ''
            }
        }
    },
    methods: {
        formatPhoneInput() {
            let cleaned = this.employee.phone.replace(/\D/g, '');
            if (cleaned.length <= 10)
                this.employee.phone = cleaned.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
            else
                this.employee.phone = cleaned.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
        },
        validatePassword() {
            this.passwordErrors = [];
            const pass = this.employee.password;

            if (pass.length < 8)
                this.passwordErrors.push('A senha deve ter pelo menos 8 caracteres.');
            if (!/[A-Z]/.test(pass))
                this.passwordErrors.push('A senha deve conter pelo menos uma letra maiuscula.');
            if (!/[0-9]/.test(pass))
                this.passwordErrors.push('A senha deve conter pelo menos um numero.');
            if (!/[!@#$%^&*]/.test(pass))
                this.passwordErrors.push('A senha deve conter pelo menos um caractere especial.');
        },
        validatePasswordConfirmation() {
            this.confirmPasswordError =
                this.employee.password !== this.employee.password_confirmation
                    ? 'As senhas nao coincidem.'
                    : '';
        },
        save() {
            this.validatePassword();
            this.validatePasswordConfirmation();
            if (this.passwordErrors.length > 0 || this.confirmPasswordError) return;

            this.employee.crp = this.employee.crp.replace(/\D/g, '');

            this.loading = true;
            axiosTenant.post('/employees/store', this.employee)
                .then((response) => {
                    this.alertSuccess('Operacao realizada com sucesso!');
                    window.scrollTo(0, top);
                    this.clearForm();
                })
                .catch((errors) => {
                    this.alertDanger(errors);
                    window.scrollTo(0, top);
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        clearForm() {
            this.employee.name = '';
            this.employee.email = '';
            this.employee.cpf = '';
            this.employee.crp = '';
            this.employee.phone = '';
            this.employee.function = '';
            this.employee.password = '';
            this.employee.password_confirmation = '';
        }
    }
}
</script>
