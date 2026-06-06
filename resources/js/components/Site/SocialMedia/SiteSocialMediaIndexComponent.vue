<template>
    <div class="container-fluid px-4 mt-2">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-12 col-md-3 text-md-left text-center mb-2 mb-md-0">
                        <h3>Redes Sociais</h3>
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
                                <a :href="urlCreateSocialMedia" type="button"
                                    class="btn btn-primary btn-sm w-100 w-md-auto">
                                    Cadastrar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="loading === true" class="d-flex justify-content-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <div v-else class="card-body">
                <div v-if="this.socialMedia.length == 0" class="text-center">
                    <p>Nenhum resultado encontrado</p>
                </div>
                <div v-else class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th class="col-md-3">Url</th>
                                <th class="col-md-3">Icone</th>
                                <th class="col-md-3">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="social in this.socialMedia.data">
                                <td class="col-md-3"><a style="text-decoration: none;" target="_blank"
                                        :href="social.url">{{ social.name }}</a></td>
                                <td class="col-md-3">
                                    <i :class="social.icon"></i>
                                </td>
                                <td class="col-md-3">
                                    <a :href="'/admin/site/social-media/edit/' + social.id">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    &nbsp;&nbsp;&nbsp;
                                    <button type="button" style="color: red; padding: 0;" class="btn"
                                        @click="deleteRecord(social.id)">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { Modal } from 'bootstrap';

export default {
    props: {
        urlCreateSocialMedia: String,
    },
    data() {
        return {
            searchFilter: null,
            socialMediaToDelete: null,
            loading: false,
            socialMedia: [],
        };
    },
    mounted() {
        this.getSocialMedia();
    },
    methods: {
        getSocialMedia() {
            this.loading = true;
            axios.get('admin/site/social-media/list')
                .then(response => {
                    this.socialMedia = response.data.socialMedia;
                })
                .catch(errors => {
                    this.alertDanger(errors);
                }).finally(() => {
                    this.loading = false;
                });
        },
        deleteRecord(id) {
            this.confirmYesNo('Excluir?').then(() => {

                axios.delete('/admin/site/social-media/delete/' + id)
                    .then(response => {
                        this.getSocialMedia();
                        this.alertSuccess('Operação realizada com sucesso!');
                    })
                    .catch(errors => {
                        this.alertDanger(errors);
                        window.scrollTo(0, 0);
                    }).finally(() => {
                        this.loading = false;
                    });
            });
        },
    }
}
</script>