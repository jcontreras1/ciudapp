<script setup>
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PostShow from '@/Pages/Post/Show.vue';
import CreatePost from '@/Pages/Post/Create.vue';
import { ref } from 'vue';
import axios from 'axios';
import { useIntersectionObserver } from '@vueuse/core'
import BootstrapModal from '@/Components/BootstrapModal.vue';



const props = defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    posts:{
        type: Object,
        // required: true,
    },
    categorias:{
        type: Array,
        // required: true,
    },
});

const veryBottomTarget = ref(null) // Elemento para detectar el final de la página
const selectedPostToModal = ref(null) // Elemento para detectar el final de la página

const { stop } = useIntersectionObserver(veryBottomTarget, ([{ isIntersecting }]) => {
    if (!isIntersecting) {
        return;
    }
    axios.get(`${props.posts.meta.path}?cursor=${props.posts.meta.next_cursor}`)
    .then((response) => {
        props.posts.data = [...props.posts.data, ...response.data.data];
        props.posts.meta = response.data.meta;
        if(!response.data.meta.next_cursor){
            stop();
        }
    })
    .catch(error => {
        console.log(error);
    });

});
</script>

<template>
    <AppLayout >
        <BootstrapModal :post="selectedPostToModal"></BootstrapModal>
        <Head title="Inicio"></Head>
        <!--LATERAL IZQUIERDO - Barra de navegación -->
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" href="#">Para ti</a>
            </li>
            <li class="nav-item">
                <del><a class="nav-link" href="#">Reportes</a></del>
            </li>
        </ul>
        <!-- CENTRO -Cuadro para crear un post -->
        <div class="mt-3">
            <div class="d-flex align-items-start">
                <!-- Área de texto -->
                <CreatePost :categorias="categorias" />
            </div>
        </div>
        <hr>
        <!-- Post -->
        <div class="mb-2" v-for="post in props.posts.data" :key="post.id">
            <PostShow :post="post" v-on:showPostOnModal="selectedPostToModal = post"></PostShow>
        </div>
        <!-- <div ref="veryBottomTarget" class="-translate-y-72 bg-black"></div> -->
    </AppLayout>
</template>
