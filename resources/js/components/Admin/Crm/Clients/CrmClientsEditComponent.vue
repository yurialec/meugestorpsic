<template>
    <div class="container-fluid px-4 mt-2">
        <div class="card">
            <div class="card-header">
                <h4>Editar Cliente</h4>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-center">
                    <div v-if="loading" class="d-flex justify-content-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden"></span>
                        </div>
                    </div>
                    <form v-else method="POST" action="" @submit.prevent="save()" class="col-lg-8 col-md-10 col-sm-12"
                        autocomplete="off">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Nome</label>
                                    <input type="text" class="form-control" v-model="client.name" required>
                                </div>
                                <div class="form-group">
                                    <label>E-mail</label>
                                    <input type="text" class="form-control" v-model="client.email"
                                        @input="validateEmail" autocomplete="off" required>
                                    <div v-if="validEmail === false" class="alert alert-danger mt-3" role="alert">E-mail
                                        inválido.</div>
                                </div>
                                <div class="form-group">
                                    <label>Telefone</label>
                                    <input type="text" class="form-control" v-model="client.phone"
                                        v-mask="['(##) ####-####', '(##) #####-####']" required>
                                </div>
                            </div>
                            <div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>CPF</label>
                                        <input type="text" class="form-control" v-model="client.cpf" required
                                            v-mask="'###.###.###-##'">
                                    </div>
                                    <div class="form-group">
                                        <label>Cargo/Função</label>
                                        <input type="text" class="form-control" v-model="client.function" required>
                                    </div>
                                    <div class="form-group">
                                        <label>CRP</label>
                                        <input v-mask="'01/#####'" disabled class="form-control" type="text"
                                            v-model="client.tenant.domain" placeholder="01/#####" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Planos</label>
                                        <select class="form-control" v-model="client.tenant.subscription.plan.id">
                                            <option v-for="plan in this.plans" :value="plan.id">{{ plan.name }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="row mt-4">
                            <div class="col-12 col-md-6 text-center text-md-start mb-2 mb-md-0">
                                <a :href="urlIndexClient" class="btn btn-secondary btn-sm w-100 w-md-auto">Voltar</a>
                            </div>
                            <div class="col-12 col-md-6 text-center text-md-end">
                                <button class="btn btn-primary btn-sm w-100 w-md-auto" type="submit">
                                    Editar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card shadow mt-4">
            <div class="card-body">
                <h5>Assinatura</h5>
                <p>Status: {{ client.tenant.subscription.status == 'Active' ? 'Ativo' : 'Inativo' }}</p>
                <p>Início: {{ formatDate(client.tenant.subscription.start_date) }}</p>
                <p>Fim: {{ formatDate(client.tenant.subscription.end_date) }}</p>
                <h5>Plano</h5>
                <p>Nome: {{ client.tenant.subscription.plan.name }}</p>
                <p>Preço: R$ {{ client.tenant.subscription.plan.price }}</p>
                <p><strong>{{ client.tenant.subscription.plan.description }}</strong></p>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import moment from 'moment';

export default {
    props: {
        id: Number,
        urlIndexClient: String,
    },
    data() {
        return {
            loading: false,
            client: {
                cpf: '',
                created_at: '',
                email: '',
                function: '',
                name: '',
                phone: '',
                tenant: {
                    domain: '',
                    subscription: {
                        status: '',
                        start_date: '',
                        end_date: '',
                        plan: {
                            id: '',
                            name: '',
                            price: '',
                            duration: '',
                            duration_type: ''
                        }
                    }
                }
            },
            validEmail: null,
            plans: [],
        };
    },
    mounted() {
        this.getPlans();
        this.find();
    },
    methods: {
        validateEmail() {
            const emailPattern = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
            this.validEmail = emailPattern.test(this.client.email);
        },
        getPlans() {
            this.loading = true;
            axios.get('/admin/financial/plan/list')
                .then(response => {
                    this.plans = response.data.plans;
                })
                .catch(errors => {
                    this.alertDanger(errors);
                }).finally(() => {
                    this.loading = false
                });
        },
        find() {
            this.loading = true;
            axios.get('/admin/crm/clients/find/' + this.id)
                .then(response => {
                    const clientData = response.data.client;
                    if (!clientData.tenant) {
                        clientData.tenant = {
                            domain: '',
                            subscription: {
                                status: '',
                                start_date: '',
                                end_date: '',
                                plan: {
                                    id: '',
                                    name: '',
                                    price: '',
                                    duration: '',
                                    duration_type: ''
                                }
                            }
                        };
                    }

                    this.client = clientData;
                })
                .catch(errors => {
                    this.alertDanger(errors);
                }).finally(() => {
                    this.loading = false;
                });
        },
        save() {
            this.loading = true;

            let payLoad = {};
            payLoad.cpf = this.client.cpf.replace(/[^\d]/g, '');
            payLoad.email = this.client.email;
            payLoad.function = this.client.function;
            payLoad.name = this.client.name;
            payLoad.phone = this.client.phone.replace(/[^\d]/g, '');
            payLoad.domain = this.client.tenant.domain.replace(/[^\d]/g, '');
            payLoad.plan_id = this.client.tenant.subscription.plan.id;

            axios.put('/admin/crm/clients/update/' + this.id, payLoad)
                .then(response => {
                    this.find();
                    this.alertSuccess('Operação realizada com sucesso!');
                })
                .catch(errors => {
                    this.alertDanger(errors);
                }).finally(() => {
                    this.loading = false;
                });
        },
        formatDate(date) {
            return moment(date).format('DD/MM/YYYY');
        },
    }
}
</script>