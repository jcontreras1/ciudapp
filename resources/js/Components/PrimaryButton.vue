<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>

    <div class="d-grid gap-2">
        <button class="btn btn-success" :type="type">
            <slot />
        </button>
    </div>
</template>
