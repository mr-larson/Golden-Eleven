<script setup>
// Faux curseur de souris des visites guidées : purement décoratif, positionné en px dans son parent `relative`.
defineProps({
    x: { type: Number, default: 0 },
    y: { type: Number, default: 0 },
    visible: { type: Boolean, default: false },
    pressed: { type: Boolean, default: false },
});
</script>

<template>
    <svg v-show="visible" aria-hidden="true" viewBox="0 0 24 24" width="26" height="26"
         class="demo-cursor pointer-events-none absolute left-0 top-0 z-10 drop-shadow-[0_2px_3px_rgba(0,0,0,0.35)]"
         :class="pressed ? 'demo-cursor--pressed' : ''"
         :style="{ transform: `translate(${x}px, ${y}px)` }">
        <path d="M4 2l15 9-6.5 1.6L9.6 19z" fill="#0f172a" stroke="#fff" stroke-width="1.6" stroke-linejoin="round" />
    </svg>
</template>

<style scoped>
.demo-cursor { transition: transform 0.85s cubic-bezier(0.45, 0, 0.2, 1); }
.demo-cursor--pressed { filter: drop-shadow(0 0 6px rgb(45 212 191 / 0.9)); }
@media (prefers-reduced-motion: reduce) { .demo-cursor { transition: none; } }
</style>
