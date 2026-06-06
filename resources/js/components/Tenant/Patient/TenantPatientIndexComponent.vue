<template>
    <div class="container-fluid py-4 px-4 px-lg-5">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-sm-row align-items-sm-start justify-content-between gap-3 mb-4">
            <div>
                <h4 class="mb-1 fw-semibold">Pacientes</h4>
            </div>
            <div class="d-flex gap-2">
                <a :href="urlCreatePatient" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 px-4">
                    <i class="fa-solid fa-plus fa-xs"></i> Cadastrar
                </a>
                <button type="button" class="btn btn-sm btn-success d-inline-flex align-items-center gap-1 px-4"
                    @click="openUploadModal">
                    <i class="fa-solid fa-upload fa-xs"></i> Importar
                </button>
            </div>
        </div>

        <!-- Card -->
        <div class="card border shadow-none">

            <!-- Toolbar -->
            <!-- <div class="px-4 py-3 border-bottom">
                <div class="input-group input-group-sm" style="max-width: 340px;">
                    <input type="text" class="form-control" v-model="searchFilter"
                        placeholder="Buscar por nome ou CPF..." />
                    <button type="button" class="btn btn-primary" @click="search">
                        <i class="fa-solid fa-magnifying-glass fa-xs"></i>
                    </button>
                </div>
            </div> -->

            <!-- Loading -->
            <div v-if="loading" class="d-flex justify-content-center py-5">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                </div>
            </div>

            <!-- Table -->
            <div v-else class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3 small fw-semibold text-uppercase text-muted">Nome</th>
                            <th class="py-3 small fw-semibold text-uppercase text-muted">E-mail</th>
                            <th class="py-3 small fw-semibold text-uppercase text-muted">CPF</th>
                            <th class="py-3 small fw-semibold text-uppercase text-muted">Status</th>
                            <th class="py-3 small fw-semibold text-uppercase text-muted">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="patient in patients.data" :key="patient.id">
                            <td class="px-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width:30px; height:30px; background:#EEF2FF; color:#4F46E5; font-size:11px; font-weight:600;">
                                        {{ initials(patient.full_name) }}
                                    </div>
                                    <span class="fw-medium">{{ patient.full_name }}</span>
                                </div>
                            </td>
                            <td class="text-muted">{{ patient.email }}</td>
                            <td class="text-muted">{{ patient.cpf }}</td>
                            <td>
                                <span class="badge rounded-pill"
                                    :class="patient.deleted_at ? 'text-bg-danger' : 'text-bg-success'">
                                    {{ patient.deleted_at ? 'Desativado' : 'Ativo' }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1"
                                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa-solid fa-gear fa-xs"></i> Opções
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border">
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                                            :href="urlEditPatient.replace('_id', patient.id)">
                                            <i class="fa-solid fa-pen-to-square fa-sm text-warning"></i>
                                            Editar
                                        </a>
                                    </li>
                                    <li>
                                        <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2"
                                            @click="disable(patient.id)">
                                            <i class="fa-sm" :class="patient.deleted_at
                                                ? 'fa-solid fa-circle-check text-success'
                                                : 'fa-solid fa-circle-xmark text-danger'"></i>
                                            {{ patient.deleted_at ? 'Ativar' : 'Desativar' }}
                                        </button>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider my-1">
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                                            :href="urlAnamnese.replace('_patient_id', patient.id)">
                                            <i class="fas fa-notes-medical fa-sm text-success"></i>
                                            Anamnese
                                        </a>
                                    </li>
                                    <li>
                                        <button class="dropdown-item d-flex align-items-center gap-2 py-2"
                                            @click="downloadPdf(patient.id)">
                                            <i class="fa-regular fa-file-pdf fa-sm text-danger"></i>
                                            PDF Anamnese
                                        </button>
                                    </li>
                                    <li>
                                        <button class="dropdown-item d-flex align-items-center gap-2 py-2"
                                            @click="downloadProntuario(patient.id)">
                                            <i class="fa-regular fa-file-pdf fa-sm text-danger"></i>
                                            Prontuário
                                        </button>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="patients.links && patients.links.length > 0" class="border-top px-4 py-3">
                <nav>
                    <ul class="pagination pagination-sm justify-content-center mb-0">
                        <li v-for="(link, i) in patients.links" :key="i"
                            :class="['page-item', { active: link.active, disabled: !link.url }]">
                            <a class="page-link" href="#" v-html="link.label" @click.prevent="pagination(link.url)"></a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Upload Modal -->
        <div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border shadow-none">
                    <div class="modal-header border-bottom">
                        <h6 class="modal-title fw-semibold d-flex align-items-center gap-2">
                            <i class="fa-solid fa-upload fa-sm text-muted"></i>
                            Importar pacientes
                        </h6>
                        <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <label class="form-label small fw-semibold text-uppercase text-muted">Arquivo de
                            importação</label>
                        <input class="form-control form-control-sm" type="file" accept=".xlsx,.xls,.csv"
                            @change="onFileSelected">
                        <small class="text-muted">Formatos aceitos: .xlsx, .xls ou .csv</small>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-sm btn-light px-4"
                            data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-sm btn-success px-4" @click="uploadPatients"
                            :disabled="isUploading">
                            <span v-if="!isUploading">Importar</span>
                            <span v-else>
                                <span class="spinner-border spinner-border-sm me-1"></span>
                                Enviando...
                            </span>
                        </button>
                    </div>
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
        urlCreatePatient: String,
        urlEditPatient: String,
        urlAnamnese: String,
        urlDownloadPdf: String,
        urlDownloadPdfProntuario: String,
    },
    data() {
        return {
            loading: false,
            patients: {
                data: [],
                links: []
            },
            searchFilter: '',
            selectedFile: null,
            isUploading: false,
            uploadModalInstance: null,
        };
    },
    mounted() {
        this.getPatients();
    },
    methods: {
        initials(name) {
            return name.split(' ').slice(0, 2).map(n => n[0]).join('').toUpperCase();
        },
        search() {
            this.getPatients('/patients/list', this.searchFilter);
        },
        pagination(url) {
            if (url) {
                this.getPatients(url);
            }
        },
        getPatients(url = '/patients/list', term = '') {
            this.loading = true;
            axiosTenant.post(url, { term })
                .then(response => {
                    this.patients = response.data.patients;
                })
                .catch(errors => {
                    this.alertDanger(errors);
                }).finally(() => {
                    this.loading = false
                });
        },
        disable(id) {
            this.confirmYesNo('Deseja desabilitar paciente?').then(() => {
                // this.loading = true;
                axiosTenant.post('/patients/disable', { patient: id })
                    .then(response => {
                        this.getPatients();
                        this.alertSuccess('Operação realizada com sucesso!');
                        window.scrollTo(0, top);
                    })
                    .catch(errors => {
                        this.alertDanger(errors);
                        window.scrollTo(0, top);
                    }).finally(() => {
                        // this.loading = false;
                    });
            });
        },
        downloadPdf(id) {
            const url = this.urlDownloadPdf.replace('_patient_id', id);
            this.loading = true;
            axiosTenant.get(url, { responseType: 'blob' })
                .then(response => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;

                    const contentDisposition = response.headers['content-disposition'];
                    let fileName = 'anamnese-paciente-' + id + '.pdf';

                    if (contentDisposition) {
                        const fileNameMatch = contentDisposition.match(/filename="(.+)"/);
                        if (fileNameMatch && fileNameMatch.length === 2) {
                            fileName = fileNameMatch[1];
                        }
                    }

                    link.setAttribute('download', fileName);
                    document.body.appendChild(link);
                    link.click();

                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(link);
                })
                .catch(errors => {
                    this.alertDanger(errors);
                    window.scrollTo(0, top);
                }).finally(() => {
                    this.loading = false
                });
        },
        downloadProntuario(id) {
            const url = this.urlDownloadPdfProntuario.replace('_patient_id', id);
            this.loading = true;
            axiosTenant.get(url, { responseType: 'blob' })
                .then(response => {
                    const blobUrl = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = blobUrl;

                    const contentDisposition = response.headers['content-disposition'];
                    let fileName = 'prontuario-paciente-' + id + '.pdf';

                    if (contentDisposition) {
                        const fileNameMatch = contentDisposition.match(/filename="(.+)"/);
                        if (fileNameMatch && fileNameMatch.length === 2) {
                            fileName = fileNameMatch[1];
                        }
                    }

                    link.setAttribute('download', fileName);
                    document.body.appendChild(link);
                    link.click();

                    window.URL.revokeObjectURL(blobUrl);
                    document.body.removeChild(link);
                })
                .catch(errors => {
                    this.alertDanger(errors);
                    window.scrollTo(0, top);
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        openUploadModal() {
            if (!this.uploadModalInstance) {
                this.uploadModalInstance = new Modal(document.getElementById('uploadModal'));
            }
            this.uploadModalInstance.show();
        },
        onFileSelected(event) {
            this.selectedFile = event.target.files[0] || null
        },
        uploadPatients() {

            if (!this.selectedFile) {
                this.alertDanger('Por favor, selecione um arquivo.');
                return;
            }

            this.loading = true;

            const formData = new FormData()
            formData.append('file', this.selectedFile);

            axiosTenant.post('/patients/upload', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => {
                    this.uploadModalInstance.hide();
                    this.selectedFile = null;
                    this.getPatients();
                    this.$refs.fileInput.value = '';
                    this.alertSuccess('Operação realizada com sucesso!');
                })
                .catch(errors => {
                    this.alertDanger(errors);
                }).finally(() => {
                    // this.loading = false;
                });
        }
    }
}
</script>