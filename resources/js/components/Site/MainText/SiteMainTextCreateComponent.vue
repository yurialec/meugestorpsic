<template>
    <div class="container-fluid px-4 mt-2">
        <div class="card">
            <div class="card-header">
                <h4>Cadastrar</h4>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-center">
                    <div v-if="loading" class="d-flex justify-content-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <form v-else method="POST" @submit.prevent="save" class="col-lg-6" autocomplete="off">
                        <div class="row">
                            <div v-if="this.alertStatus == 'maxchars'"
                                class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fa-regular fa-circle-xmark"></i> Você atingiu o máximo de caracteres
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                            <div class="form-group">
                                <label>Titulo</label>
                                <input type="text" class="form-control" v-model="main.title">
                            </div>
                            <div class="form-group">
                                <label>Texto</label>
                                <textarea class="form-control" v-model="main.text"></textarea>

                                <small v-show="main.text.length > 255" style="color: red;">Você atingiu o máximo de
                                    caracteres</small>
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
        urlIndexMainText: String,
    },
    data() {
        return {
            main: {
                title: '',
                text: '',
            },
            loading: false,
            alertStatus: null,
            messages: [],
        };
    },
    methods: {
        save() {

            if (this.main.text.length > 255) {
                this.alertStatus = 'maxchars';
                return;
            }

            this.loading = true;
            axios.post('/admin/site/main-text/store', this.main)
                .then(response => {
                    this.alertSuccess('Operação realizada com sucesso!');
                })
                .catch(errors => {
                    this.alertDanger(errors);
                }).finally(() => {
                    this.loading = false;
                });
        },
    }
}
</script>
