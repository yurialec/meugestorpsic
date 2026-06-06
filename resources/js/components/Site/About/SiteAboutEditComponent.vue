<template>
    <div class="container-fluid px-4 mt-2">
        <div class="card">
            <div class="card-header">
                <h4>Editar Sobre</h4>
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
                                    <img v-show="!newImage" :src="'/storage/' + about.image" class="form-control mb-3"
                                        width="100">
                                    <img v-show="newImage" class="form-control mb-3" :src="urlImage" width="200">
                                    <input type="file" class="form-control mb-3" @change="loadImage">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Título</label>
                                    <input type="text" class="form-control" v-model="about.title">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Descrição</label>
                                    <input type="text" class="form-control" v-model="about.description">
                                </div>
                                <div class="row mt-4">
                                    <div class="col-12 col-md-6 text-center text-md-start mb-2 mb-md-0">
                                        <a :href="urlIndexAbout"
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
        urlIndexAbout: String,
    },
    data() {
        return {
            loading: false,
            about: {},
            newImage: null,
            urlImage: null,
        };
    },
    mounted() {
        this.find();
    },
    methods: {
        loadImage(e) {
            this.newImage = e.target.files[0];
            this.urlImage = URL.createObjectURL(this.newImage);
        },
        find() {
            this.loading = true;
            axios.get(`/admin/site/site-about/find/${this.id}`)
                .then(response => {
                    this.about = response.data.about;
                }).catch(errors => {
                    this.alertDanger(errors);
                }).finally(() => {
                    this.loading = false;
                });
        },
        save() {
            let formData = new FormData();

            if (this.newImage) {
                formData.append('image', this.newImage);
            }

            if (this.about.title) {
                formData.append('title', this.about.title);
            }

            if (this.about.description) {
                formData.append('description', this.about.description);
            }

            axios.post('/admin/site/site-about/update/' + this.id, formData, {
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
