<script setup>
import { Head, router, Link } from '@inertiajs/vue3';
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

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <AppLayout >
        <span v-if="canLogout">
            
            <button
            @click="logout"
            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
            >
            Log out
        </button>
    </span>
    <nav v-if="canLogin" class="-mx-3 flex flex-1 justify-end">
        <Link
        v-if="$page.props.auth.user"
        :href="route('dashboard')"
        class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
        >
        Dashboard
    </Link>
    
    <template v-else>
        <Link
        :href="route('login')"
        class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
        >
        Log in
    </Link>
    
    <Link
    v-if="canRegister"
    :href="route('register')"
    class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
    >
    Register
</Link>
</template>
</nav>
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
<div ref="veryBottomTarget" class="-translate-y-72 h-20 text-center">
    <i class="fas fa-spinner fa-spin fa-2x"></i>&nbsp;Cargando más publicaciones...
</div>
</AppLayout>
</template>
