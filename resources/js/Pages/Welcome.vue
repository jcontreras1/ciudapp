<script setup>
import { Head, router, Link } from '@inertiajs/vue3';
import AppLayoutHome from '@/Layouts/AppLayoutHome.vue';
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
    canLogout: {
        type: Boolean,
        required: true,
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
    user : {
        type: Object,
        // required: true,

    }
});

const veryBottomTarget = ref(null) // Elemento para detectar el final de la página
const selectedPostToModal = ref(null) // Elemento para detectar el final de la página

const deletePost = (post) => {
    Swal.fire({
        title: '¿Eliminar Post?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.delete(`/api/post/${post.id}`)
                .then(() => {
                    props.posts.data = props.posts.data.filter(p => p.id !== post.id);
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Ocurrió un error al eliminar la publicación:' + error.message,
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                });
        }
    });
}

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
        Swal.fire({
            title: 'Error!',
            text: 'Ocurrió un error al cargar más publicaciones:' + error.message,
            icon: 'error',
            confirmButtonText: 'Ok'
        });
    });

});

</script>

<template>
    <AppLayoutHome >
        <BootstrapModal :post="selectedPostToModal"></BootstrapModal>
        <Head title="Inicio"></Head>
        <!--LATERAL IZQUIERDO - Barra de navegación -->
        <!-- <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" href="#">Para ti</a>
            </li>
            <li class="nav-item">
                <del><a class="nav-link" href="#">Reportes</a></del>
            </li>
        </ul> -->

        <!-- CENTRO -Cuadro para crear un post -->
        <div class="mt-0" v-if="$page.props.auth.user">
            <div class="d-flex align-items-start">
                <CreatePost :categorias="categorias" v-on:newPost="(post) => {props.posts.data = [post, ...props.posts.data]}" />
            </div>
        </div>
        <hr>
        <!-- Post -->
        <div class="mb-2" v-for="post in props.posts.data" :key="post.id">
            <PostShow :post="post" v-on:deletePost="deletePost(post)" v-on:showPostOnModal="selectedPostToModal = post"></PostShow>
        </div>
        <div ref="veryBottomTarget" class="-translate-y-72 h-20 text-center">
            <i class="fas fa-spinner fa-spin fa-2x"></i>&nbsp;Cargando más publicaciones...
        </div>
    </AppLayoutHome>
</template>
