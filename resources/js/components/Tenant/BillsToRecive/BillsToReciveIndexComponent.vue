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
                    <h4 class="mb-1 fw-semibold">Contas a receber</h4>
                </div>
                <div class="dropdown">
                    <button class="btn btn-sm btn-light d-inline-flex align-items-center gap-1 px-3" type="button"
                        data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                        <i class="fas fa-filter fa-xs"></i>
                        Filtros
                        <i class="fas fa-caret-down fa-xs ms-1"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end p-3 shadow-sm border" style="min-width:280px;">
                        <li>
                            <label class="form-label small fw-semibold text-uppercase text-muted">Paciente</label>
                            <input type="text" class="form-control form-control-sm" v-model="filtro.patient">
                        </li>
                        <li class="mt-3">
                            <label class="form-label small fw-semibold text-uppercase text-muted">Forma de
                                pagamento</label>
                            <select class="form-select form-select-sm" v-model="filtro.paymentMethod">
                                <option v-for="method in getPaymentMethods" :key="method.value?.id || 'all'"
                                    :value="method.value">
                                    {{ method.label }}
                                </option>
                            </select>
                        </li>
                        <li class="mt-3">
                            <label class="form-label small fw-semibold text-uppercase text-muted">Status</label>
                            <select class="form-select form-select-sm" v-model="filtro.selectedStatus">
                                <option v-for="status in getPaymentStatus" :key="status.label" :value="status.label">
                                    {{ status.value }}
                                </option>
                            </select>
                        </li>
                        <li class="mt-3">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small fw-semibold text-uppercase text-muted">Ano</label>
                                    <select class="form-select form-select-sm" v-model="filtro.year">
                                        <option v-for="y in getYearOfActivity" :key="y.value" :value="y.value">{{
                                            y.label }}</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-semibold text-uppercase text-muted">Mês</label>
                                    <select class="form-select form-select-sm" v-model="filtro.month">
                                        <option v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </li>
                        <li>
                            <hr class="dropdown-divider my-3">
                        </li>
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

            <!-- Card -->
            <div class="card border shadow-none">

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3 small fw-semibold text-uppercase text-muted">Paciente</th>
                                <th class="py-3 small fw-semibold text-uppercase text-muted">Valor</th>
                                <th class="py-3 small fw-semibold text-uppercase text-muted">Forma de pagamento</th>
                                <th class="py-3 small fw-semibold text-uppercase text-muted">Parcelas</th>
                                <th class="py-3 small fw-semibold text-uppercase text-muted">Status</th>
                                <th class="py-3 small fw-semibold text-uppercase text-muted">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="bill in bills.data" :key="bill.id">
                                <td class="px-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width:30px; height:30px; background:#EEF2FF; color:#4F46E5; font-size:11px; font-weight:600;">
                                            {{ initials(bill.patient.full_name) }}
                                        </div>
                                        <span class="fw-medium">{{ bill.patient.full_name }}</span>
                                    </div>
                                </td>
                                <td class="fw-medium">{{ bill.amount }}</td>
                                <td class="text-muted">{{ bill.payment_method.label }}</td>
                                <td class="text-muted">{{ bill.installments.length ?? 1 }}</td>
                                <td>
                                    <span class="badge" :class="{
                                        'text-bg-success': bill.status === 'paid',
                                        'text-bg-warning': bill.status === 'pending',
                                        'text-bg-danger': bill.status === 'cancelled',
                                        'text-bg-info': bill.status === 'free',
                                    }">{{ formatStatus(bill.status) }}</span>
                                </td>
                                <td>
                                    <button
                                        class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1"
                                        :disabled="bill.status !== 'pending'" @click="openModalPayment(bill.id)">
                                        <i class="fa-solid fa-dollar-sign fa-xs"></i>
                                        Registrar pagamento
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="bills.links" class="border-top px-4 py-3">
                    <nav>
                        <ul class="pagination pagination-sm justify-content-center mb-0">
                            <li v-for="(link, i) in bills.links" :key="i"
                                :class="['page-item', { active: link.active, disabled: !link.url }]">
                                <a class="page-link" href="#" v-html="link.label"
                                    @click.prevent="pagination(link.url)"></a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

        </template>

        <!-- Modal: Registrar Pagamento -->
        <div class="modal fade" id="modalPayment" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border shadow-none">
                    <div class="modal-header border-bottom">
                        <h6 class="modal-title fw-semibold d-flex align-items-center gap-2">
                            <i class="fa-solid fa-circle-dollar-to-slot fa-sm text-muted"></i>
                            Registrar pagamento
                        </h6>
                        <button type="button" class="btn-close btn-close-sm" @click="closeModal"></button>
                    </div>
                    <div class="modal-body p-4">

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-uppercase text-muted">Forma de
                                pagamento</label>
                            <select class="form-select form-select-sm" v-model="payment_method_id">
                                <option value="" disabled>Selecione</option>
                                <option v-for="method in getPaymentMethods" :key="method.id" :value="method.id">
                                    {{ method.label }}
                                </option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-uppercase text-muted">Desconto (R$)</label>
                            <input type="text" class="form-control form-control-sm" v-mask="'####,##'"
                                v-model="discount" placeholder="0,00">
                        </div>

                        <!-- Cartão de crédito -->
                        <div v-if="payment_method_id == 3" class="p-3 rounded-3 border"
                            style="background:var(--bs-secondary-bg, #f8f9fa);">
                            <p class="small fw-semibold text-uppercase text-muted mb-2"
                                style="letter-spacing:.05em; font-size:11px;">Opções de crédito</p>
                            <div class="d-flex gap-4 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="creditType" id="creditVista"
                                        v-model="credit_type" value="vista">
                                    <label class="form-check-label small" for="creditVista">À vista</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="creditType" id="creditParcelado"
                                        v-model="credit_type" value="parcelado">
                                    <label class="form-check-label small" for="creditParcelado">Parcelado</label>
                                </div>
                            </div>
                            <div v-if="credit_type === 'parcelado'">
                                <label class="form-label small fw-semibold text-uppercase text-muted">Parcelas</label>
                                <select class="form-select form-select-sm" v-model="installments">
                                    <option value="" disabled>Selecione</option>
                                    <option v-for="n in 12" :key="n" :value="n">{{ n }}x</option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-sm btn-light px-4" @click="closeModal">Cancelar</button>
                        <button type="button" class="btn btn-sm btn-primary px-4"
                            @click="registerPayment(billToRegister)">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import { Modal } from 'bootstrap';

