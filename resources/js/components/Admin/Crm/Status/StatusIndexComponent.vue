<template>
    <div class="container-fluid px-4 mt-2">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-12 col-md-3 text-md-left text-center mb-2 mb-md-0">
                        <h3>CRM Status</h3>
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
                                <a :href="urlCreateStatus" type="button" class="btn btn-primary btn-sm w-100 w-md-auto">
                                    Cadastrar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="loading" class="d-flex justify-content-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <div v-else class="card-body">
                <div v-if="statuses.length" class="row justify-content-center">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="width: 25%">Nome</th>
                                    <th style="width: 30%">Cor</th>
                                    <th style="width: 35%; text-align: right;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="status in statuses" :key="status.id">
                                    <th scope="row">{{ status.id }}</th>
                                    <th scope="row">{{ status.name }}</th>
                                    <th scope="row" :style="{
                                        backgroundColor: status.color,
                                        color: getContrastColor(status.color),
                                        borderRadius: '10px',
                                        width: '15px'
                                    }">
                                        {{ status.color }}
                                    </th>
                                    <td style="text-align: right;">
                                        <a :href="'/admin/crm/status/edit/' + status.id">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        &nbsp;&nbsp;&nbsp;
                                        <button type="button" style="color: red; padding: 0;" class="btn"
                                            @click="deleteRecord(status.id)" data-bs-toggle="modal"
                                            data-bs-target="#exampleModal">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
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
        urlCreateStatus: String,
    },
    data() {
        return {
            loading: false,
            statuses: {
                data: [],
                links: []
            },
            searchFilter: '',
            statusToDelete: null,
        };
    },
    mounted() {
        this.getCrmStatuses();
    },
    methods: {
        pesquisar() {
            this.getCrmStatuses('admin/status/list', this.searchFilter);
        },
        pagination(url) {
            if (url) {
                this.getCrmStatuses(url);
            }
        },
        getCrmStatuses(url = 'admin/crm/status/list') {
            this.loading = true;
            axios.get(url)
                .then(response => {
                    this.statuses = response.data.statuses;
                })
                .catch(errors => {
                    this.alertDanger(errors);
                }).finally(() => {
                    this.loading = false
                });
        },
        deleteRecord(id) {
            this.confirmYesNo('Excluir status?').then(() => {
                axios.delete(`/admin/crm/status/delete/${id}`)
                    .then(() => {
                        this.getCrmStatuses();
                        this.alertSuccess('Excluido com sucesso!');
                    })
                    .catch(errors => {
                        this.alertDanger(errors);
                    }).finally(() => {
                        this.loading = false;
                    });
            });
        },
        getContrastColor(hexColor) {
            hexColor = hexColor.replace('#', '');
            const r = parseInt(hexColor.substr(0, 2), 16);
            const g = parseInt(hexColor.substr(2, 2), 16);
            const b = parseInt(hexColor.substr(4, 2), 16);
            const brightness = (r * 299 + g * 587 + b * 114) / 1000;
            return brightness > 128 ? '#000000' : '#FFFFFF';
        }
    }
}
</script>