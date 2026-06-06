<template>
    <div class="container-fluid px-4 mt-2">
        <div class="card">
            <div class="card-header">
                <div class="container">
                    <div class="row">
                        <div class="col-sm">
                            <h4>Editar Rede Social</h4>
                        </div>
                        <div class="col-sm">
                            <a href="https://icons.getbootstrap.com/" target="_blank">Biblioteca de
                                ícones</a>&nbsp;&nbsp;
                            <i class="bi bi-info-circle fs-4" style="color: #00a803;" data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                title="Ao adicionar o nome do ícone, você deve inserir sem as tags HTML"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col-sm-6">
                        <div v-if="loading" class="d-flex justify-content-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <form v-else method="POST" action="" @submit.prevent="save()" class="col-lg-8"
                            autocomplete="off">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Nome</label>
                                        <input type="text" class="form-control" v-model="social.name">
                                    </div>
                                    <div class="form-group">
                                        <label>Ícone</label>
                                        <input type="text" class="form-control" v-model="social.icon">
                                    </div>
                                    <div class="form-group">
                                        <label>URL</label>
                                        <input type="text" class="form-control" v-model="social.url">
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-12 col-md-6 text-center text-md-start mb-2 mb-md-0">
                                            <a :href="urlIndexSocialMedia"
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
    </div>
</template>

<script>
import axios from 'axios';

export default {
    props: {
        id: String,
        urlIndexSocialMedia: String,
    },
    data() {
        return {
            social: {
                data: [],
                links: []
            },
            loading: false,
        };
    },
    mounted() {
        this.find();
    },
    methods: {
        find() {
            this.loading = true;
            axios.get('/admin/site/social-media/find/' + this.id)
                .then(response => {
                    this.social = response.data.social;
                })
                .catch(errors => {
                    this.alertDanger(errors);
                }).finally(() => {
                    this.loading = false;
                });
        },
        save() {
            this.loading = true;
            axios.post('/admin/site/social-media/update/' + this.id, this.social)
                .then(response => {
                    this.alertSuccess('Operação realizada com sucesso!');
                    window.scrollTo(0, 0);
                })
                .catch(errors => {
                    this.alertDanger(errors);
                    window.scrollTo(0, 0); 3
                }).finally(() => {
                    this.loading = false;
                });
        },
    }
}
</script>
