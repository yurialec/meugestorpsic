<template>
    <div class="container-fluid px-4 mt-2">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6 text-start">
                        <h3>Carousel</h3>
                    </div>
                    <div class="col-md-6 text-end">
                        <a :href="urlCreateCarousel" type="button" class="btn btn-primary btn-sm">Cadastrar</a>
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
                    <table class="table table-sm table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Preview</th>
                                <th>Título</th>
                                <th class="d-none d-md-table-cell">Descrição</th>
                                <th class="d-none d-md-table-cell">Nome Link Externo</th>
                                <th class="d-none d-md-table-cell">URL Link Externo</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="carousel in carousels" :key="carousel.id">
                                <td>
                                    <img :src="'/storage/' + carousel.image" alt="Imagem" width="80"
                                        class="img-fluid rounded shadow-sm">
                                </td>
                                <td class="text-truncate" style="max-width: 150px;">
                                    {{ carousel.title }}
                                </td>
                                <td class="d-none d-md-table-cell text-truncate" style="max-width: 200px;">
                                    {{ carousel.description }}
                                </td>
                                <td class="d-none d-md-table-cell text-truncate" style="max-width: 180px;">
                                    {{ carousel.name_link }}
                                </td>
                                <td class="d-none d-md-table-cell text-truncate" style="max-width: 220px;">
                                    {{ carousel.url_link }}
                                </td>
                                <td class="text-end">
                                    <div class="btn-group" role="group" aria-label="Ações">
                                        <button type="button" class="btn btn-sm text-secondary" data-bs-toggle="modal"
                                            data-bs-target="#viewImgModal" @click="viewImage(carousel)"
                                            aria-label="Visualizar imagem">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <a :href="urlUpdateCarousel.replace('_id', carousel.id)"
                                            class="btn btn-sm text-primary" aria-label="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm text-danger"
                                            @click="deleteRecord(carousel.id)" aria-label="Excluir">
                                            <i class="bi bi-trash3"></i>
                                        </button>
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
                            <img :src="'/storage/' + selectedCarousel.image" width="450">
                            <small>Nome: {{ selectedCarousel.name_link }}</small>
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
        urlCreateCarousel: String,
        urlUpdateCarousel: String,
    },
    data() {
        return {
            carouselToDelete: null,
            loading: false,
            carousels: [],
            selectedCarousel: {},
        };
    },
    mounted() {
        this.getCarousel();
    },
    methods: {
        getCarousel() {
            this.loading = true;
            axios.get('admin/site/carousel/list')
                .then(response => {
                    this.carousels = response.data.carousels;
                })
                .catch(errors => {
                    this.alertDanger(errors);
                }).finally(() => {
                    this.loading = false;
                });
        },
        deleteRecord(id) {
            this.loading = true;
            this.confirmYesNo('Excluir?').then(() => {
                axios.delete('/admin/site/carousel/delete/' + this.carouselToDelete)
                    .then(response => {
                        this.getCarousel();
                        this.alertSuccess('Operação realizada com sucesso!');
                    })
                    .catch(errors => {
                        this.alertDanger(errors);
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            });
        },
        viewImage(carousel) {
            this.selectedCarousel = carousel;
        },
    }
}
</script>
