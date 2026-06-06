<template>
    <div class="container-fluid px-4 mt-2">
        <div class="card">

            <div class="card-header">
                <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
                    <div class="text-center text-md-start">
                        <h3 class="mb-0">Editar Menu</h3>
                    </div>
                    <div class="d-flex flex-column flex-md-row align-items-center gap-2">
                        <a href="https://icons.getbootstrap.com/" target="_blank" class="text-nowrap">
                            Biblioteca de ícones
                        </a>
                        <i class="bi bi-info-circle fs-4" style="color: #00a803;" data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="Ao adicionar o nome do ícone, você deve inserir sem as tags HTML"
                            aria-label="Informação sobre ícones"></i>
                    </div>
                </div>
            </div>

            <div v-if="loading" class="d-flex justify-content-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden"></span>
                </div>
            </div>

            <div v-else class="card-body">
                <div class="row justify-content-center">
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-md-12">
                                <form method="POST" @submit.prevent="save()" class="col-lg-8" autocomplete="off">
                                    <div class="form-group">
                                        <label>Nome</label>
                                        <input type="text" class="form-control" v-model="menu.label">
                                    </div>
                                    <div class="form-group">
                                        <label>Ícone</label>
                                        <input type="text" class="form-control" v-model="menu.icon">
                                    </div>
                                    <div class="form-group">
                                        <label>Url</label>
                                        <input type="text" class="form-control" v-model="menu.url">
                                    </div>
                                    <div class="container mt-3">
                                        <div class="row">
                                            <div class="col-sm text-start">
                                                Adicionar submenu
                                            </div>
                                            <div class="col-sm text-end">
                                                <button type="button" @click="addRow" class="btn btn-primary">
                                                    <i class="bi bi-plus-square"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group row mt-3" v-for="(child, index) in menu.children"
                                        :key="index">
                                        <div class="col-lg-3">
                                            <input type="text" :name="'children[' + index + '][label]'"
                                                class="form-control" placeholder="Nome" v-model="child.label">
                                        </div>
                                        <div class="col-lg-4">
                                            <input type="text" :name="'children[' + index + '][icon]'"
                                                class="form-control" placeholder="Ícone" v-model="child.icon">
                                        </div>
                                        <div class="col-lg-4">
                                            <input type="text" :name="'children[' + index + '][url]'"
                                                class="form-control" placeholder="URL" v-model="child.url">
                                        </div>
                                        <div class="col-lg-1">
                                            <button type="button" @click="deleteRow(index)"
                                                class="btn btn-outline-danger rounded-circle">
                                                <i class="bi bi-x-circle-fill"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-12 col-md-6 text-center text-md-start mb-2 mb-md-0">
                                            <a :href="urlIndexMenu"
                                                class="btn btn-secondary btn-sm w-100 w-md-auto">Voltar</a>
                                        </div>
                                        <div class="col-12 col-md-6 text-center text-md-end">
                                            <button class="btn btn-primary btn-sm w-100 w-md-auto"
                                                type="submit">Atualizar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Multiselect from 'vue-multiselect';

export default {
    components: { Multiselect },
    props: {
        id: {
            Number,
        },
        urlIndexMenu: String,
    },
    data() {
        return {
            loading: false,
            menu: {
                label: '',
                icon: '',
                url: '',
                children: {}
            },
        };
    },
    mounted() {
        this.find();
    },
    methods: {
        find() {
            this.loading = true;

            axios.get(`/admin/menu/find/${this.id}`)
                .then(response => {
                    this.menu = response.data.menu;
                })
                .catch(errors => {
                    this.alertDanger(errors);
                }).finally(() => {
                    this.loading = false;
                });
        },
        save() {
            this.loading = true;
            axios.post('/admin/menu/update/' + this.menu.id, this.menu)
                .then(response => {
                    this.alertSuccess('Operação realizada com sucesso!');
                    window.scrollTo(0, 0);
                })
                .catch(errors => {
                    this.alertDanger(errors);
                }).finally(() => {
                    this.loading = false;
                });
        },
        addRow() {
            this.menu.children.push({
                label: "",
                icon: "",
                url: "",
                active: 1,
                son: this.menu.id
            });
        },
        deleteRow(index) {
            this.menu.children.splice(index, 1);
        }
    }
}
</script>
