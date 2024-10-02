<script setup>
import SectionTitle from './SectionTitle.vue';
</script>

<template>
        <div class="">
            <div class="px-4 py-5 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <slot name="content" />
            </div>
        </div>
</template>
