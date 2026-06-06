<template>
    <div class="container-fluid px-4 mt-2">
        <div class="card">
            <div class="card-header">
                <h4>Editar CRM Status</h4>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-center">

                    <div v-if="loading" class="d-flex justify-content-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden"></span>
                        </div>
                    </div>

                    <form v-else method="POST" action="" @submit.prevent="save()" class="col-lg-8" autocomplete="off">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Nome</label>
                                    <input type="text" class="form-control" v-model="status.name" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="colorPicker">Cor</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color"
                                            v-model="status.color" required title="Escolha uma cor">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12 col-md-6 text-center text-md-start mb-2 mb-md-0">
                                <a :href="urlIndexStatus" class="btn btn-secondary btn-sm w-100 w-md-auto">Voltar</a>
                            </div>
                            <div class="col-12 col-md-6 text-center text-md-end">
                                <button class="btn btn-primary btn-sm w-100 w-md-auto" type="submit">Editar</button>
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
import moment from 'moment';

export default {
    props: {
        id: Number,
        urlIndexStatus: String,
    },
    data() {
        return {
            loading: false,
            status: {
                name: '',
                color: '',
            },
        };
    },
    mounted() {
        this.find();
    },
    methods: {
        find() {
            this.loading = true;
            axios.get('/admin/crm/status/find/' + this.id)
                .then(response => {
                    this.status = response.data.sts;
                })
                .catch(errors => {
                    this.alertDanger(errors);
                }).finally(() => {
                    this.loading = false;
                });
        },
        save() {
            this.loading = true;
            axios.put('/admin/crm/status/update/' + this.id, this.status)
                .then(response => {
                    this.find();
                    this.alertSuccess('Status alterado com sucesso!');
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