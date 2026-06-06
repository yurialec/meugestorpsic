<template>
    <div class="container-fluid px-4 mt-2">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6 text-start">
                        <h3>Texto principal</h3>
                    </div>
                    <div v-show="!this.main?.id" class="col-md-6 text-end">
                        <a :href="urlCreateMainText" type="button" class="btn btn-primary btn-sm">Cadastrar</a>
                    </div>
                </div>
            </div>
            <div v-if="loading" class="d-flex justify-content-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <div v-else class="card-body">
                <div v-if="!main || main.length === 0" class="text-center">
                    <p>Nenhum resultado encontrado</p>
                </div>
                <div v-else class="table-responsive" style="overflow-x: hidden;">
                    <table class="table table-sm table-hover align-middle">
                        <thead>
                            <tr>
                                <th scope="col">Título</th>
                                <th scope="col" class="d-none d-md-table-cell">Texto</th>
                                <th scope="col" class="text-center d-none d-md-table-cell">Ações</th>
                                <th scope="col" class="d-md-none text-end">Opções</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ main.title }}</td>
                                <td class="d-none d-md-table-cell">{{ main.text }}</td>
                                <td class="text-center d-none d-md-table-cell">
                                    <a :href="urlUpdateMainText.replace('_id', main?.id)"
                                        class="text-decoration-none me-2">
                                        <i class="bi bi-pencil-square text-primary"></i>
                                    </a>
                                    <button type="button" class="btn text-danger p-0" @click="deleteRecord(main.id)">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </td>
                                <td class="d-md-none text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            Opções
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item"
                                                    :href="urlUpdateMainText.replace('_id', main?.id)">
                                                    <i class="bi bi-pencil-square me-1"></i> Editar
                                                </a>
                                            </li>
                                            <li>
                                                <button class="dropdown-item text-danger" type="button"
                                                    @click="deleteRecord(main.id)">
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
    </div>
</template>

<script>
import axios from 'axios';
import { Modal } from 'bootstrap';

export default {
    props: {
        urlCreateMainText: String,
        urlUpdateMainText: String,
    },
    data() {
        return {
            loading: true,
            main: [],
        };
    },
    mounted() {
        this.getMainText();
    },
    methods: {
        getMainText() {
            this.loading = true;
            axios.get('/admin/site/main-text/find')
                .then(response => {
                    this.main = response.data.main;
                })
                .catch((errors) => {
                    this.alertDanger(errors);
                })
                .finally(() => {
                    this.loading = false;
                });

        },
        deleteRecord(id) {
            this.confirmYesNo('Excluir?').then(() => {
                axios.delete('/admin/site/main-text/delete/' + this.mainToDelete)
                    .then(response => {
                        this.getMainText();
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
    }
}
</script>
