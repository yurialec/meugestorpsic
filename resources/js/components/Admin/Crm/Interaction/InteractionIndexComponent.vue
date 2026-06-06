<template>
    <div class="container-fluid px-4 mt-2">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-12 col-md-3 text-md-left text-center mb-2 mb-md-0">
                        <h3>Interações</h3>
                    </div>
                    <div class="col-12 col-md-9">
                        <div class="row align-items-center">
                            <div class="col-12 col-md-8 mb-2 mb-md-0">
                                <div class="input-group">
                                    <input type="text" class="form-control" v-model="searchFilter"
                                        placeholder="Pesquisar..." />
                                    <button type="button" class="btn btn-primary" @click="pesquisar()">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-12 col-md-2 text-md-end text-center">
                                <a :href="urlCreateInteraction" type="button"
                                    class="btn btn-primary btn-sm w-100 w-md-auto">
                                    Cadastrar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="loading" class="d-flex justify-content-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <div v-else class="card-body">
                <div v-if="interactions?.data.length" class="row justify-content-center">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 5%"></th>
                                    <th style="width: 25%"></th>
                                    <th style="width: 25%"></th>
                                    <th style="width: 35%; text-align: right;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="interaction in interactions.data" :key="interaction.id">
                                    <th scope="row">#{{ interaction.id }}</th>
                                    <td>{{ interaction.client_name }}</td>
                                    <td>{{ interaction.observation }}</td>
                                    <th scope="row" :style="{
                                        backgroundColor: interaction.status_color,
                                        color: getContrastColor(interaction.status_color),
                                        borderRadius: '10px',
                                        width: '15px'
                                    }">
                                        {{ interaction.status_name }}
                                    </th>
                                    <td style="text-align: right;">
                                        <a :href="'/admin/crm/interactions/edit/' + interaction.id">
                                            <i class="bi bi-arrow-right-circle-fill h4 text-primary"></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div v-if="interactions?.links.length" class="card-footer">
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-center">
                        <li v-for="(link, key) in interactions.links" :key="key" class="page-item"
                            :class="{ 'active': link.active }">
                            <a class="page-link" href="#" @click.prevent="pagination(link.url)" v-html="link.label"></a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { Modal } from 'bootstrap';

export default {
    props: {
        urlCreateInteraction: String,
    },
    data() {
        return {
            loading: false,
            interactions: {
                data: [],
                links: []
            },
            searchFilter: '',
        };
    },
    mounted() {
        this.getInteractions();
    },
    methods: {
        pesquisar() {
            this.getInteractions('admin/crm/interactions/list', this.searchFilter);
        },
        pagination(url) {
            if (url) {
                this.getInteractions(url);
            }
        },
        getInteractions(url = 'admin/crm/interactions/list') {
            this.loading = true;
            axios.get(url)
                .then(response => {
                    this.interactions = response.data.interactions;
                })
                .catch(errors => {
                    this.alertDanger(errors);
                }).finally(() => {
                    this.loading = false
                });
        },
        getContrastColor(hexColor) {
            hexColor = hexColor.replace('#', '');
            const r = parseInt(hexColor.substr(0, 2), 16);
            const g = parseInt(hexColor.substr(2, 2), 16);
            const b = parseInt(hexColor.substr(4, 2), 16);
            const brightness = (r * 299 + g * 587 + b * 114) / 1000;
            return brightness > 128 ? '#000000' : '#FFFFFF';
        }
    }
}
</script>