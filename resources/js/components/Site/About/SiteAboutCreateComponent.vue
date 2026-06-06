<template>
    <div class="container-fluid px-4 mt-2">
        <div class="card">
            <div class="card-header">
                <h4>Cadastrar Sobre</h4>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-center">
                    <form method="POST" @submit.prevent="save" class="col-lg-6" autocomplete="off">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>Imagem</label>
                                    <img v-show="urlImage" class="form-control" :src="urlImage" width="200">
                                    <input type="file" required class="form-control" @change="loadImage">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Título</label>
                                    <input type="text" class="form-control" v-model="about.title">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Descrição</label>
                                    <textarea class="form-control" rows="5" v-model="about.description"></textarea>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-12 col-md-6 text-center text-md-start mb-2 mb-md-0">
                                        <a :href="urlIndexAbout"
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
        urlIndexAbout: String,
    },
    data() {
        return {
            loading: false,
            about: {
                title: '',
                description: '',
                image: null,
            },
            urlImage: null,
        };
    },
    methods: {
        loadImage(e) {
            this.about.image = e.target.files[0];
            this.urlImage = URL.createObjectURL(this.about.image);
        },
        save() {
            let formData = new FormData();

            if (this.about.image) {
                formData.append('image', this.about.image);
            }

            if (this.about.title) {
                formData.append('title', this.about.title);
            }

            if (this.about.description) {
                formData.append('description', this.about.description);
            }

            axios.post('/admin/site/site-about/store', formData, {
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
