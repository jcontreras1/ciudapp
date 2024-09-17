<script setup>
import { reactive, ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SinglePost from '@/Pages/Post/SinglePost.vue';
import PostShow from '@/Pages/Post/Show.vue';
import CreatePost from '@/Pages/Post/Create.vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    posts:{
        type: Array,
        // required: true,
    },
    categorias:{
        type: Array,
        // required: true,
    },
});

</script>

<template>
    <AppLayout>
        <Head title="Inicio"></Head>

        <!--LATERAL IZQUIERDO - Barra de navegación -->
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" href="#">Para ti</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Reportes</a>
            </li>
        </ul>

        <!-- CENTRO -Cuadro para crear un post -->
        <div class="mt-3">
            <div class="d-flex align-items-start">
                <!-- Área de texto -->
                <CreatePost :categorias="categorias" />
            </div>
        </div>
        <div class="mt-3">
            <a href="#">Mostrar {{posts.length}} posts</a>
        </div>
    <!-- </div> -->
    <hr>
    <PostShow :posts="posts"></PostShow>

</AppLayout>
</template>
