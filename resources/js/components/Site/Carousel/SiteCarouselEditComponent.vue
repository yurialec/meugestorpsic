<template>
    <div class="container-fluid px-4 mt-2">
        <div class="card">
            <div class="card-header">
                <h4>Editar Carousel</h4>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-center">
                    <div v-if="loading" class="d-flex justify-content-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <form v-else method="POST" action="" @submit.prevent="save()" class="col-lg-6" autocomplete="off">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>Imagem</label>
                                    <img v-show="!newImage" :src="'/storage/' + carousel.image"
                                        class="form-control mb-3" width="200">
                                    <img v-show="newImage" class="form-control mb-3" :src="urlImage" width="200">
                                    <input type="file" class="form-control mb-3" @change="loadImage">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Título</label>
                                    <input type="text" class="form-control" v-model="carousel.title">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Descrição</label>
                                    <input type="text" class="form-control" v-model="carousel.description">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Nome Link Externo</label>
                                    <input type="text" class="form-control" v-model="carousel.name_link">

                                    <small v-show="carousel?.url_link?.length > 1 && carousel?.name_link?.length == 0"
                                        style="color: red;">
                                        É necessário inserir o nome do link externo
                                    </small>
                                </div>
                                <div class="form-group mb-3">
                                    <label>URL Link Externo</label>
                                    <input type="text" class="form-control" v-model="carousel.url_link">

                                    <small v-show="carousel?.name_link?.length > 1 && carousel?.url_link.length == 0"
                                        style="color: red;">
                                        É necessário inserir o nome do link externo
                                    </small>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-12 col-md-6 text-center text-md-start mb-2 mb-md-0">
                                        <a :href="urlIndexCarousel"
                                            class="btn btn-secondary btn-sm w-100 w-md-auto">Voltar</a>
                                    </div>
                                    <div class="col-12 col-md-6 text-center text-md-end">
                                        <button class="btn btn-primary btn-sm w-100 w-md-auto"
                                            type="submit">Editar</button>
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
        id: String,
        urlIndexCarousel: String,
    },
    data() {
        return {
            loading: false,
            carousel: {},
            newImage: null,
            file: '',
            urlImage: null,
        };
    },
    mounted() {
        this.find();
    },
    methods: {
        find() {
            this.loading = true;
            axios.get(`/admin/site/carousel/find/${this.id}`)
                .then(response => {
                    this.carousel = response.data.carousel;
                })
                .catch((errors) => {
                    this.alertDanger(errors);
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        loadImage(e) {
            this.newImage = e.target.files[0];
            this.urlImage = URL.createObjectURL(this.newImage);
        },
        save() {
            let formData = new FormData();

            if (this.newImage) {
                formData.append('image', this.newImage);
            }

            if (this.carousel.title) {
                formData.append('title', this.carousel.title);
            }

            if (this.carousel.description) {
                formData.append('description', this.carousel.description);
            }

            if (this.carousel.url_link) {
                formData.append('url_link', this.carousel.url_link);
            }

            if (this.carousel.name_link) {
                formData.append('name_link', this.carousel.name_link);
            }

            axios.post(`/admin/site/carousel/update/${this.id}`, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            }).then(response => {
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
