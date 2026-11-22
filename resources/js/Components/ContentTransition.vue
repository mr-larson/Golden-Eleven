<script setup>
defineProps({
    panelKey: { type: String, required: true },
    panelClass: { type: String, default: '' },
});

function prepareLeave(element) {
    element.inert = true;
}
</script>

<template>
    <div class="relative">
        <Transition name="page" @before-leave="prepareLeave">
            <div :key="panelKey" :class="panelClass">
                <slot />
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.page-leave-active {
    position: absolute;
    inset: 0 0 auto;
    z-index: 0;
    pointer-events: none;
}

@media (prefers-reduced-motion: no-preference) {
    .page-enter-active {
        position: relative;
        z-index: 1;
        transition: opacity 260ms ease-out;
    }

    .page-leave-active {
        /* Garder l'écran précédent opaque le temps que le suivant le recouvre. */
        animation: hold-page 260ms;
    }

    .page-enter-from {
        opacity: 0;
    }

    @keyframes hold-page {
        from, to {
            opacity: 1;
        }
    }
}
</style>
