<template>
    <div class="container-fluid px-4 mt-2">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6 text-start">
                        <h3>Logotipo</h3>
                    </div>

                    <div class="col-md-6 text-end">
                        <a v-show="!logo?.id" :href="urlCreateLogo" type="button"
                            class="btn btn-primary btn-sm">Cadastrar</a>
                    </div>
                </div>
            </div>
            <div v-if="loading" class="d-flex justify-content-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <div v-else class="card-body">
                <div v-if="!logo?.id" class="text-center">
                    <p>Nenhum resultado encontrado</p>
                </div>
                <div v-else class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Preview</th>
                                <th scope="col">Nome</th>
                                <th scope="col">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <img :src="'/storage/' + logo.image" width="80">
                                </th>
                                <td>{{ logo.name }}</td>
                                <td>
                                    <button type="button" style="color: #333; padding: 0;" class="btn"
                                        data-bs-toggle="modal" data-bs-target="#viewImgModal">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    &nbsp;&nbsp;&nbsp;

                                    <a :href="urlUpdateLogo.replace('_id', logo.id)">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    &nbsp;&nbsp;&nbsp;

                                    <button type="button" style="color: red; padding: 0;" class="btn"
                                        @click="deleteRecord(logo.id)">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Modal de Visualização de Imagem -->
        <div class="modal fade" id="viewImgModal" tabindex="-1" aria-labelledby="viewImgModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div style="text-align: center;">
                            <img :src="'/storage/' + logo.image" width="450">
                            <small>Nome: {{ logo.name }}</small>
                        </div>
                    </div>
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
        urlCreateLogo: String,
        urlUpdateLogo: String,
    },
    data() {
        return {
            logoToDelete: null,
            loading: false,
            logo: {},
        };
    },
    mounted() {
        this.getLogo();
    },
    methods: {
        getLogo() {
            this.loading = true;
            axios.get('admin/site/logo/list')
                .then(response => {
                    this.logo = response.data.logo || {};
                })
                .catch(errors => {
                    this.$showWidget(errors, false);
                }).finally(() => {
                    this.loading = false;
                });
        },
        deleteRecord(id) {
            this.confirmYesNo('Excluir usuário?').then(() => {
                axios.delete('/admin/site/logo/delete/' + id)
                    .then(response => {
                        this.getLogo();
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
