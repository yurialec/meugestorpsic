<template>
    <div class="container-fluid px-4 mt-2">
        <div class="card">
            <div class="card-header">
                <h4>Cadastrar Carousel</h4>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-center">
                    <form method="POST" @submit.prevent="save" class="col-lg-8" autocomplete="off">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>Imagem</label>
                                    <input type="file" required class="form-control" @change="loadImage">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Título</label>
                                    <input type="text" class="form-control" v-model="carousel.title">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Texto</label>
                                    <textarea class="form-control" v-model="carousel.description"></textarea>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Nome Link Externo</label>
                                    <input type="text" class="form-control" v-model="carousel.name_link">
                                    <small v-show="carousel.url_link.length && !carousel.name_link" style="color: red;">
                                        É necessário inserir o nome do link externo
                                    </small>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Url Link Externo</label>
                                    <input type="text" class="form-control" v-model="carousel.url_link">
                                    <small v-show="carousel.name_link.length && !carousel.url_link" style="color: red;">
                                        É necessário inserir a url do link externo
                                    </small>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-12 col-md-6 text-center text-md-start mb-2 mb-md-0">
                                        <a :href="urlIndexCarousel"
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
        urlIndexCarousel: String,
    },
    data() {
        return {
            loading: false,
            carousel: {
                title: '',
                description: '',
                image: null,
                text: '',
                name_link: '',
                url_link: '',
            },
        };
    },
    methods: {
        loadImage(e) {
            this.carousel.image = e.target.files[0];
        },
        save() {
            let formData = new FormData();
            formData.append('title', this.carousel.title);
            formData.append('description', this.carousel.description);
            formData.append('name_link', this.carousel.name_link);
            formData.append('url_link', this.carousel.url_link);
            formData.append('image', this.carousel.image);

            axios.post('/admin/site/carousel/store', formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            })
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
