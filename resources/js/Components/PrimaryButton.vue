<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>

    <div class="gap-2 float-end">
        <button class="btn btn-primary" :type="type">
            <slot />
        </button>
    </div>
</template>
