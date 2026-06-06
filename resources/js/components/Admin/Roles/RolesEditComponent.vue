<template>
    <div class="container-fluid px-4 mt-2">
        <div class="card">
            <div class="card-header">
                <h4>Editar perfil</h4>
            </div>
            <div v-if="loading" class="d-flex justify-content-center py-4">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <div v-else class="card-body">
                <div class="row justify-content-center">
                    <form method="POST" @submit.prevent="salvar" class="col-lg-6" autocomplete="off">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>Nome</label>
                                    <input type="text" class="form-control" disabled v-model="role.name">
                                </div>

                                <div class="form-group mb-3">
                                    <label>Permissões</label>
                                    <multiselect v-model="role.permissionsSelected" :options="permissions"
                                        :multiple="true" label="label" track-by="id">
                                    </multiselect>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12 col-md-6 text-center text-md-start mb-2 mb-md-0">
                                        <a :href="urlIndexRole" class="btn btn-secondary btn-sm w-100 w-md-auto">
                                            Voltar
                                        </a>
                                    </div>
                                    <div class="col-12 col-md-6 text-center text-md-end">
                                        <button class="btn btn-primary btn-sm w-100 w-md-auto" type="submit">
                                            Salvar Alterações
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Multiselect from 'vue-multiselect';

export default {
    components: { Multiselect },
    props: {
        id: String,
        urlIndexRole: String,
    },
    data() {
        return {
            role: {
                permissionsSelected: []
            },
            permissions: [],
            loading: false,
        };
    },
    mounted() {
        Promise.all([
            this.find(),
            this.getPermissions()
        ]).then(() => {
            this.setSelectedPermissions();
        }).finally(() => {
            this.loading = false;
        });
    },
    methods: {
        find() {
            return axios.get(`/admin/roles/find/${this.id}`)
                .then(response => {
                    this.role = {
                        ...response.data.role,
                        permissionsSelected: []
                    };
                }).catch(errors => {
                    this.alertDanger(errors);
                });
        },
        getPermissions() {
            return axios.get('/admin/roles/list-permissions')
                .then(response => {
                    this.permissions = response.data.permissions;
                }).catch(errors => {
                    this.alertDanger(errors);
                });
        },
        setSelectedPermissions() {
            if (!this.role.permissions || !Array.isArray(this.role.permissions)) {
                this.role.permissionsSelected = [];
                return;
            }

            const selectedPermissions = this.role.permissions.map(permission => {
                return this.permissions.find(p => p.id === permission.id);
            }).filter(Boolean);

            this.role.permissionsSelected = selectedPermissions;
        },
        salvar() {
            const permissionsIds = (this.role.permissionsSelected || []).map(permission => permission.id);

            const dataToSend = {
                role_id: this.role.id,
                name: this.role.name,
                permissions: permissionsIds
            };

            this.loading = true;
            axios.post('/admin/roles/update/' + this.role.id, dataToSend)
                .then(() => {
                    this.alertSuccess('Alterado com sucesso!');
                }).catch(errors => {
                    this.alertDanger(errors);
                }).finally(() => {
                    this.loading = false;
                });
        }
    }
}
</script>
