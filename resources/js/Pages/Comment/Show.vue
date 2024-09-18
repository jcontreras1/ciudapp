<script>
import { ref } from 'vue';
import { Head, Link, router, useForm  } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';


defineProps({
    post: Object,
});

</script>

<template>
    {{post.comments}}

</template>
