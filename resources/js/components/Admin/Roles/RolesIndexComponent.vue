<template>
    <div class="container-fluid px-4 mt-2">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-sm-3 text-left">
                        <h3>Perfis</h3>
                    </div>
                    <div class="col-sm-6">
                        <div class="input-group">
                            <input type="text" class="form-control" v-model="searchFilter" />
                            <button type="button" class="btn btn-primary" @click="pesquisar()">
                                <i class="bi bi-search"></i>
                            </button>
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
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nome</th>
                                <th>Permissões</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="role in this.roles.data" :key="role.id">
                                <th>{{ role.id }}</th>
                                <td>{{ role.name }}</td>
                                <td>
                                    <span v-for="permission in role['permissions']" class="badge bg-success"
                                        id="span-role-permissions">{{ permission.label }}</span>&nbsp;
                                </td>
                                <td>
                                    <a :href="'roles/edit/' + role.id">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div v-show="roles?.links.length > 10" class="card-footer">
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-center">
                        <li v-for="(link, key) in roles.links" :key="key" class="page-item"
                            :class="{ 'active': link.active }">
                            <a class="page-link" href="#" @click.prevent="paginacao(link.url)" v-html="link.label"></a>
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
        urlCreateRole: String,
    },
    data() {
        return {
            loading: false,
            roles: {
                data: [],
                links: []
            },
            searchFilter: '',
        };
    },
    mounted() {
        this.getRoles();
    },
    methods: {
        confirmarExclusao(roleId) {
            this.roleToDelete = roleId;
        },
        pesquisar() {
            this.getRoles(`/admin/roles/list?search=${this.searchFilter}`);
        },
        paginacao(url) {
            if (url) {
                this.getRoles(url);
            }
        },
        getRoles(url = '/admin/roles/list') {
            this.loading = true;
            axios.get(url)
                .then(response => {
                    this.roles = response.data.roles;
                })
                .catch(errors => {
                    this.alertDanger(errors);
                }).finally(() => {
                    this.loading = false;
                });
        }
    }
}
</script>