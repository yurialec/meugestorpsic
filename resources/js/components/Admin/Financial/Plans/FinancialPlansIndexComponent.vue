<template>
    <div class="container-fluid px-4 mt-2">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-12 col-md-3 text-md-left text-center mb-2 mb-md-0">
                        <h3>Planos</h3>
                    </div>
                    <div class="col-12 col-md-9">
                        <div class="row align-items-center">
                            <div class="col-12 col-md-8 mb-2 mb-md-0">
                                <div class="input-group">
                                    <input type="text" class="form-control" v-model="searchFilter"
                                        placeholder="Pesquisar..." />
                                    <button type="button" class="btn btn-primary" @click="pesquisar()">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-12 col-md-2 text-md-end text-center">
                                <a :href="urlCreatePlan" type="button" class="btn btn-primary btn-sm w-100 w-md-auto">
                                    Cadastrar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="loading" class="d-flex justify-content-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden"></span>
                </div>
            </div>

            <div v-else class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Nome</th>
                                <th scope="col">Preço</th>
                                <th scope="col">Duração</th>
                                <th scope="col">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="plan in plans" :key="plan.id">
                                <th scope="row">{{ plan.id }}</th>
                                <td>{{ plan.name }}</td>
                                <td>{{ plan.price }}</td>
                                <td>{{ plan.billing_cycle }}</td>
                                <td>
                                    <a :href="'/admin/financial/plan/edit/' + plan.id">
                                        <i class="bi bi-pencil-square text-warning"></i>
                                    </a>
                                    &nbsp;&nbsp;&nbsp;
                                    <button type="button" class="btn p-0" @click="disable(plan)">
                                        <i class="bi bi-trash3 text-danger"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { Modal } from 'bootstrap';

export default {
    props: {
        urlCreatePlan: String,
    },
    data() {
        return {
            loading: false,
            plans: {},
            searchFilter: '',
        };
    },
    mounted() {
        this.getPlans();
    },
    methods: {
        pesquisar() {
            this.getPlans('/admin/financial/plan/list', this.searchFilter);
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
                    this.loading = false;
                });
        },
    }
}
</script>