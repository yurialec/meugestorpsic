<template>
    <div class="card">
        <div class="card-header">
            <h4>Editar Plano</h4>
        </div>
        <div v-if="loading" class="d-flex justify-content-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden"></span>
            </div>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-center">
                <form method="POST" action="" @submit.prevent="save()" class="col-lg-8 col-md-10 col-sm-12"
                    autocomplete="off">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="name">Nome</label>
                                <input type="text" class="form-control" id="name" v-model="plan.name" required />
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="price">Preço</label>
                                <!-- Alterado para type="text" e aplicação da diretiva personalizada -->
                                <input type="text" class="form-control" id="price" v-model="plan.price" v-money
                                    required />
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="description">Descrição</label>
                                <input type="text" class="form-control" id="description" v-model="plan.description"
                                    required />
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">

                                <div class="container">
                                    <div class="row">
                                        <div class="col-sm">
                                            <label for="duration">Duração</label>
                                            <input type="text" class="form-control" id="duration"
                                                v-model="plan.duration" required />
                                        </div>
                                        <div class="col-sm">
                                            <label for="duration"></label>
                                            <select class="form-select form-control" v-model="plan.dutarion_type"
                                                required>
                                                <option v-for="(label, key) in getDurtationType" :value="key">{{ label
                                                    }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-sm-6 col-12 mb-3 mb-sm-0">
                            <a :href="urlIndexPlans" class="btn btn-secondary btn-sm w-100">Voltar</a>
                        </div>
                        <div class="col-sm-6 col-12">
                            <button class="btn btn-primary btn-sm w-100" type="submit">Cadastrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    props: {
        id: Number,
        urlIndexPlans: String,
    },
    data() {
        return {
            loading: null,
            plan: {
                name: '',
                price: '',
                description: '',
                duration: '',
                dutarion_type: '',
            },
        };
    },
    mounted() {
        this.getPlanById();
    },
    computed: {
        getDurtationType() {
            return {
                "D": "Dias",
                "M": "Meses",
                "S": "Semestre",
                "Y": "Anos",
            }
        },
    },
    directives: {
        money: {
            beforeMount(el) {
                function formatMoney(value) {
                    value = value.replace(/\D/g, '');
                    if (!value) return '';
                    let num = parseInt(value, 10) / 100;
                    return num.toLocaleString('pt-BR', {
                        style: 'currency',
                        currency: 'BRL',
                    });
                }

                el.addEventListener('input', function (e) {
                    let cursorPosition = el.selectionStart;
                    let rawValue = el.value;
                    let formatted = formatMoney(rawValue);
                    el.value = formatted;
                    el.dispatchEvent(new Event('input'));
                });
            },
        },
    },
    methods: {
        getPlanById() {
            this.loading = true;
            let url = '/admin/financial/plan/get-plan-by-id/' + this.id;

            axios.get(url)
                .then((response) => {
                    this.plan = response.data.plan;
                })
                .catch((errors) => {
                    this.alertDanger(errors);
                    window.scrollTo(0, top);
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        save() {
            this.loading = true;
            let url = '/admin/financial/plan/update/' + this.id;
            axios.put(url, this.plan)
                .then((response) => {
                    this.alertSuccess('Operação realizada com sucesso!');
                    window.scrollTo(0, top);
                })
                .catch((errors) => {
                    this.alertDanger(errors);
                    window.scrollTo(0, top);
                })
                .finally(() => {
                    this.loading = false;
                });
        }
    }
}
</script>
