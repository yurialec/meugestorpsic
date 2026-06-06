<template>
    <div class="card">
        <div class="card-header">
            <h4>Cadastrar perfil</h4>
        </div>
        <div class="card-body">
            <div class="row justify-content-center">
                <div class="col-sm-6">
                    <form method="POST" action="" @submit.prevent="salvar()">
                        <div class="form-group">
                            <label>Nome</label>
                            <input type="text" class="form-control" v-model="role.name">
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="text-start">
                                    <a :href="this.urlIndexRole" class="btn btn-secondary btn-sm">Voltar</a>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-end">
                                    <a href="#" class="btn btn-primary btn-sm" @click="salvar()">Cadastrar</a>
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
        urlIndexRole: String,
    },
    data() {
        return {
            loading: false,
            role: {
                name: '',
            },
        };
    },
    methods: {
        salvar() {
            axios.post('/admin/roles/store', this.role)
                .then(response => {
                    this.alertSuccess('Operação realizada com sucesso!');
                })
                .catch(errors => {
                    this.alertDanger(errors);
                })
                .finally(() => {
                    this.loading = false;
                });
        }
    }
}
</script>