<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
    icon: String,
    active: Boolean,
    title: String,
});

const classes = computed(() => {
    return props.active
        ? 'inline-flex items-center px-1 pt-1 border-b-2 border-indigo-400 dark:border-indigo-600 text-sm font-medium leading-5 text-gray-900 dark:text-gray-100 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out'
        : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700 transition duration-150 ease-in-out';
});
</script>

<template>
    <span class="nav-item">
        <a class="nav-link d-flex flex-column flex-md-row" :href="href">
            <!-- Icono siempre visible -->
            <i class="fs-5" v-html="icon"></i>
            <!-- Título visible solo en pantallas medianas o más grandes -->
            <span class="fs-5 fw-lighter d-none d-md-block ms-md-2 mt-1 mt-md-0">{{title}}</span>
        </a>
    </span>
</template>
