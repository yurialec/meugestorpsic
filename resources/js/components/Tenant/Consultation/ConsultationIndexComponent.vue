<template>
    <div class="container-fluid px-4 px-lg-5 py-4">
        <div class="d-flex flex-column flex-sm-row align-items-sm-start justify-content-between gap-3 mb-4">
            <div>
                <h4 class="mb-1 fw-semibold">Consultas</h4>
            </div>
        </div>
        <div class="card border shadow-none">
            <!-- Toolbar -->
            <!-- <div class="px-4 py-3 border-bottom">
                <div class="input-group input-group-sm" style="max-width: 340px;">
                    <input type="text" class="form-control" v-model="searchFilter" placeholder="Buscar por nome..." />
                    <button type="button" class="btn btn-primary" @click="search">
                        <i class="fa-solid fa-magnifying-glass fa-xs"></i>
                    </button>
                </div>
            </div> -->
            <div v-if="loading" class="d-flex justify-content-center py-5">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
            </div>
            <div v-else class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3 small fw-semibold text-uppercase text-muted">Nome</th>
                            <th class="py-3 small fw-semibold text-uppercase text-muted">Data da sessão</th>
                            <th class="py-3 small fw-semibold text-uppercase text-muted">Status</th>
                            <th class="py-3 small fw-semibold text-uppercase text-muted">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="consultation in consultationRows" :key="consultation.id">
                            <td class="px-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width:30px; height:30px; background:#EEF2FF; color:#4F46E5; font-size:11px; font-weight:600;">
                                        {{ initials(consultation.patient?.full_name) }}
                                    </div>
                                    <span class="fw-medium">{{ consultation.patient?.full_name || '—' }}</span>
                                </div>
                            </td>
                            <td class="text-muted">{{ formatDate(consultation.scheduled_at) }}</td>
                            <td>
                                <span class="badge rounded-pill" :class="{
                                    'text-bg-success': consultation.status === 'done',
                                    'text-bg-danger': consultation.status === 'canceled',
                                    'text-bg-warning': consultation.status === 'open'
                                }">
                                    {{ sanitizeString(consultation.status) }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-cog me-1"></i> Opções
                                </button>
                                <ul class="dropdown-menu shadow-sm border-0">
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center py-2"
                                            :href="urlShowConsultation.replace('_id', consultation.id)">
                                            <i class="fa-solid fa-eye me-2"></i>
                                            <span>Acessar</span>
                                        </a>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-if="paginationLinks.length > 0" class="border-top px-4 py-3">
                <nav>
                    <ul class="pagination pagination-sm justify-content-center mb-0">
                        <li v-for="(link, i) in paginationLinks" :key="i"
                            :class="['page-item', { active: link.active, disabled: !link.url }]">
                            <a class="page-link" href="#" v-html="link.label" @click.prevent="pagination(link.url)"></a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        urlShowConsultation: String,
    },
    data() {
        return {
            loading: false,
            searchFilter: '',
            consultations: this.emptyConsultations()
        };
    },
    computed: {
        consultationRows() {
            return Array.isArray(this.consultations?.data) ? this.consultations.data : [];
        },
        paginationLinks() {
            return Array.isArray(this.consultations?.links) ? this.consultations.links : [];
        }
    },
    mounted() {
        this.find()
    },
    methods: {
        initials(name) {
            if (!name) return '—';
            return name.split(' ').slice(0, 2).map(n => n[0]).join('').toUpperCase();
        },
        search() {
            this.find('/consultation/list', this.searchFilter);
        },
        pagination(url) {
            if (url) {
                this.find(url);
            }
        },
        emptyConsultations() {
            return {
                data: [],
                links: []
            };
        },
        find(url = '/consultation/list', term = '') {
            this.loading = true;
            axiosTenant.post(url, { term })
                .then(response => {
                    const consultations = response.data?.consultations;
                    if (!consultations || !Array.isArray(consultations.data)) {
                        this.consultations = this.emptyConsultations();
                        this.alertDanger(response.data?.message || 'Erro ao carregar consultas.');
                        return;
                    }

                    this.consultations = {
                        ...this.emptyConsultations(),
                        ...consultations
                    };
                })
                .catch(errors => {
                    this.consultations = this.emptyConsultations();
                    this.alertDanger(errors);
                }).finally(() => {
                    this.loading = false;
                });
        },
        formatDate(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            return date.toLocaleDateString('pt-BR');
        },
        sanitizeString(status) {
            switch (status) {
                case 'done':
                    return 'Feito'
                    break;
                case 'canceled':
                    return 'Cancelado'
                    break;
                case 'open':
                    return 'Aberto'
            }
        }
    }
}
</script>
