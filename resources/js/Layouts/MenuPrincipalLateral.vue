<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';
import ThemeToggler from '@/Components/ThemeToggler.vue';


defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);

//Cerrar sesión (para después)
const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    
    
    <div class="nav d-flex justify-content-around d-md-inline">
        
        <!-- Inicio -->
        <NavLink :href="route('home')" icon='<i class="fas fa-bars"></i>' title="Inicio" ></NavLink>
        
        <!-- Nuevo Post -->
        <!-- <del><NavLink :href="'#'" icon='<i class="far fa-plus-square"></i>' title="Nuevo Post" v-if="$page.props.auth.user"></NavLink></del> -->
        
        <!-- Mapa de calor-->
        <!-- <NavLink :href="route('mapacalor')" icon='<i class="fas fa-map"></i>' title="Mapa de calor" ></NavLink> -->
        
        
        
        <!-- INSTITUCION -->
        <NavLink :href="route('institution.index')" icon='<i class="fas fa-landmark"></i>' title="Instituciones" v-if="$page.props.auth.user && ($page.props.isAdmin || $page.props.userInstitutions.length)"></NavLink>
        
        
        
        <NavLink :href="route('category.index')" icon='<i class="fas fa-th-large"></i>' title="Categorías" v-if="$page.props.isAdmin"></NavLink>
        
        <!-- Editar Perfil -->
        <NavLink :href="route('profile')" icon='<i class="fas fa-user-edit"></i>' title="Perfil" v-if="$page.props.auth.user"></NavLink>
        
        <ThemeToggler />
        
        <NavLink :href="'ask'" icon='<svg xmlns="http://www.w3.org/2000/svg" width="19" fill="currentColor" viewBox="0 0 512 512" id="icons" class="">
            <path d="M208,512a24.84,24.84,0,0,1-23.34-16l-39.84-103.6a16.06,16.06,0,0,0-9.19-9.19L32,343.34a25,25,0,0,1,0-46.68l103.6-39.84a16.06,16.06,0,0,0,9.19-9.19L184.66,144a25,25,0,0,1,46.68,0l39.84,103.6a16.06,16.06,0,0,0,9.19,9.19l103,39.63A25.49,25.49,0,0,1,400,320.52a24.82,24.82,0,0,1-16,22.82l-103.6,39.84a16.06,16.06,0,0,0-9.19,9.19L231.34,496A24.84,24.84,0,0,1,208,512Zm66.85-254.84h0Z"/>
            <path d="M88,176a14.67,14.67,0,0,1-13.69-9.4L57.45,122.76a7.28,7.28,0,0,0-4.21-4.21L9.4,101.69a14.67,14.67,0,0,1,0-27.38L53.24,57.45a7.31,7.31,0,0,0,4.21-4.21L74.16,9.79A15,15,0,0,1,86.23.11,14.67,14.67,0,0,1,101.69,9.4l16.86,43.84a7.31,7.31,0,0,0,4.21,4.21L166.6,74.31a14.67,14.67,0,0,1,0,27.38l-43.84,16.86a7.28,7.28,0,0,0-4.21,4.21L101.69,166.6A14.67,14.67,0,0,1,88,176Z"/>
            <path d="M400,256a16,16,0,0,1-14.93-10.26l-22.84-59.37a8,8,0,0,0-4.6-4.6l-59.37-22.84a16,16,0,0,1,0-29.86l59.37-22.84a8,8,0,0,0,4.6-4.6L384.9,42.68a16.45,16.45,0,0,1,13.17-10.57,16,16,0,0,1,16.86,10.15l22.84,59.37a8,8,0,0,0,4.6,4.6l59.37,22.84a16,16,0,0,1,0,29.86l-59.37,22.84a8,8,0,0,0-4.6,4.6l-22.84,59.37A16,16,0,0,1,400,256Z"/>
            </svg>
            ' title="IA"></NavLink>
            <NavLink :href="'#'" icon='<i class="fas fa-sign-out-alt"></i>' @click.prevent="logout" title="Salir" v-if="$page.props.auth.user"></NavLink>
            
            <!-- Google -->
            <NavLink :href="'/google-auth/redirect'" icon='<i class="fab fa-google"></i>' title="Iniciar con Google" v-if="!$page.props.auth.user"></NavLink>
            
            <NavLink :href="route('login')" icon='<i class="fas fa-sign-in-alt"></i>' title="Iniciar Sesión" v-if="!$page.props.auth.user"></NavLink>
            
            <NavLink :href="route('register')" icon='<i class="fas fa-user-plus"></i>' title="Registrarse" v-if="!$page.props.auth.user"></NavLink>
            
            
            
            
        </div>
        <div class="d-block d-md-none py-1"></div>
        
        
    </template>
    