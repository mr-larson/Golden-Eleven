<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import ContentTransition from '@/Components/ContentTransition.vue';

const page = usePage();
// Les filtres, la pagination et les ancres ne rejouent pas le fondu.
const pageKey = computed(() => `${page.component}:${page.url.split(/[?#]/, 1)[0]}`);
</script>

<template>
    <ContentTransition :panel-key="pageKey" panel-class="bg-slate-100">
        <slot />
    </ContentTransition>
</template>
