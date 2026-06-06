<template>
    <div class="container-fluid px-4 mt-2">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Histórico de Interações</h4>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#addInteractionModal"> <i class="fas fa-plus"></i> Nova Interação
                </button>
            </div>
            <div class="card-body">
                <div v-if="loading" class="d-flex justify-content-center py-5">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden"></span>
                    </div>
                </div>
                <div v-else>
                    <div class="mb-4">
                        <h5>Informações do Cliente</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Nome:</strong> {{ client.name }}</p>
                                <p><strong>Email:</strong> {{ client.email }}</p>
                                <p><strong>Telefone:</strong> {{ client.phone }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>CPF:</strong> {{ client.cpf }}</p>
                                <p><strong>Função:</strong> {{ client.function }}</p>
                                <p><strong>Data de Cadastro:</strong> {{ formatDate(client.created_at) }}</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h5>Interações</h5>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Status</th>
                                    <th>Observação</th>
                                    <th>Anexo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(interaction, index) in interactions" :key="index">
                                    <td>{{ formatDate(interaction.created_at) }}</td>
                                    <td>
                                        <span :style="{ color: interaction.status.color }">
                                            {{ interaction.status.name }}
                                        </span>
                                    </td>
                                    <td>{{ interaction.observation || 'Nenhuma observação' }}</td>
                                    <td>
                                        <button v-if="interaction.attachment" @click="downloadAttachment(interaction)"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-download"></i>
                                        </button>
                                        <span v-else>Nenhum anexo</span>
                                    </td>
                                </tr>
                                <tr v-if="interactions.length === 0">
                                    <td colspan="4" class="text-center">Nenhuma interação encontrada.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="addInteractionModal" tabindex="-1" aria-labelledby="addInteractionModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addInteractionModalLabel">Nova Interação</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="saveInteraction">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select v-model="interaction.status_id" id="status" class="form-select" required>
                                    <option v-for="status in statuses" :key="status.id" :value="status.id">
                                        {{ status.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="observation" class="form-label">Observação</label>
                                <textarea v-model="interaction.observation" id="observation" class="form-control"
                                    rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="formFileSm" class="form-label">Anexo</label>
                                <input @change="handleFileUpload" class="form-control form-control-sm" id="formFileSm"
                                    type="file">
                                <div v-if="interaction.attachment">
                                    <p>Arquivo selecionado: {{ interaction.attachment.name }}</p>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary btn-sm" @click="save">Salvar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import moment from 'moment';
import { Modal } from 'bootstrap';


export default {
    props: {
        id: Number,
        urlIndexClient: String,
    },
    data() {
        return {
            client: {},
            interactions: [],
            statuses: [],
            loading: true,
            interaction: {
                status_id: null,
                observation: '',
                attachment: null,
            },
        };
    },
    mounted() {
        this.find();
        this.findStatuses();
    },
    methods: {
        find() {
            this.loading = true;
            axios.get('/admin/crm/interactions/find/' + this.id)
                .then(response => {
                    this.client = response.data.interaction[0].client;
                    this.interactions = response.data.interaction;
                })
                .catch(errors => {
                    this.alertDanger(errors);
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        findStatuses() {
            axios.get('/admin/crm/status/list')
                .then(response => {
                    this.statuses = response.data.statuses;
                }).catch(errors => {
                    this.alertDanger(errors);
                }).finally(() => {
                    this.loading = false;
                });
        },
        save() {
            const formData = new FormData();
            formData.append('status_id', this.interaction.status_id);
            formData.append('client_id', this.id);
            formData.append('observation', this.interaction.observation);

            if (this.interaction.attachment) {
                formData.append('attachment', this.interaction.attachment);
            }
            this.loading = true;

            axios.post('/admin/crm/interactions/store', formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            }).then(response => {
                this.alertSuccess('Operação realizada com sucesso!');
                this.find();

                this.interaction = {
                    status_id: null,
                    observation: '',
                    attachment: null,
                };

                const modal = Modal.getInstance(document.getElementById('addInteractionModal'));
                modal.hide();

            }).catch(errors => {
                this.alertDanger(errors);
            }).finally(() => {
                this.loading = false
            });
        },
        viewDetails(interaction) {
            console.log('Detalhes da interação:', interaction);
        },
        formatDate(date) {
            return moment(date).format('DD/MM/YYYY');
        },
        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];

                if (!allowedTypes.includes(file.type)) {
                    this.alertDanger('Tipo de arquivo inválido. Por favor, selecione uma imagem (JPEG/PNG) ou PDF.');
                    return;
                }

                const maxSize = 2 * 1024 * 1024; // 2MB
                if (file.size > maxSize) {
                    this.alertDanger('O arquivo é muito grande. O tamanho máximo permitido é 2MB.');
                    return;
                }

                this.interaction.attachment = file;
            }
        },
        downloadAttachment(interaction) {
            this.loading = true;
            axios.post('/admin/crm/interactions/download/', { interaction })
                .then(response => {
                    const blob = new Blob([response.data]);
                    const contentDisposition = response.headers['content-disposition'];
                    let fileName = 'arquivo';

                    if (contentDisposition && contentDisposition.indexOf('filename=') !== -1) {
                        fileName = contentDisposition
                            .split('filename=')[1]
                            .split(';')[0]
                            .replace(/"/g, '')
                            .trim();
                    }

                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = fileName;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    URL.revokeObjectURL(link.href);
                }).catch(errors => {
                    this.alertDanger(errors);
                }).finally(() => {
                    this.loading = false
                });
        }
    },
};
</script>

<style scoped>
.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #ddd;
}

.table thead th {
    background-color: #f8f9fa;
    border-color: #ddd;
}
</style>