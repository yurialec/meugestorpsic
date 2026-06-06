<template>
    <aside class="main-sidebar sidebar-light-primary elevation-4">
        <a :href="urlDashboard" class="brand-link d-flex align-items-center">
            <img :src="logoMobile" alt="logoMobile" class="brand-image">
            <img :src="logo" alt="Logo" class="brand-image">
        </a>
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" role="menu">
                    <li v-for="(item, index) in menuItems" :key="index" class="nav-item" :class="{
                        'menu-open': expandedItems[index] || isSubmenuActive(item.submenu)
                    }">
                        <a :href="item.submenu && item.submenu.length ? '#' : item.url" class="nav-link"
                            :class="{ 'active': isActive(item.url) || isSubmenuActive(item.submenu) }"
                            @click="item.submenu && item.submenu.length && $event.preventDefault(); toggleExpand(index)">
                            <i :class="['nav-icon', item.icon]"></i>
                            <p>
                                {{ item.name }}
                                <i v-if="item.submenu && item.submenu.length" class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <transition name="slide-fade">
                            <ul v-if="item.submenu && item.submenu.length" class="nav nav-treeview"
                                v-show="expandedItems[index] || isSubmenuActive(item.submenu)">
                                <li v-for="(subItem, subIndex) in item.submenu" :key="subIndex" class="nav-item">
                                    <a :href="subItem.url" class="nav-link"
                                        :class="{ 'active': isActive(subItem.url) }">
                                        <i :class="[subItem.icon || 'far fa-circle nav-icon']"></i>
                                        <p>{{ subItem.name }}</p>
                                    </a>
                                </li>
                            </ul>
                        </transition>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>
</template>

<script>
export default {
    props: {
        urlDashboard: String,
        urlProfile: String,
        logo: String,
        logoMobile: String,
        logoText: String,
    },
    data() {
        return {
            loading: false,
            menuItems: [],
            currentUrl: window.location.href,
            expandedItems: []
        };
    },
    mounted() {
        this.getMenuItems();
    },
    methods: {
        getMenuItems() {
            this.loading = true;
            axiosTenant.get('/get-menu')
                .then(response => {
                    this.menuItems = response.data;
                    this.expandedItems = Array(this.menuItems.length).fill(false);
                })
                .catch(error => {
                    console.error('Erro ao carregar o menu:', error);
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        isActive(menuUrl) {
            if (!menuUrl || menuUrl === '#') return false;

            const currentPath = this.currentUrl.split('?')[0].split('#')[0];
            const cleanMenuUrl = menuUrl.split('?')[0].split('#')[0];

            return currentPath === cleanMenuUrl;
        },
        isSubmenuActive(submenu) {
            if (!submenu || !submenu.length) return false;

            return submenu.some(subItem => this.isActive(subItem.url));
        },
        toggleExpand(index) {
            this.expandedItems[index] = !this.expandedItems[index];
        }
    }
};
</script>
<style>
.slide-fade-enter-active,
.slide-fade-leave-active {
    transition: all 0.25s ease;
}

.slide-fade-enter-from,
.slide-fade-leave-to {
    opacity: 0;
    transform: translateY(-6px);
}

.slide-fade-enter-to,
.slide-fade-leave-from {
    opacity: 1;
    transform: translateY(0);
}
</style>