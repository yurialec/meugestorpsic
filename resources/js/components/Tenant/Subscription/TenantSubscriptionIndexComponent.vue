<template>
  <div class="container-fluid py-3">
    <div v-if="subscriptionAlert" class="alert py-3 px-3 mb-3 bg-white border" :class="subscriptionAlert.class">
      <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
        <div>
          <div class="fw-semibold">
            <i class="fa-solid fa-triangle-exclamation me-2" :class="subscriptionAlert.iconClass"></i>
            {{ subscriptionAlert.title }}
          </div>
        </div>
      </div>
    </div>
    <div class="row g-4">
      <div class="col-12 col-md-8">
        <div class="p-3 bg-white border rounded">
          <div class="table-responsive" v-if="payments.data.length">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <span class="text-muted">Historico de pagamentos</span>
            </div>

            <table class="table table-sm mb-0 align-middle">
              <tbody>
                <tr v-for="payment in payments.data" :key="payment.id">
                  <td>{{ formatDate(payment.paid_at) }}</td>
                  <td>{{ payment.plan?.name || '-' }}</td>
                  <td class="text-muted">
                    {{ paymentMethodLabel(payment.payment_method) }}
                  </td>
                  <td class="text-center">
                    <button class="btn btn-sm btn-link text-muted p-0" @click="openInstallments(payment)">
                      {{ payment.installments_count }}x
                    </button>
                  </td>
                  <td class="text-end">R$ {{ payment.amount }}</td>
                  <td class="text-center">
                    <span class="text-muted">{{ statusLabel(payment.status) }}</span>
                  </td>
                </tr>
              </tbody>
            </table>

            <div class="d-flex justify-content-between align-items-center mt-3">
              <small class="text-muted">
                {{ payments.from }}-{{ payments.to }} de {{ payments.total }}
              </small>
              <ul class="pagination pagination-sm mb-0">
                <li class="page-item" :class="{ disabled: !payments.prev_page_url }">
                  <a class="page-link" href="#" @click.prevent="changePage(payments.current_page - 1)">
                    &laquo;
                  </a>
                </li>
                <li v-for="page in payments.last_page" :key="page" class="page-item"
                  :class="{ active: page === payments.current_page }">
                  <a class="page-link" href="#" @click.prevent="changePage(page)">
                    {{ page }}
                  </a>
                </li>
                <li class="page-item" :class="{ disabled: !payments.next_page_url }">
                  <a class="page-link" href="#" @click.prevent="changePage(payments.current_page + 1)">
                    &raquo;
                  </a>
                </li>
              </ul>
            </div>
          </div>

          <div v-else>
            <small>
              <span class="text-muted">
                Nenhum pagamento registrado.
              </span>
            </small>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-4">
        <div class="p-3 bg-white border rounded">
          <div v-if="loading" class="text-center py-3">
            <div class="spinner-border spinner-border-sm text-secondary"></div>
          </div>

          <div v-else>
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted">Plano atual</span>
              <span class="badge" :class="subscriptionBadgeClass">
                <span class="text-white">{{ subscriptionBadgeText }}</span>
              </span>
            </div>

            <div class="mb-3">
              <div>{{ tenant.subscription.plan.name || '-' }}</div>
              <small class="text-muted">{{ tenant.subscription.plan.description || '-' }}</small>
            </div>

            <div v-if="tenant.subscription.plan.price !== undefined && tenant.subscription.plan.price !== null"
              class="mb-3">
              <div class="text-muted small">Valor</div>
              <div>R$ {{ tenant.subscription.plan.price }}</div>
            </div>

            <div class="d-flex justify-content-between small text-muted mb-3">
              <div>
                <div>Inicio</div>
                <div>{{ formatDate(tenant.subscription.current_period_start) }}</div>
              </div>
              <div class="text-end">
                <div>Termino</div>
                <div :class="{ 'text-danger': tenant.is_expired, 'text-warning': isSubscriptionNearExpiration }">
                  {{ formatDate(tenant.subscription.current_period_end) }}
                </div>
              </div>
            </div>

            <div class="d-grid gap-2">
              <!-- <button class="btn btn-sm btn-outline-success align-self-start align-self-md-center" @click="openCommercialContact()">
                <i class="fa-brands fa-whatsapp"></i> {{ tenant.is_expired ? 'Renovar Assinatura' : 'Falar com um Consultor' }}
              </button> -->
              <button class="btn btn-sm btn-outline-primary w-100" @click="openModalPlans">
                Ver planos disponiveis
              </button>
            </div>
            
          </div>
        </div>
      </div>
    </div>

    <div class="container-fluid py-3">
      <div class="modal fade" id="plansModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h6 class="modal-title">Planos disponiveis</h6>
              <button type="button" class="close" @click="closeModalPlans">
                <span>&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div v-for="plan in plans" :key="plan.id" class="col-md-4 mb-3">
                  <div class="card h-100" :class="{ 'border-primary': plan.id === tenant.subscription.plan.id }">
                    <div class="card-body d-flex flex-column">
                      <h6 class="card-title">{{ plan.name }}</h6>
                      <small class="text-muted mb-2">{{ plan.description }}</small>
                      <strong class="mb-3">R$ {{ plan.price }}</strong>
                      <ul v-if="(plan.filteredFeatures || []).length" class="small mb-3 pl-3">
                        <li v-for="feature in (plan.filteredFeatures || [])" :key="feature.id">
                          {{ feature.name }} - ate {{ feature.user_limit }} usuario(s)
                        </li>
                      </ul>
                      <div class="mt-auto">
                        <button v-if="plan.id !== tenant.subscription.plan.id" class="btn btn-sm btn-outline-success btn-block"
                          @click="selectPlan(plan)">
                          <i class="fa-brands fa-whatsapp"></i> Falar com consultor
                        </button>
                        <button v-else class="btn btn-sm btn-outline-secondary btn-block" disabled>
                          Plano atual
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" id="installmentsModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h6 class="modal-title">
                <i class="fas fa-list-ol mr-2"></i>Parcelas do pagamento
              </h6>
              <button type="button" class="close" @click="closeModalInstallments">
                <span>&times;</span>
              </button>
            </div>
            <div class="modal-body p-0">
              <table class="table table-hover table-sm mb-0">
                <thead class="thead-light">
                  <tr>
                    <th class="text-center">#</th>
                    <th>Vencimento</th>
                    <th>Pago em</th>
                    <th class="text-right">Valor</th>
                    <th class="text-right">Juros</th>
                    <th class="text-right">Total</th>
                    <th class="text-center">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="selectedInstallments.length === 0">
                    <td colspan="7" class="text-center text-muted py-4">
                      Nenhuma parcela encontrada.
                    </td>
                  </tr>
                  <tr v-for="installment in selectedInstallments" :key="installment.id">
                    <td class="text-center">{{ installment.number }}</td>
                    <td>{{ formatDate(installment.due_date) }}</td>
                    <td>{{ installment.paid_at ? formatDate(installment.paid_at) : '-' }}</td>
                    <td class="text-right">R$ {{ installment.amount }}</td>
                    <td class="text-right">R$ {{ installment.interest_amount }}</td>
                    <td class="text-right">R$ {{ installment.total_amount }}</td>
                    <td class="text-center">
                      <span class="badge" :class="statusBadge(installment.status)">
                        {{ statusLabel(installment.status) }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="modal-footer d-flex justify-content-between align-items-center">
              <small class="text-muted">{{ selectedInstallments.length }} parcela(s)</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Modal } from 'bootstrap';

