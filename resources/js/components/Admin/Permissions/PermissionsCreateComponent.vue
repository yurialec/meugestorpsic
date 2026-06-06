<template>
    <div class="container-fluid px-4 mt-2">
        <div class="card">
            <div class="card-header">
                <h4>Cadastrar Permissões</h4>
            </div>
            <div v-if="loading" class="d-flex justify-content-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <div v-else class="card-body">
                <div class="row justify-content-center">
                    <form method="POST" @submit.prevent="save" class="col-lg-6" autocomplete="off">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Descrição</label>
                                    <input type="text" class="form-control" v-model="permission.label">
                                </div>
                                <div class="form-group">
                                    <label>Nome</label>
                                    <input type="text" class="form-control" v-model="permission.name">
                                </div>
                                <div class="row mt-4">
                                    <div class="col-12 col-md-6 text-center text-md-start mb-2 mb-md-0">
                                        <a :href="urlIndexPermission"
                                            class="btn btn-secondary btn-sm w-100 w-md-auto">Voltar</a>
                                    </div>
                                    <div class="col-12 col-md-6 text-center text-md-end">
                                        <button class="btn btn-primary btn-sm w-100 w-md-auto"
                                            type="submit">Cadastrar</button>
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

export default {
    props: {
        urlIndexPermission: String,
    },
    data() {
        return {
            loading: false,
            permission: {
                name: '',
                label: '',
            },
        };
    },
    mounted() {

    },
    methods: {
        save() {
            axios.post('/admin/permissions/store', this.permission)
                .then(response => {
                    this.alertSuccess('Operação realizada com sucesso!');
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