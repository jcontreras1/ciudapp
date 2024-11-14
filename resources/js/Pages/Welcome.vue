<script setup>
import { Head, router, Link } from '@inertiajs/vue3';
import AppLayoutHome from '@/Layouts/AppLayoutHome.vue';
import PostShow from '@/Pages/Post/Show.vue';
import CreatePost from '@/Pages/Post/Create.vue';
import { onMounted, ref, watch } from 'vue';
import axios from 'axios';
import { useIntersectionObserver } from '@vueuse/core'
import BootstrapModal from '@/Components/BootstrapModal.vue';

const props = defineProps({
    canLogin: Boolean,
    canLogout: {
        type: Boolean,
        required: true,
    },
    canRegister: Boolean,
    posts: {
        type: Object,
    },
    categorias: Array,
    user: Object,
});
const updateKey = ref(0); // Crear un contador de clave
const veryBottomTarget = ref(null);
const selectedPostToModal = ref(null);
const temporalPost = ref([]);

// Crea la variable reactiva myPosts a partir de props.posts
const myPosts = ref({ ...props.posts });

// Función para eliminar un post
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
                myPosts.value.data = myPosts.value.data.filter(p => p.id !== post.id);
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

// Función para detectar el final de la página y cargar más posts
const { stop } = useIntersectionObserver(veryBottomTarget, ([{ isIntersecting }]) => {
    if (!isIntersecting) return;

    axios.get(`${myPosts.value.meta.path}?cursor=${myPosts.value.meta.next_cursor}`)
    .then((response) => {
        myPosts.value.data = [...myPosts.value.data, ...response.data.data];
        myPosts.value.meta = response.data.meta;
        if (!response.data.meta.next_cursor) stop();
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

// Función para mostrar los posts nuevos al hacer clic en "Actualizar"
const mostrarMas = () => {
    myPosts.value.data = [...temporalPost.value, ...myPosts.value.data];
    temporalPost.value = [];
}

// Escuchar nuevos posts creados y almacenarlos temporalmente
Echo.channel('post').listen('.created', (response) => {
    temporalPost.value = [response.post, ...temporalPost.value];
    console.log(temporalPost.value)
});

Echo.channel('post').listen('.updated', (response) => {
    let idSearch = response.post.id;
    let index = myPosts.value.data.findIndex(post => post.id == idSearch);

    if (index !== -1) {
        Object.assign(myPosts.value.data[index], response.post);
        updateKey.value++; // Incrementar la clave para forzar la actualización
    }
});
</script>

<template>
    <AppLayoutHome>
        <BootstrapModal :post="selectedPostToModal"></BootstrapModal>
        <Head title="Inicio"></Head>

        <div class="mt-0" v-if="$page.props.auth.user">
            <div class="d-flex align-items-start">
                <CreatePost :categorias="categorias" v-on:newPost="(post) => { myPosts.data = [post, ...myPosts.data] }" />
            </div>
            <hr>
        </div>

        <div class="mb-3 col-12" v-if="temporalPost.filter(elem => elem.user_id !== $page.props.auth.user?.id).length">
            <a class="float-center link-underline link-underline-opacity-0" href="#" @click.prevent="mostrarMas" v-if="temporalPost.length == 1">
                Actualizar {{ temporalPost.length }} reporte nuevo ...
            </a>
            <a class="float-center link-underline link-underline-opacity-0" href="#" @click.prevent="mostrarMas" v-if="temporalPost.length > 1">
                Actualizar {{ temporalPost.length }} reportes nuevos ...
            </a>
        </div>

        <div class="mb-2" v-for="post in myPosts.data" :key="updateKey.value">
            <PostShow v-if="!post.private || (post.private && post.user_id == $page.props.auth.user?.id)" :post="post" v-on:deletePost="deletePost(post)" v-on:showPostOnModal="selectedPostToModal = post"></PostShow>
        </div>

        <div ref="veryBottomTarget" class="-translate-y-72 h-20 text-center">
            <i class="fas fa-spinner fa-spin fa-2x"></i>&nbsp;Cargando más publicaciones...
        </div>
    </AppLayoutHome>
</template>
