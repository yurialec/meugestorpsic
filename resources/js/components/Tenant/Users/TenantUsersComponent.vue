<template>
    <div class="container-fluid px-4 mt-2">
        <div v-if="loading" class="d-flex justify-content-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden"></span>
            </div>
        </div>
        <div v-else class="card">
            <div class="card-header">
                <div class="row align-items-center g-3">
                    <div class="col-12 col-md-3 text-md-start text-center">
                        <h5 class="mb-0">Usuários</h5>
                    </div>
                    <div class="col-12 col-md-5">
                        <div class="input-group">
                            <input type="text" class="form-control" v-model="searchFilter" placeholder="Nome" />
                            <button type="button" class="btn btn-primary" @click="search">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                        </div>
                    </div>
                    <div
                        class="col-12 col-md-4 d-flex flex-column flex-md-row gap-2 justify-content-center justify-content-md-end">
                        <a :href="urlCreateEmployee" class="btn btn-primary btn-sm w-100 w-md-auto">
                            <i class="fa-solid fa-plus me-1"></i>Cadastrar
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Nome</th>
                                <th scope="col">E-mail</th>
                                <th scope="col">Crp</th>
                                <th scope="col">Status</th>
                                <th scope="col">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="employee in this.employees.data">
                                <td>{{ employee.name }}</td>
                                <td>{{ employee.email }}</td>
                                <td>{{ employee.crp }}</td>
                                <td>
                                    <span v-if="employee.active" class="badge badge-success">Ativo</span>
                                    <span v-else class="badge badge-danger">Desativado</span>
                                </td>
                                <td>
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-cog me-1"></i> Opções
                                    </button>
                                    <ul class="dropdown-menu shadow-sm border-0">
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center py-2"
                                                :href="urlEditEmployee.replace('_id', employee.id)">
                                                <i class="fa-solid fa-pen-to-square text-warning me-2"></i>
                                                <span>Editar</span>
                                            </a>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item d-flex align-items-center py-2"
                                                @click="disable(employee.id)">
                                                <i class="fa-solid me-2"
                                                    :class="employee.active ? 'fa-circle-xmark text-danger' : 'fa-circle-check text-success'"></i>
                                                <span>{{ employee.active ? 'Desativar' : 'Ativar' }}</span>
                                            </button>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer py-2">
                    <nav v-if="employees.links.length > 0">
                        <ul class="pagination pagination-sm justify-content-center mb-0">
                            <li v-for="(link, i) in employees.links" :key="i"
                                :class="['page-item', { active: link.active, disabled: !link.url }]">
                                <a class="page-link" href="#" v-html="link.label"
                                    @click.prevent="pagination(link.url)"></a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    props: {
        urlCreateEmployee: String,
        urlEditEmployee: String,
    },
    data() {
        return {
            loading: false,
            employees: {
                data: [],
                links: []
            },
            searchFilter: '',
        };
    },
    mounted() {
        this.getEmployees();
    },
    methods: {
        search() {
            this.getEmployees('/employees/list', this.searchFilter);
        },
        pagination(url) {
            if (url) {
                this.getEmployees(url);
            }
        },
        getEmployees(url = '/employees/list', term = '') {
            this.loading = true;
            axiosTenant.post(url, { term })
                .then(response => {
                    this.employees = response.data.employee;
                })
                .catch(errors => {
                    this.alertDanger(errors);
                }).finally(() => {
                    this.loading = false
                });
        },
        disable(id) {
            axiosTenant.post(`/employees/disable/${id}`)
                .then(response => {
                    const index = this.employees.data.findIndex(emp => emp.id === response.data.employee.id);
                    if (index !== -1) {
                        this.employees.data[index] = response.data.employee;
                    }
                })
                .catch(errors => {
                    this.alertDanger(errors);
                }).finally(() => {
                });
        },
    }
}
</script>
