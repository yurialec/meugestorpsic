<template>
    <div class="container-fluid px-4 mt-2">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-12 col-md-3 text-md-left text-center mb-2 mb-md-0">
                        <h3>Menus</h3>
                    </div>
                    <div class="col-12 col-md-6 text-center mb-2 mb-md-0">
                        <div class="input-group">
                            <input type="text" class="form-control" v-model="searchFilter" />
                            <button type="button" class="btn btn-primary" @click="pesquisar()">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-12 col-md-3 text-md-end text-end">
                        <a :href="urlCreateMenu" type="button" class="btn btn-primary btn-sm">Cadastrar</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div v-if="loading" class="d-flex justify-content-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <table v-else class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nome</th>
                            <th scope="col">Ícone</th>
                            <th scope="col">Url</th>
                            <th scope="col">Ordem</th>
                            <th scope="col">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="menu in menus.data" :key="menu.id">
                            <th scope="row">{{ menu.id }}</th>
                            <td>{{ menu.label }}</td>
                            <td><i :class="menu.icon"></i></td>
                            <td>{{ menu.url }}</td>
                            <td>{{ menu.order }}&nbsp;
                                <button v-if="menu.order != 1" @click="changeOrderMenu(menu.id)" class="btn btn-sm">
                                    <i class="bi bi-chevron-double-up"></i>
                                </button>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-danger me-1" @click="confirmExclusion(menu.id)">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <a :href="urlEditMenu.replace('_id', menu.id)" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </td>
                        </tr>
                        <tr v-if="menus.data.length === 0">
                            <td colspan="4" class="text-center">Nenhum registro encontrado.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-center">
                        <li v-for="(link, key) in menus.links" :key="key" class="page-item"
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

export default {
    props: {
        urlCreateMenu: String,
        urlEditMenu: String,
    },
    data() {
        return {
            loading: false,
            menus: {
                data: [],
                links: []
            },
            searchFilter: '',
        };
    },
    mounted() {
        this.getMenus();
    },
    methods: {
        pesquisar() {
            this.getMenus('admin/menu/list', this.searchFilter);
        },
        pagination(url) {
            if (url) {
                this.getMenus(url);
            }
        },
        getMenus(url = 'admin/menu/list') {
            this.loading = true;
            axios.get(url)
                .then(response => {
                    this.menus = response.data.menus;
                })
                .catch(errors => {
                    this.alertDanger(errors);
                }).finally(() => {
                    this.loading = false;
                });
        },
        deleteRedcord(id) {
            this.confirmYesNo('Excluir usuário?').then(() => {
                axios.delete('/admin/menu/delete/' + this.menuToDelete)
                    .then(response => {
                        this.getMenus();
                        this.alertSuccess('Excluido com sucesso!');
                    })
                    .catch(errors => {
                        this.alertDanger(errors);
                    }).finally(() => {
                        this.loading = false;
                    });
            });
        },
        changeOrderMenu(menuId) {
            axios.post('/admin/menu/change-order-menu/' + menuId)
                .then(response => {
                    this.getMenus();
                    this.alertSuccess('Menu alterado com sucesso');
                })
                .catch(errors => {
                    this.alertDanger(errors);
                }).finally(() => {
                    this.loading = false;
                });
        }
    },
}
</script>