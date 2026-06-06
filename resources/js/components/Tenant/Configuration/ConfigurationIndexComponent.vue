<template>
    <div v-if="this.loading" id="loadingOverlay"
        style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(255, 255, 255, 0.8); z-index: 9999; display: flex; justify-content: center; align-items: center;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden"></span>
        </div>
    </div>
    
</template>
<script>
export default {
    props: {
        urlListPlans: String,
    },
    data() {
        return {
            loading: false,
            previewLogo: null,
            tenant: {
                logo: '',
            },
        };
    },
    computed: {

    },
    mounted() {
        this.find();
    },
    methods: {
        find() {
            this.loading = true;
            axiosTenant.get('/get-tenant-data')
                .then(response => {
                    this.tenant = response.data.data;
                })
                .catch(errors => {
                    this.alertDanger(errors);
                }).finally(() => {
                    this.loading = false;
                });
        },
        loadImage(e) {
            const file = e.target.files[0];

            if (file) {
                this.previewLogo = URL.createObjectURL(file);
                this.tenant.logo = file;
            }
        },
        updateLogo() {

            if (!this.tenant.logo) {
                this.alertDanger('Selecione a imagem!');
                return;
            }

            let formData = new FormData();
            formData.append('file', this.tenant.logo);

            this.loading = true;
            axiosTenant.post('/configuration/logo/store', formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            }).then(response => {
                this.alertSuccess('Operação realizada com sucesso!');
                this.tenant.logo = response.data.logo;
            }).catch(errors => {
                this.alertDanger(errors);
            }).finally(() => {
                this.loading = false;
            });
        }
    }
}
</script>
