<template>
    <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto align-items-center">
            <li class="nav-item dropdown">
                <div class="dropdown">
                    <button class="btn dropdown-toggle user-dropdown" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false" aria-label="Menu do usuário">
                        <div class="user-avatar">
                            <i class="fas fa-user-circle fa-2xl"></i>
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li>
                            <div class="dropdown-header px-3 py-2">
                                <div class="fw-bold">{{ user.name }}</div>
                                <small class="text-muted">{{ user.email }}</small>
                            </div>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" :href="urlProfile">
                                <i class="fa-solid fa-user-gear me-2"></i> Configurações
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center text-danger" :href="urlLogout">
                                <i class="fas fa-sign-out-alt me-2"></i> Sair
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </nav>
</template>

<script>
export default {
    props: {
        urlProfile: String,
        urlLogout: String,
        urlConfig: String,
        admin: String,
    },
    data() {
        return {
            user: {
                email: '',
                name: '',
            },
        };
    },
    mounted() {
        this.getProfile();
    },
    methods: {
        getProfile() {
            axiosTenant.get('/view-profile')
                .then(response => {
                    this.user = response.data.profile;
                })
                .catch(errors => {
                    this.alertDanger(errors);
                }).finally(() => {

                });
        },
    },
}
</script>
<style scoped>
.user-dropdown {
    display: flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    color: #495057;
    background: transparent;
    border: none;
    transition: all 0.2s;
}

.user-dropdown:hover,
.user-dropdown:focus {
    color: #343a40;
    background: rgba(0, 0, 0, 0.05);
}

.user-avatar {
    /* font-size: 1.45rem; */
    color: #6c757d;
}

.user-name {
    font-size: 0.9rem;
}

.dropdown-menu {
    min-width: 220px;
    border: 1px solid rgba(0, 0, 0, 0.1);
    border-radius: 0.5rem;
}

.dropdown-header {
    background-color: #f8f9fa;
}

.dropdown-item {
    padding: 0.5rem 1rem;
    border-radius: 0.25rem;
    margin: 0.1rem 0.5rem;
    width: auto;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}
</style>