export default {
    data() {
        return {
            loading: false,
            filtro: {
                year: null,
                month: '',
                patient: '',
                paymentMethod: null,
                selectedStatus: '',
            },
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
            bills: {
                data: [],
                links: []
            },
            modalInstance: null,
            billToRegister: null,
            paymentMethods: [],
            payment_method_id: 1,
            credit_type: '',
            installments: '',
            discount: 0,
            yearsOfActivity: [],
        };
    },
    mounted() {
        this.findBillsToReceive();
        this.findPaymentMethods();
        this.findReceipts();
    },
    computed: {
        getPaymentMethods() {
            const methods = this.paymentMethods || [];
            return [
                { value: null, label: 'Todos' },
                ...methods.map(p => ({ value: p, label: p.label }))
            ];
        },
        getYearOfActivity() {
            if (!this.yearsOfActivity)
                this.findReceipts();
            let all = this.yearsOfActivity.map(y => ({ value: y, label: y }));
            all.unshift({ value: null, label: 'Todos' });
            return all;
        },
        getPaymentStatus() {
            return [
                { label: '', value: 'Todos' },
                { label: 'pending', value: 'Pendente' },
                { label: 'free', value: 'Livre' },
                { label: 'paid', value: 'Pago' },
            ];
        }
    },
    methods: {
        initials(name) {
            return name.split(' ').slice(0, 2).map(n => n[0]).join('').toUpperCase();
        },
        pagination(url) {
            if (url) this.findBillsToReceive(url);
        },
        aplicarFiltros() {
            this.findBillsToReceive();
        },
        findPaymentMethods() {
            this.loading = true;
            axiosTenant.get('/payment-methods')
                .then(response => {
                    this.paymentMethods = response.data.data;
                })
                .catch(errors => {
                    this.alertDanger(errors);
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        findBillsToReceive(url = '/finance/bills-to-recive/list') {
            this.loading = true;
            axiosTenant
                .post(url, { filter: this.filtro })
                .then((response) => {
                    this.bills = response.data.recive;
                })
                .catch((error) => {
                    this.alertDanger(error);
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        limparFiltros() {
            this.filtro.year = new Date().getFullYear();
            this.filtro.month = new Date().getMonth() + 1;
            this.filtro.patient = '';
            this.filtro.paymentMethod = '';
            this.filtro.selectedStatus = '';
            this.aplicarFiltros();
        },
        findReceipts(url = '/finance/list') {
            this.loading = true;
            axiosTenant
                .post(url)
                .then((response) => {
                    const r = response.data.receipts;
                    this.yearsOfActivity = r.yearsOfActivity || [];
                })
                .catch((error) => {
                    this.alertDanger(error);
                })
                .finally(() => {
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
        },
        openModalPayment(id) {
            if (!this.modalInstance) {
                this.modalInstance = new Modal(document.getElementById('modalPayment'));
            }

            this.billToRegister = id;
            this.modalInstance.show();
        },
        closeModal() {
            this.billToRegister = null;
            this.payment_method_id = 1;
            this.credit_type = null;
            this.installments = null;
            this.discount = 0;

            this.modalInstance.hide();
        },
        registerPayment(id) {
            if (!id)
                return;

            if (!this.payment_method_id) {
                this.alertDanger('Selecione a forma de pagamento');
                return;
            }

            if (this.payment_method_id == 3 && !this.credit_type) {
                this.alertDanger('Selecione o tipo (à vista/parcelado)');
                return;
            }

            if (this.payment_method_id == 3 && this.credit_type == 'parcelado' && !this.installments) {
                this.alertDanger('Selecione a quantidade de parcelas');
                return;
            }

            let payLoad = {};
            payLoad.payment_method_id = this.payment_method_id;
            payLoad.credit_type = this.credit_type;
            payLoad.installments = this.installments;
            payLoad.discount = this.discount;

            axiosTenant.post(`/finance/bills-to-recive/register-payment/${id}`, { payLoad: payLoad })
                .then((response) => {
                    this.payment_method_id = null;
                    this.findBillsToReceive();
                    this.alertSuccess('Registro realizado com sucesso!');
                    this.closeModal();
                })
                .catch((error) => {
                    this.alertDanger(error);
                })
                .finally(() => {
                    this.loading = false;
                });
        }
    }
}
</script>
