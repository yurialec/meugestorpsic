<template>
    <div class="container-fluid px-4 mt-2">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-12 col-md-3 text-md-start text-center mb-2 mb-md-0">
                        <h3 class="mb-0">Clientes</h3>
                    </div>
                    <div class="col-12 col-md-6 mb-2 mb-md-0">
                        <div class="input-group">
                            <input type="text" class="form-control" v-model="searchFilter" placeholder="Pesquisar..." />
                            <button type="button" class="btn btn-primary" @click="pesquisar()">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-12 col-md-3 text-md-end text-center mb-2 mb-md-0">
                        <button class="btn btn-outline-success btn-sm" @click="downloadImportationModel()">
                            <i class="bi bi-download me-1"></i>Modelo de Importação
                        </button>
                    </div>
                </div>
            </div>
            <div v-if="loading" class="d-flex justify-content-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <div v-else class="card-body">
                <div v-if="clients?.data.length" class="row justify-content-center">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Tenant Id</th>
                                    <th scope="col">Nome</th>
                                    <th scope="col">E-mail</th>
                                    <th scope="col">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="client in clients.data" :key="client.id">
                                    <th scope="row">{{ client.id }}</th>
                                    <td>{{ client.tenant_id }}</td>
                                    <td>{{ client.name }}</td>
                                    <td>{{ client.email }}</td>
                                    <td>
                                        <a :href="'/admin/crm/clients/edit/' + client.id">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div v-if="clients?.links.length" class="card-footer">
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-center">
                        <li v-for="(link, key) in clients.links" :key="key" class="page-item"
                            :class="{ 'active': link.active }">
                            <a class="page-link" href="#" @click.prevent="pagination(link.url)" v-html="link.label"></a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    props: {
        urlCreateClient: String,
    },
    data() {
        return {
            loading: false,
            clients: {
                data: [],
                links: []
            },
            searchFilter: '',
        };
    },
    mounted() {
        this.getClients();
    },
    methods: {
        pesquisar() {
            this.getClients('admin/crm/clients/list', this.searchFilter);
        },
        pagination(url) {
            if (url) {
                this.getClients(url);
            }
        },
        getClients(url = '/admin/crm/clients/list') {
            this.loading = true;
            axios.get(url)
                .then(response => {
                    this.clients = response.data.clients;
                })
                .catch(errors => {
                    this.alertDanger(errors);
                }).finally(() => {
                    this.loading = false
                });
        },
        downloadImportationModel() {
            this.loading = true;

            axios.get('admin/crm/clients/download-importation-model', {
                responseType: 'blob'
            })
                .then(response => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'modelo_importacao.xlsx');
                    document.body.appendChild(link);
                    link.click();
                    link.remove();
                    window.URL.revokeObjectURL(url);
                })
                .catch(errors => {
                    this.alertDanger('Erro ao baixar o modelo de importação.');
                    console.error(errors);
                })
                .finally(() => {
                    this.loading = false;
                });
        }
    }
}
</script>