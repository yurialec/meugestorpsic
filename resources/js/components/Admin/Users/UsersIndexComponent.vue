<template>
    <div class="container-fluid px-4 mt-2">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-12 col-md-3 text-md-left text-center mb-2 mb-md-0">
                        <h3>Usuários</h3>
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
                                <a :href="urlCreateUser" type="button" class="btn btn-primary btn-sm w-100 w-md-auto">
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
                <div class="row justify-content-center">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Nome</th>
                                    <th scope="col">E-mail</th>
                                    <th scope="col">Perfil</th>
                                    <!-- Ajuste para responsividade -->
                                    <th class="d-none d-md-table-cell" scope="col">Ações</th>
                                    <th class="d-md-none" scope="col">Opções</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in users.data" :key="user.id">
                                    <th scope="row">{{ user.id }}</th>
                                    <td>{{ user.name }}</td>
                                    <td>{{ user.email }}</td>
                                    <td>{{ user.role.name }}</td>
                                    <td class="d-none d-md-table-cell">
                                        <a :href="'users/edit/' + user.id">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-link text-danger"
                                            @click="confirmarExclusao(user.id)" data-bs-toggle="modal"
                                            data-bs-target="#exampleModal">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </td>
                                    <td class="d-md-none">
                                        <div class="dropdown">
                                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                Opções
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" :href="'users/edit/' + user.id">
                                                        <i class="bi bi-pencil-square"></i> Editar
                                                    </a>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item text-danger" type="button"
                                                        @click="deleteRegister(user.id)" data-bs-toggle="modal"
                                                        data-bs-target="#exampleModal">
                                                        <i class="bi bi-trash3"></i> Excluir
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-center">
                        <li v-for="(link, key) in users.links" :key="key" class="page-item"
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
import { Modal } from 'bootstrap';
import { mapGetters, mapActions } from 'vuex';

export default {
    props: {
        urlCreateUser: String,
    },
    computed: {
    },
    data() {
        return {
            loading: false,
            users: {
                data: [],
                links: []
            },
            searchFilter: '',
        };
    },
    mounted() {
        this.getUsers();
    },
    methods: {
        pagination(url) {
            if (url) {
                this.getUsers(url);
            }
        },
        getUsers(url = '/admin/users/list') {
            this.loading = true;
            axios.get(url)
                .then(response => {
                    this.users = response.data.users;
                })
                .catch(errors => {
                    this.alertDanger(errors);
                }).finally(() => {
                    this.loading = false
                });
        },
        deleteRegister(id) {
            this.confirmYesNo('Excluir usuário?').then(() => {
                this.loading = true;
                axios.delete('/admin/users/delete/' + id)
                    .then(response => {
                        this.getUsers();
                        this.alertSuccess('Excluido com sucesso!');
                    })
                    .catch(errors => {
                        this.alertDanger(errors);
                    }).finally(() => {
                        this.loading = false;
                    });
            });
        },
    }
}
</script>