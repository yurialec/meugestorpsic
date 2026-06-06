<template>
    <div class="container-fluid px-4 mt-2">
        <div class="card">
            <div class="card-header">
                <h4>Editar Conteúdo</h4>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-center">
                    <div v-if="loading" class="d-flex justify-content-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <form v-else method="POST" @submit.prevent="save()" class="col-lg-12" autocomplete="off">

                        <div class="row">
                            <div class="form-group">
                                <label>Título</label>
                                <input type="text" class="form-control" v-model="main.title">
                            </div>
                            <div class="form-group">
                                <label>Texto</label>
                                <textarea class="form-control" rows="5" v-model="main.text"></textarea>
                            </div>
                            <div class="row mt-4">
                                <div class="col-12 col-md-6 text-center text-md-start mb-2 mb-md-0">
                                    <a :href="urlIndexMainText"
                                        class="btn btn-secondary btn-sm w-100 w-md-auto">Voltar</a>
                                </div>
                                <div class="col-12 col-md-6 text-center text-md-end">
                                    <button class="btn btn-primary btn-sm w-100 w-md-auto"
                                        type="submit">Cadastrar</button>
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
        id: String,
        urlIndexMainText: String,
    },
    data() {
        return {
            main: {},
            loading: false,
        };
    },
    mounted() {
        this.find();
    },
    methods: {
        find() {
            this.loading = true;
            axios.get('/admin/site/main-text/find')
                .then(response => {
                    this.main = response.data.main;
                }).catch((errors) => {
                    this.alertDanger(errors);
                }).finally(() => {
                    this.loading = false;
                });
        },
        save() {
            this.loading = true;
            axios.post('/admin/site/main-text/update/' + this.main.id, this.main)
                .then(response => {
                    this.alertSuccess('Operação realizada com sucesso!');
                }).catch(errors => {
                    this.alertDanger(errors);
                }).finally(() => {
                    this.loading = false;
                });
        },
    }
}
</script>