export default {
  props: {
  },
  data() {
    return {
      loading: false,
      plans: [],
      tenant: {
        subscription: { plan: {}, payments: {} },
        is_expired: null,
        address: null,
        client: {},
      },
      payments: {
        data: [],
        current_page: 1,
        last_page: 1,
        from: 0,
        to: 0,
        total: 0,
        prev_page_url: null,
        next_page_url: null,
      },
      selectedPlan: null,
      selectedInstallments: [],
      modalInstallments: null,
      modalPlans: null,
    }
  },
  computed: {
    daysUntilExpiration() {
      const endDate = this.tenant?.subscription?.current_period_end;

      if (!endDate) {
        return null;
      }

      const today = new Date();
      const expirationDate = new Date(endDate);

      today.setHours(0, 0, 0, 0);
      expirationDate.setHours(0, 0, 0, 0);

      return Math.ceil((expirationDate.getTime() - today.getTime()) / 86400000);
    },
    isSubscriptionNearExpiration() {
      return !this.tenant.is_expired
        && this.daysUntilExpiration !== null
        && this.daysUntilExpiration >= 0
        && this.daysUntilExpiration <= 7;
    },
    subscriptionAlert() {
      if (this.tenant.is_expired) {
        return {
          class: 'border-danger',
          iconClass: 'text-danger',
          title: 'Sua assinatura expirou.',
          message: 'Fale com a nossa equipe para gerar a cobranca e liberar o acesso.',
        };
      }

      if (this.isSubscriptionNearExpiration) {
        return {
          class: 'border-warning',
          iconClass: 'text-warning',
          title: `Sua assinatura vence em ${this.daysUntilExpiration} dia(s).`,
          message: 'Antecipe a renovacao com o nosso time comercial para evitar interrupcoes.',
        };
      }

      return null;
    },
    subscriptionBadgeClass() {
      if (this.tenant.is_expired) {
        return 'text-bg-danger';
      }

      if (this.isSubscriptionNearExpiration) {
        return 'text-bg-warning';
      }

      return 'text-bg-success';
    },
    subscriptionBadgeText() {
      if (this.tenant.is_expired) {
        return 'Expirado';
      }

      if (this.isSubscriptionNearExpiration) {
        return 'A vencer';
      }

      return 'Ativo';
    },
    contactButtonIcon() {
      return 'fa-brands fa-whatsapp';
    },
    expiredContactButtonLabel() {
      return 'Ativar via WhatsApp';
    },
    tenantName() {
      return this.tenant?.client?.name || this.tenant?.client?.full_name || 'Cliente';
    },
  },
  mounted() {
    this.findMySub();
    this.findPlans();
  },
  methods: {
    findMySub(page = 1) {
      this.loading = true;
      axiosTenant.get(`subscription/find?page=${page}`)
        .then(response => {
          this.tenant = response.data.tenant.tenant;
          this.tenant.subscription = response.data.tenant.tenant.subscription;
          this.tenant.subscription.plan = response.data.tenant.plan || {};
          this.payments = response.data.tenant.payments;
          this.tenant.is_expired = response.data.tenant.tenant.is_expired;
          this.tenant.address = response.data.tenant.tenant.address;
          this.tenant.client = response.data.tenant.client || response.data.tenant.tenant.client || {};
        })
        .finally(() => { this.loading = false });
    },
    findPlans() {
      axiosTenant.get('/configuration/available-plans')
        .then(res => { this.plans = res.data.plans });
    },
    changePage(page) {
      if (page < 1 || page > this.payments.last_page) return;
      this.findMySub(page);
    },
    openInstallments(payment) {
      this.selectedInstallments = payment.installments ?? [];
      if (!this.modalInstallments) {
        this.modalInstallments = new Modal(document.getElementById('installmentsModal'));
      }
      this.modalInstallments.show();
    },
    closeModalInstallments() {
      this.modalInstallments?.hide();
    },
    openModalPlans() {
      if (!this.modalPlans) {
        this.modalPlans = new Modal(document.getElementById('plansModal'));
      }
      this.modalPlans.show();
    },
    closeModalPlans() {
      this.modalPlans?.hide();
    },
    selectPlan(plan) {
      this.selectedPlan = plan;
      this.closeModalPlans();
      this.openCommercialContact(plan);
    },
    openCommercialContact(plan = null) {
      const url = this.commercialContactUrl(plan);

      if (!url) {
        this.alertDanger('Canal de contato comercial nao configurado.');
        return;
      }

      if (url.startsWith('mailto:')) {
        window.location.href = url;
        return;
      }

      window.open(url, '_blank', 'noopener');
    },
    commercialContactUrl(plan = null) {
      const message = this.commercialContactMessage(plan);
      const phone = '556182298407';

      return `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
    },
    commercialContactMessage(plan = null) {
      const planName = plan?.name || 'Plano selecionado';
      const planPrice = this.formatCurrency(plan?.price || 0);

      return [
        'Olá, equipe do Meu Gestor Saúde! Gostaria de renovar/contratar o meu plano.',
        '',
        'Dados para identificação:',
        '',
        `Cliente: ${this.tenantName}`,
        `Plano Escolhido: ${planName} (${planPrice})`,
        '',
        'Aguardo as instruções e o link para o pagamento!',
      ].join('\n');
    },
    formatCurrency(value) {
      return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
      }).format(value || 0);
    },
    formatDate(date) {
      if (!date) return '-';
      return new Date(date).toLocaleDateString('pt-BR', { timeZone: 'UTC' });
    },
    statusBadge(status) {
      return {
        paid: 'badge-success',
        pending: 'badge-warning',
        failed: 'badge-danger',
        refunded: 'badge-secondary',
      }[status] ?? 'badge-secondary';
    },
    statusLabel(status) {
      return {
        paid: 'Pago',
        pending: 'Pendente',
        failed: 'Falhou',
        refunded: 'Estornado',
      }[status] ?? status;
    },
    paymentMethodLabel(method) {
      return {
        credit_card: 'Cartao',
        boleto: 'Boleto',
        pix: 'Pix',
      }[method] ?? method;
    },
    extractErrorMessage(error, fallback = 'Erro inesperado.') {
      const responseErrors = error.response?.data?.errors;

      if (responseErrors && typeof responseErrors === 'object') {
        const firstError = Object.values(responseErrors).flat()[0];
        if (firstError) {
          return firstError;
        }
      }

      return error.response?.data?.message || fallback;
    },
  }
}
</script>
