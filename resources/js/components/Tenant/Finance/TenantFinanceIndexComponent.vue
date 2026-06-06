<template>
    <div class="container-fluid px-4 px-lg-5 py-4">

        <div v-if="loading" class="d-flex justify-content-center py-5">
            <div class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="visually-hidden"></span>
            </div>
        </div>

        <template v-else>

            <!-- Page Header -->
            <div class="d-flex flex-column flex-sm-row align-items-sm-start justify-content-between gap-3 mb-4">
                <div>
                    <h4 class="mb-1 fw-semibold">Painel financeiro</h4>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 px-3" @click="gerarPDF">
                        <i class="fa-regular fa-file-pdf fa-xs"></i> Relatório PDF
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light d-inline-flex align-items-center gap-1 px-3"
                            type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                            <i class="fas fa-filter fa-xs"></i> Filtros
                            <i class="fas fa-caret-down fa-xs ms-1"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end p-3 shadow-sm border" style="min-width:260px;">
                            <li>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold text-uppercase text-muted">Mês</label>
                                        <select class="form-select form-select-sm" v-model="filtro.month">
                                            <option v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold text-uppercase text-muted">Ano</label>
                                        <select class="form-select form-select-sm" v-model="filtro.year">
                                            <option v-for="y in getYearOfActivity" :key="y.value" :value="y.value">{{ y.label }}</option>
                                        </select>
                                    </div>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider my-3"></li>
                            <li class="d-flex gap-2">
                                <button class="btn btn-sm btn-primary flex-fill" @click="aplicarFiltros">
                                    <i class="fas fa-search fa-xs me-1"></i> Aplicar
                                </button>
                                <button class="btn btn-sm btn-light flex-fill" @click="limparFiltros">
                                    <i class="fas fa-eraser fa-xs me-1"></i> Limpar
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- KPIs -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <div class="card border shadow-none d-flex flex-row align-items-center gap-3 p-3">
                        <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0"
                            style="width:40px; height:40px; background:#EEF2FF;">
                            <i class="fa-solid fa-dollar-sign fa-sm" style="color:#4F46E5;"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-muted" style="font-size:11px; text-transform:uppercase; letter-spacing:.05em; font-weight:500;">Receita total</p>
                            <h5 class="mb-0 fw-semibold mt-1">
                                R$ {{ parseFloat(totalRecipe).toLocaleString('pt-BR', { minimumFractionDigits: 2 }) }}
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card border shadow-none d-flex flex-row align-items-center gap-3 p-3">
                        <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0"
                            style="width:40px; height:40px; background:#D1FAE5;">
                            <i class="fa-solid fa-users fa-sm" style="color:#059669;"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-muted" style="font-size:11px; text-transform:uppercase; letter-spacing:.05em; font-weight:500;">Pacientes</p>
                            <h5 class="mb-0 fw-semibold mt-1">{{ patients.total }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card border shadow-none d-flex flex-row align-items-center gap-3 p-3">
                        <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0"
                            style="width:40px; height:40px; background:#FEF3C7;">
                            <i class="fa-regular fa-credit-card fa-sm" style="color:#D97706;"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-muted" style="font-size:11px; text-transform:uppercase; letter-spacing:.05em; font-weight:500;">Ticket médio</p>
                            <h5 class="mb-0 fw-semibold mt-1">
                                R$ {{ parseFloat(averageTicket).toLocaleString('pt-BR', { minimumFractionDigits: 2 }) }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="row g-4 mb-4">
                <div class="col-12 col-md-6">
                    <div class="card border shadow-none">
                        <div class="card-header bg-transparent border-bottom d-flex align-items-center gap-2 py-3">
                            <span class="d-flex align-items-center justify-content-center rounded-2 p-1"
                                style="width:28px; height:28px; background:#EEF2FF;">
                                <i class="fa-solid fa-chart-pie fa-xs" style="color:#4F46E5;"></i>
                            </span>
                            <h6 class="mb-0 fw-semibold">Lançamentos — {{ currentMonthYear }}</h6>
                        </div>
                        <div class="card-body">
                            <div style="height:300px;">
                                <Doughnut v-if="donutData" :data="donutData" :options="donutOptions" />
                                <div v-else class="d-flex align-items-center justify-content-center h-100 text-muted small fst-italic">
                                    Nenhum dado para exibir
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card border shadow-none">
                        <div class="card-header bg-transparent border-bottom d-flex align-items-center gap-2 py-3">
                            <span class="d-flex align-items-center justify-content-center rounded-2 p-1"
                                style="width:28px; height:28px; background:#D1FAE5;">
                                <i class="fa-solid fa-chart-bar fa-xs" style="color:#059669;"></i>
                            </span>
                            <h6 class="mb-0 fw-semibold">Faturamento — últimos 6 meses</h6>
                        </div>
                        <div class="card-body">
                            <div style="height:300px;">
                                <Bar v-if="barsData" :data="barsData" :options="barsOptions" />
                                <div v-else class="d-flex align-items-center justify-content-center h-100 text-muted small fst-italic">
                                    Nenhum dado para exibir
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabela de recebimentos -->
            <div class="card border shadow-none">
                <div class="card-header bg-transparent border-bottom d-flex align-items-center gap-2 py-3">
                    <span class="d-flex align-items-center justify-content-center rounded-2 p-1"
                        style="width:28px; height:28px; background:#EEF2FF;">
                        <i class="fa-regular fa-file-lines fa-xs" style="color:#4F46E5;"></i>
                    </span>
                    <h6 class="mb-0 fw-semibold">Detalhamento de recebimentos</h6>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3 small fw-semibold text-uppercase text-muted">Data</th>
                                <th class="py-3 small fw-semibold text-uppercase text-muted">Paciente</th>
                                <th class="py-3 small fw-semibold text-uppercase text-muted">Valor</th>
                                <th class="py-3 small fw-semibold text-uppercase text-muted">Forma</th>
                                <th class="py-3 small fw-semibold text-uppercase text-muted">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="receip in receipts.data" :key="receip.id">
                                <td class="px-4 text-muted">{{ receip.created_at }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width:28px; height:28px; background:#EEF2FF; color:#4F46E5; font-size:10px; font-weight:600;">
                                            {{ initials(receip.patient.full_name) }}
                                        </div>
                                        <span class="fw-medium">{{ receip.patient.full_name }}</span>
                                    </div>
                                </td>
                                <td class="fw-medium">
                                    R$ {{ parseFloat(receip.amount).toLocaleString('pt-BR', { minimumFractionDigits: 2 }) }}
                                </td>
                                <td class="text-muted">{{ receip.payment_method.label }}</td>
                                <td>
                                    <span class="badge rounded-pill" :class="{
                                        'text-bg-success': receip.status === 'paid',
                                        'text-bg-warning': receip.status === 'pending',
                                        'text-bg-danger':  receip.status === 'cancelled',
                                        'text-bg-info':    receip.status === 'free',
                                    }">{{ formatStatus(receip.status) }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="receipts.links" class="border-top px-4 py-3">
                    <nav>
                        <ul class="pagination pagination-sm justify-content-center mb-0">
                            <li v-for="(link, i) in receipts.links" :key="i"
                                :class="['page-item', { active: link.active, disabled: !link.url }]">
                                <a class="page-link" href="#" v-html="link.label"
                                    @click.prevent="pagination(link.url)"></a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

        </template>
    </div>
</template>

<script>

import {
    Chart as ChartJS,
    ArcElement,
    Tooltip,
    Legend,
    CategoryScale,
    LinearScale,
    BarElement,
} from 'chart.js'; import { Doughnut, Bar } from 'vue-chartjs';
ChartJS.register(
    ArcElement,
    Tooltip,
    Legend,
    CategoryScale,
    LinearScale,
    BarElement
);
export default {
    components: {
        Doughnut,
        Bar,
    },
    data() {
        return {
            loading: true,
            months: [
                { value: '', label: 'Todos' },
                { value: 1, label: 'Janeiro' },
                { value: 2, label: 'Fevereiro' },
                { value: 3, label: 'Março' },
                { value: 4, label: 'Abril' },
                { value: 5, label: 'Maio' },
                { value: 6, label: 'Junho' },
                { value: 7, label: 'Julho' },
                { value: 8, label: 'Agosto' },
                { value: 9, label: 'Setembro' },
                { value: 10, label: 'Outubro' },
                { value: 11, label: 'Novembro' },
                { value: 12, label: 'Dezembro' },
            ],
            receipts: {
                data: [],
                links: []
            },
            patients: {
                total: 0,
                newest: 0
            },
            totalRecipe: 0,
            averageTicket: 0,
            yearsOfActivity: [],
            charts: {
                bars: [],
                donut: [],
            },
            currentMonthYear: null,
            totalTransactions: '',
            filtro: {
                year: null,
                month: '',
            }
        };
    },
    computed: {
        donutData() {
            if (!this.charts || !this.charts.donut || !Array.isArray(this.charts.donut)) {
                return null;
            }

            const statusLabels = {
                pending: 'pendente',
                free: 'gratuito',
                paid: 'pago',
            };

            const labels = this.charts.donut.map(item => statusLabels[item.status] || item.status);

            const data = this.charts.donut.map(item => {
                const quantity = parseFloat(item.quantity);
                return isNaN(quantity) ? 0 : quantity;
            });

            const colors = [
                '#FFEE8C',
                '#ADEBB3',
                '#B3EBF2',
                '#333',
            ];

            return {
                labels,
                datasets: [
                    {
                        data,
                        backgroundColor: labels.map((_, i) => colors[i % colors.length]),
                        borderWidth: 1,
                    }
                ]
            };
        },
        donutOptions() {
            return {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            filter: (legendItem, data) => {
                                return true;
                            }
                        }
                    },
                    tooltip: {
                        filter: (tooltipItem, data) => true,
                        callbacks: {
                            label: (context) => {

                            }
                        }
                    }
                },
                layout: {
                    padding: 2
                }
            };
        },
        barsData() {
            if (!this.charts || !this.charts.bars || !Array.isArray(this.charts.bars)) {
                return null;
            }
            return {
                labels: this.charts.bars.map(item => item.label),
                datasets: [
                    {
                        label: 'Faturamento (R$)',
                        data: this.charts.bars.map(item =>
                            parseFloat(item.total_amount) || 0
                        ),
                        backgroundColor: '#0d6efd',
                        borderRadius: 6,
                        barThickness: 32,
                    }
                ]
            };
        },
        barsOptions() {
            return {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        callbacks: {
                            label: (context) => {
                                const value = context.raw || 0;
                                return `R$ ${value.toLocaleString('pt-BR', {
                                    minimumFractionDigits: 2,
                                })}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) =>
                                `R$ ${value.toLocaleString('pt-BR')}`,
                        },
                        grid: {
                            drawBorder: false,
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                        }
                    }
                }
            };
        },
        getYearOfActivity() {
            if (!this.yearsOfActivity)
                this.findReceipts();
            let all = this.yearsOfActivity.map(y => ({ value: y, label: y }));
            all.unshift({ value: null, label: 'Todos' });
            return all;
        }
    },
    mounted() {
        this.findReceipts();
    },
    methods: {
        initials(name) {
            return name.split(' ').slice(0, 2).map(n => n[0]).join('').toUpperCase();
        },
        pagination(url) {
            if (url) this.findReceipts(url);
        },
        aplicarFiltros() {
            this.findReceipts();
        },
        findReceipts(url = '/finance/list') {
            this.loading = true;
            axiosTenant
                .post(url, {
                    year: this.filtro.year,
                    month: this.filtro.month,
                })
                .then((response) => {
                    const r = response.data.receipts;
                    this.receipts = r.finance;
                    this.totalRecipe = parseFloat(r.totalRecipe) || 0;
                    this.averageTicket = parseFloat(r.averageTicket) || 0;
                    this.yearsOfActivity = r.yearsOfActivity || [];
                    this.charts = r.chart;
                    this.patients = r.patients;
                    this.currentMonthYear = r.currentMonthYear;
                    this.totalTransactions = r.totalTransactions;
                })
                .catch((error) => {
                    this.alertDanger(error);
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        limparFiltros() {
            this.filtro.year = '';
            this.filtro.month = '';
            this.aplicarFiltros();
        },
        gerarPDF() {
            this.loading = true;
            axiosTenant.post('/finance/get-pdf', {
                year: this.filtro.year,
                month: this.filtro.month,
            }, {
                responseType: 'blob'
            })
                .then((response) => {
                    const blob = new Blob([response.data], { type: 'application/pdf' });
                    const url = window.URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', `relatorio-financeiro-${this.currentMonthYear}.pdf`);
                    document.body.appendChild(link);
                    link.click();

                    document.body.removeChild(link);
                    window.URL.revokeObjectURL(url);
                })
                .catch((error) => {
                    this.alertDanger(error);
                }).finally(() => {
                    this.loading = false;
                });
        },
        formatStatus(value) {
            return {
                paid: 'pago',
                pending: 'pendente',
                cancelled: 'cancelado',
                free: 'grátis',
            }[value] || value;
        }
    },
};
</script>