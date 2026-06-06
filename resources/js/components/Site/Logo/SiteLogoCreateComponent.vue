<template>
    <div class="container-fluid px-4 mt-2">
        <div class="card">
            <div class="card-header">
                <h4>Cadastrar logotipo</h4>
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
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Nome</label>
                                    <input type="text" class="form-control" v-model="logo.name">
                                </div>
                                <div class="form-group">
                                    <label>Imagem</label>
                                    <input type="file" class="form-control" @change="loadImage">
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-12 col-md-6 text-center text-md-start mb-2 mb-md-0">
                                    <a :href="urlIndexLogo" class="btn btn-secondary btn-sm w-100 w-md-auto">Voltar</a>
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
        urlIndexLogo: String,
    },
    data() {
        return {
            logo: {
                name: '',
                imageFile: null,
            },
            alertStatus: null,
            loading: false,
        };
    },
    methods: {
        loadImage(e) {
            this.logo.imageFile = e.target.files[0];
        },
        save() {
            let formData = new FormData();
            formData.append('name', this.logo.name);
            formData.append('image', this.logo.imageFile);

            this.loading = true;
            axios.post('/admin/site/logo/store', formData, {
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
