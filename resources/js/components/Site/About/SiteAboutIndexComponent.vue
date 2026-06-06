<template>
    <div class="container-fluid px-4 mt-2">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6 text-start">
                        <h3>Sobre</h3>
                    </div>
                    <div v-show="!about" class="col-md-6 text-end">
                        <a :href="urlCreateAbout" type="button" class="btn btn-primary btn-sm">Cadastrar</a>
                    </div>
                </div>
            </div>
            <div v-if="loading === true" class="d-flex justify-content-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <div v-else class="card-body">
                <div v-if="!about" class="text-center">
                    <p>Nenhum resultado encontrado</p>
                </div>
                <div v-else class="table-responsive">
                    <table class="table table-sm table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Preview</th>
                                <th>Título</th>
                                <th class="d-none d-md-table-cell">Descrição</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <img :src="'/storage/' + about.image" alt="Preview" width="80"
                                        class="img-fluid rounded shadow-sm">
                                </td>
                                <td>{{ about.title }}</td>
                                <td class="d-none d-md-table-cell text-truncate" style="max-width: 250px;">
                                    {{ about.description }}
                                </td>
                                <td class="text-end">
                                    <div class="d-none d-md-inline-flex align-items-center">
                                        <button type="button" class="btn btn-sm text-secondary me-2"
                                            data-bs-toggle="modal" data-bs-target="#viewImgModal"
                                            @click="viewImage(about)" aria-label="Visualizar imagem">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <a :href="urlUpdateAbout.replace('_id', about.id)"
                                            class="btn btn-sm text-primary me-2" aria-label="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm text-danger" data-bs-toggle="modal"
                                            data-bs-target="#exampleModal" @click="confirmExclusion(about.id)"
                                            aria-label="Excluir">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </div>

                                    <div class="dropdown d-inline-block d-md-none">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            Opções
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <button class="dropdown-item" @click="viewImage(about)"
                                                    data-bs-toggle="modal" data-bs-target="#viewImgModal">
                                                    <i class="bi bi-eye me-1"></i> Visualizar
                                                </button>
                                            </li>
                                            <li>
                                                <a class="dropdown-item"
                                                    :href="urlUpdateAbout.replace('_id', about.id)">
                                                    <i class="bi bi-pencil-square me-1"></i> Editar
                                                </a>
                                            </li>
                                            <li>
                                                <button class="dropdown-item text-danger"
                                                    @click="deleteRecord(about.id)" data-bs-toggle="modal"
                                                    data-bs-target="#exampleModal">
                                                    <i class="bi bi-trash3 me-1"></i> Excluir
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
        <div class="modal fade" id="viewImgModal" tabindex="-1" aria-labelledby="viewImgModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewImgModalLabel">Visualizar imagem</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div style="text-align: center;">
                            <img :src="'/storage/' + selectedAbout.image" width="450">
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
        urlCreateAbout: String,
        urlUpdateAbout: String,
    },
    data() {
        return {
            loading: false,
            about: null,
            selectedAbout: {},
        };
    },
    mounted() {
        this.getAbout();
    },
    methods: {
        getAbout() {
            this.loading = true;
            axios.get('admin/site/site-about/list')
                .then(response => {
                    this.about = response.data.about;
                })
                .catch(errors => {
                    this.alertDanger(errors);
                }).finally(() => {
                    this.loading = false;
                });
        },
        deleteRecord(id) {
            this.confirmYesNo('Excluir usuário?').then(() => {
                axios.delete('/admin/site/site-about/delete/' + id)
                    .then(response => {
                        this.getAbout();
                        this.alertSuccess('Operação realizada com sucesso!');
                    })
                    .catch(errors => {
                        this.alertDanger(errors);
                    }).finally(() => {
                        this.loading = false;
                    });
            });
        },
        viewImage(about) {
            this.selectedAbout = about;
        },
    }
}
</script>
