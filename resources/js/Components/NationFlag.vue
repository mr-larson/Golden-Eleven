<script setup>
// Drapeau d'une nation : image du catalogue (nations.flag_path) si elle existe, sinon l'emoji.
// Sert aussi d'emblème de club en Coupe Club : un logo (hors dossier « nations ») est affiché en entier, sans recadrage.
// Les emoji de drapeaux ne s'affichent pas sous Windows, d'où l'image en priorité.
import { computed, ref, watch } from 'vue';

const props = defineProps({
    flag: { type: String, default: '' },   // emoji de repli
    url:  { type: String, default: null }, // URL de l'image
    name: { type: String, default: '' },   // texte alternatif
});

const isLogo = computed(() => !!props.url && !props.url.includes('/nations/'));

// Une image introuvable (404) retombe sur l'emoji.
const broken = ref(false);
watch(() => props.url, () => { broken.value = false; });
</script>

<template>
    <img v-if="url && !broken" :src="url" :alt="name" loading="lazy"
         class="inline-block h-[1em] align-[-0.125em]"
         :class="isLogo ? 'w-[1em] object-contain' : 'w-[1.4em] object-cover rounded-[2px]'"
         @error="broken = true" />
    <span v-else>{{ flag }}</span>
</template>
