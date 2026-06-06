<template>
    <div class="container-fluid px-4 mt-2">
        <h1 class="mt-4">Dashboard</h1>
        <div class="row">
            <div class="col-xl-6">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-users me-1"></i>
                        Clientes
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-light text-dark mb-2">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Ativos</h5>
                                        <p class="card-text">130</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light text-dark mb-2">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Novos Leads</h5>
                                        <p class="card-text">25</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-dollar-sign me-1"></i>
                        Faturamento
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-light text-dark mb-2">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Mensal</h5>
                                        <p class="card-text">R$ 8.999,00</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light text-dark mb-2">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Total</h5>
                                        <p class="card-text">R$ 120.000,00</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-6">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-chart-line me-1"></i>
                        Evolução de Clientes
                    </div>
                    <div class="card-body">
                        <canvas id="clientesChart" width="100%" height="40"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-chart-bar me-1"></i>
                        Evolução de Faturamento
                    </div>
                    <div class="card-body">
                        <canvas id="faturamentoChart" width="100%" height="40"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { onMounted } from 'vue';
import Chart from 'chart.js/auto';

export default {
    setup() {
        onMounted(() => {
            const clientesCtx = document.getElementById('clientesChart').getContext('2d');
            new Chart(clientesCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                    datasets: [
                        {
                            label: 'Clientes Ativos',
                            data: [100, 110, 120, 130, 125, 130],
                            borderColor: 'rgba(54, 162, 235, 1)',
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            fill: true,
                        },
                        {
                            label: 'Novos Leads',
                            data: [20, 25, 30, 25, 30, 25],
                            borderColor: 'rgba(255, 99, 132, 1)',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            fill: true,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                    },
                },
            });

            const faturamentoCtx = document.getElementById('faturamentoChart').getContext('2d');
            new Chart(faturamentoCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                    datasets: [
                        {
                            label: 'Faturamento Mensal',
                            data: [8000, 9000, 8500, 9500, 8700, 9000],
                            backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        },
                        {
                            label: 'Faturamento Total',
                            data: [8000, 17000, 25500, 35000, 43700, 52700],
                            backgroundColor: 'rgba(153, 102, 255, 0.6)',
                        },
                    ],
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                    },
                    scales: {
                        x: {
                            stacked: true,
                        },
                        y: {
                            stacked: true,
                        },
                    },
                },
            });
        });

        return {};
    },
};
</script>

<style scoped>
.card-body.text-center {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
</style>