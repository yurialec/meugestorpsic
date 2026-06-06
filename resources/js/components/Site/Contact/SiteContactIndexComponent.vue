<template>
    <div class="container-fluid px-4 mt-2">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6 text-start">
                        <h3>Informações Contato</h3>
                    </div>
                    <div v-show="this.contacts.length == 0" class="col-md-6 text-end">
                        <a :href="urlCreateContact" type="button" class="btn btn-primary btn-sm">Cadastrar</a>
                    </div>
                </div>
            </div>
            <div v-if="loading === true" class="d-flex justify-content-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <div v-else class="card-body">
                <div v-if="this.contacts.length == 0" class="text-center">
                    <p>Nenhum resultado encontrado</p>
                </div>
                <div v-else class="table-responsive" style="overflow-x: hidden;">
                    <table class="table table-sm table-hover align-middle">
                        <thead>
                            <tr>
                                <th class="col-md-1 d-none d-md-table-cell">Telefone</th>
                                <th class="col-md-1 d-none d-md-table-cell">E-mail</th>
                                <th class="col-md-1 d-none d-md-table-cell">Cidade</th>
                                <th class="col-md-1 d-none d-md-table-cell">UF</th>
                                <th class="col-md-3 d-md-table-cell">Endereço</th>
                                <th class="col-md-1 d-none d-md-table-cell">CEP</th>
                                <th class="col-md-1 text-center d-none d-md-table-cell">Ações</th>
                                <th class="d-md-none text-end">Opções</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="contact in contacts" :key="contact.id">
                                <!-- Dados visíveis apenas em tela grande -->
                                <td class="d-none d-md-table-cell">{{ contact.phone }}</td>
                                <td class="d-none d-md-table-cell">{{ contact.email }}</td>
                                <td class="d-none d-md-table-cell">{{ contact.city }}</td>
                                <td class="d-none d-md-table-cell">{{ contact.state }}</td>
                                <td class="d-md-table-cell">{{ contact.address }}</td>
                                <td class="d-none d-md-table-cell">{{ contact.zipcode }}</td>
                                <td class="text-center d-none d-md-table-cell">
                                    <a :href="urlEditContact.replace('_id', contact.id)"
                                        class="text-decoration-none me-2">
                                        <i class="bi bi-pencil-square text-primary"></i>
                                    </a>
                                    <button type="button" class="btn text-danger p-0" @click="deleteRecord(contact.id)">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </td>
                                <td class="d-md-none text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            Opções
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item"
                                                    :href="urlEditContact.replace('_id', contact.id)">
                                                    <i class="bi bi-pencil-square me-1"></i> Editar
                                                </a>
                                            </li>
                                            <li>
                                                <button class="dropdown-item text-danger" type="button"
                                                    @click="deleteRecord(contact.id)">
                                                    <i class="bi bi-trash3 me-1"></i> Excluir
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
        urlCreateContact: String,
        urlEditContact: String,
    },
    data() {
        return {
            contactToDelete: null,
            loading: null,
            contacts: [],
        };
    },
    mounted() {
        this.getContact();
    },
    methods: {
        getContact() {
            this.loading = true;
            axios.get('admin/site/contact/list')
                .then(response => {
                    this.contacts = response.data.contacts;
                })
                .catch(errors => {
                    this.alertDanger(errors);
                }).finally(() => {
                    this.loading = false;
                });
        },
        deleteRecord(id) {
            this.confirmYesNo('Excluir usuário?').then(() => {
                axios.delete('/admin/site/contact/delete/' + this.contactToDelete)
                    .then(response => {
                        this.getContact();
                        this.alertSuccess('Operação realizada com sucesso!');
                    })
                    .catch(errors => {
                        this.alertSuccess('Operação realizada com sucesso!');
                    }).finally(() => {
                        this.loading = false;
                    });
            });
        },
    }
}
</script>
