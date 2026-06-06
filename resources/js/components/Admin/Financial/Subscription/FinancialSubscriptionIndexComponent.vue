<template>
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-12 col-md-3 text-md-left text-center mb-2 mb-md-0">
                    <h3>Inscrições</h3>
                </div>
                <div class="col-12 col-md-6 text-center mb-2 mb-md-0">
                    <div class="input-group">
                        <input type="text" class="form-control" v-model="searchFilter" />
                        <button type="button" class="btn btn-primary" @click="pesquisar()">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="!subscriptions.data.length" class="card-body text-center">
            <h5>Nenhum resultado encontrado</h5>
        </div>
        <div v-else class="card-body">
            <div v-if="loading" class="d-flex justify-content-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden"></span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Cliente/CRP</th>
                            <th scope="col">Plano</th>
                            <th scope="col">Valor</th>
                            <th scope="col">Status</th>
                            <th scope="col">Fim</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="subscription in subscriptions.data" :key="subscription.id">
                            <td scope="row">{{ subscription.id }}</td>
                            <td scope="row">{{ subscription.tenant.client.name + '/' + subscription.tenant.domain }}
                            </td>
                            <td scope="row">{{ subscription.plan.name }}</td>
                            <td scope="row">{{ currecyFormater(subscription.plan.price) }}</td>
                            <td scope="row">{{ formatStatus(subscription.status) }}</td>
                            <td scope="row">{{ calcPlanDuration(subscription.plan) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer" v-if="subscriptions.data.length">
            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center">
                    <li v-for="(link, key) in subscriptions.links" :key="key" class="page-item"
                        :class="{ 'active': link.active }">
                        <a class="page-link" href="#" @click.prevent="pagination(link.url)" v-html="link.label"></a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import moment from 'moment';

export default {
    props: {

    },
    data() {
        return {
            loading: false,
            subscriptions: {
                data: [],
                links: []
            },
            searchFilter: '',
        };
    },
    mounted() {
        this.getSubscriptions();
    },
    methods: {
        pesquisar() {
            this.getSubscriptions('/admin/financial/subscriptions/list', this.searchFilter);
        },
        pagination(url) {
            if (url) {
                this.getSubscriptions(url);
            }
        },
        getSubscriptions() {
            this.loading = true;
            let url = '/admin/financial/subscriptions/list';
            axios.get(url)
                .then(response => {
                    this.subscriptions = response.data.subscriptions;
                })
                .catch(errors => {
                    this.alertDanger(errors);
                }).finally(() => {
                    this.loading = false
                });
        },
        formatedDuration(dutarion_type) {

            let word = '';
            switch (dutarion_type) {
                case 'D':
                    word = 'Dias'
                    break;
                case 'M':
                    word = 'Meses'
                    break;
                case 'S':
                    word = 'Semetre'
                    break;
                case 'Y':
                    word = 'Anos'
                    break;
            }

            return word;
        },
        formatDate(date) {
            return moment(date).format('DD/MM/YYYY');
        },
        calcPlanDuration(plan) {
            const endDate = moment();

            switch (plan.dutarion_type) {
                case 'D':
                    endDate.add(plan.duration, 'days');
                    break;
                case 'M':
                    endDate.add(plan.duration, 'months');
                    break;
                case 'S':
                    endDate.add(plan.duration * 6, 'months');
                    break;
                case 'Y':
                    endDate.add(plan.duration, 'years');
                    break;
                default:
                    return null;
            }

            return endDate.format('DD/MM/YYYY');
        },
        formatStatus(status) {
            switch (status) {
                case 'Active':
                    return 'Ativo';
                    break;
                case 'Expired':
                    return 'Expirado';
                    break;
                case 'Canceled':
                    return 'Cancelado';
                    break;
            }
        },
        currecyFormater(price) {
            if (isNaN(price)) {
                console.error("Valor inválido para formatação:", price);
                return price;
            }

            const formatter = new Intl.NumberFormat('pt-BR', {
                style: 'currency',
                currency: 'BRL',
            });

            return formatter.format(price);
        },
    }
}
</script>