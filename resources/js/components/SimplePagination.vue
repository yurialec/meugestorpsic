<template>
    <div v-if="lastPage > 1" class="d-flex justify-content-between align-items-center mt-2">
        <span class="text-muted small">
            Exibindo {{ firstItem }}–{{ lastItem }} de {{ total }} registros
        </span>

        <nav>
            <ul class="pagination pagination-sm mb-0">
                <!-- Anterior -->
                <li class="page-item" :class="{ disabled: currentPage === 1 }">
                    <a class="page-link" href="#" @click.prevent="$emit('change', currentPage - 1)">Anterior</a>
                </li>

                <!-- Números (Simplificado: mostra todos ou range básico se preferir lógica mais complexa depois) -->
                <li v-for="page in visiblePages" :key="page" class="page-item"
                    :class="{ active: page === currentPage }">
                    <a class="page-link" href="#" @click.prevent="$emit('change', page)">{{ page }}</a>
                </li>

                <!-- Próxima -->
                <li class="page-item" :class="{ disabled: currentPage === lastPage }">
                    <a class="page-link" href="#" @click.prevent="$emit('change', currentPage + 1)">Próxima</a>
                </li>
            </ul>
        </nav>
    </div>
</template>

<script setup>
import { computed } from 'vue'

// Props: Recebe o objeto de metadados da paginação do Laravel/API
const props = defineProps({
    meta: { type: Object, required: true }
    // Exemplo de meta: { current_page: 1, last_page: 5, per_page: 10, total: 50, from: 1, to: 10 }
})

const emit = defineEmits(['change'])

// Computados para facilitar a leitura do template
const currentPage = computed(() => props.meta.current_page)
const lastPage = computed(() => props.meta.last_page)
const total = computed(() => props.meta.total)
const firstItem = computed(() => props.meta.from || 0)
const lastItem = computed(() => props.meta.to || 0)

// Lógica simples para não poluir a UI se houver muitas páginas
// Se quiser apenas "1, 2, 3...", remova esta computada e use `lastPage` no v-for
const visiblePages = computed(() => {
    const pages = []
    const maxVisible = 5 // Quantidade máxima de botões numéricos

    let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2))
    let end = Math.min(lastPage.value, start + maxVisible - 1)

    if (end - start + 1 < maxVisible) {
        start = Math.max(1, end - maxVisible + 1)
    }

    for (let i = start; i <= end; i++) {
        pages.push(i)
    }
    return pages
})
</script>