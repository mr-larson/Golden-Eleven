<script setup>
import ConfigRange from '@/Components/ConfigRange.vue';

/**
 * Curseur « nombre de participants » (le complément est toujours tiré au hasard).
 * Partagé par la Ligue (clubs) et la Coupe Nation (nations).
 *
 *  - v-model          : nombre choisi ;
 *  - allLabel         : texte quand le curseur est au maximum (null = pas de libellé spécial) ;
 *  - hint             : phrase d'aide sous le curseur.
 */
const count = defineModel({ type: Number, required: true });

defineProps({
    id:       { type: String, required: true },
    label:    { type: String, required: true },
    unit:     { type: String, default: '' },        // « clubs », « nations »…
    min:      { type: Number, required: true },
    max:      { type: Number, required: true },
    step:     { type: Number, default: 1 },
    allLabel: { type: String, default: null },
    hint:     { type: String, default: '' },        // phrase d'aide sous le curseur
    error:    { type: String, default: null },
});

</script>

<template>
    <div class="flex flex-col gap-1.5">
        <ConfigRange v-model="count" :id="id" :label="label" :min="min" :max="max" :step="step" class="max-w-md" value-width="w-24">
            <template #value>{{ allLabel && count >= max ? allLabel : `${count} ${unit}` }}</template>
        </ConfigRange>
        <p v-if="hint" class="text-xs text-slate-400">{{ hint }}</p>
        <p v-if="error" class="text-xs text-rose-500">{{ error }}</p>
    </div>
</template>
