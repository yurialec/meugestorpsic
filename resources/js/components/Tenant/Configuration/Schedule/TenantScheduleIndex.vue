<template>
    <div class="container-fluid py-3">
        <div class="row g-4">
            <div class="col-12 col-xl-8">
                <div class="bg-white border rounded-3 overflow-hidden h-100">
                    <div class="border-bottom px-4 py-3 bg-light-subtle">
                        <h5 class="mb-1">Horarios de atendimento</h5>
                        <p class="text-muted small mb-0">Escolha os dias disponiveis e defina sua janela de atendimento.</p>
                    </div>

                    <div v-if="loading" class="d-flex justify-content-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>

                    <div v-else class="p-4">
                        <form method="POST" action="" @submit.prevent="save()" autocomplete="off">
                            <div class="border rounded-3 p-3 p-md-4 bg-light-subtle mb-4">
                                <label class="form-label d-block mb-3">Dias da semana</label>
                                <div class="d-flex flex-wrap gap-3">
                                    <div class="form-check" v-for="(day, value) in daysOfWeek" :key="value">
                                        <input class="form-check-input" type="checkbox" :id="value"
                                            v-model="schedule.selectedDays" :value="value" />
                                        <label class="form-check-label" :for="value">{{ day }}</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Horario de inicio</label>
                                    <input type="time" class="form-control" v-model="schedule.start_time" />
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Horario de termino</label>
                                    <input type="time" class="form-control" v-model="schedule.end_time" />
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Inicio da pausa</label>
                                    <input type="time" class="form-control" v-model="schedule.start_break_time" />
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Fim da pausa</label>
                                    <input type="time" class="form-control" v-model="schedule.end_break_time" />
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                                <button type="submit" class="btn btn-primary btn-sm px-4">
                                    Confirmar horarios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="bg-white border rounded-3 p-4 h-100">
                    <div class="small text-muted text-uppercase mb-2">Resumo</div>
                    <h6 class="mb-3">Como isso reflete na agenda</h6>
                    <div class="text-muted small d-grid gap-2">
                        <span>Os dias marcados definem quando novos horarios podem ser exibidos.</span>
                        <span>A pausa ajuda a bloquear automaticamente o intervalo entre atendimentos.</span>
                        <span>As alteracoes ficam refletidas nas telas de agenda do tenant.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {},
    data() {
        return {
            loading: false,
            schedule: {
                start_time: '',
                end_time: '',
                start_break_time: '',
                end_break_time: '',
                selectedDays: [],
            },
        };
    },
    computed: {
        daysOfWeek() {
            return {
                "monday": "Segunda",
                "tuesday": "Terca",
                "wednesday": "Quarta",
                "thursday": "Quinta",
                "friday": "Sexta",
                "saturday": "Sabado",
                "sunday": "Domingo",
            }
        },
    },
    mounted() {
        this.find();
    },
    methods: {
        find() {
            this.loading = true;
            axiosTenant.get('/configuration/schedule/find')
                .then(response => {
                    if (response.data.schedule.length) {
                        this.schedule.start_time = response.data.schedule[0].start_time;
                        this.schedule.end_time = response.data.schedule[0].end_time;
                        this.schedule.start_break_time = response.data.schedule[0].start_break_time;
                        this.schedule.end_break_time = response.data.schedule[0].end_break_time;
                        this.schedule.selectedDays = response.data.schedule.map((d) => d.day_of_week);
                    }
                })
                .catch(errors => {
                    this.$showWidget(errors.response.data.errors, false);
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        save() {
            if (!this.schedule.selectedDays.length) {
                this.$showWidget('Selecione pelo menos um dia.', false);
                return;
            }
            if (!this.schedule.start_time) {
                this.$showWidget('Informe o horario de inicio.', false);
                return;
            }
            if (!this.schedule.end_time) {
                this.$showWidget('Informe o horario de termino.', false);
                return;
            }
            if (!this.schedule.start_break_time) {
                this.$showWidget('Informe o Inicio da pausa.', false);
                return;
            }
            if (!this.schedule.end_break_time) {
                this.$showWidget('Informe o Fim da pausa.', false);
                return;
            }

            const data = {
                selectedDays: this.schedule.selectedDays,
                start_time: this.schedule.start_time,
                end_time: this.schedule.end_time,
                start_break_time: this.schedule.start_break_time,
                end_break_time: this.schedule.end_break_time
            };

            this.loading = true;
            axiosTenant.post('/configuration/schedule/create', data)
                .then(response => {
                    this.find();
                    this.$showWidget('Horarios atualizados com sucesso!', true);
                })
                .catch(errors => {
                    this.$showWidget(errors.response.data.errors, false);
                })
                .finally(() => {
                    this.loading = false;
                });
        }
    }
}
</script>
