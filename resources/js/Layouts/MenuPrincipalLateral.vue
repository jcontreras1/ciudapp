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

        <!-- Mapa -->
        <!-- <del><NavLink :href="'#'" icon='<i class="fas fa-map"></i>' title="Mapa" ></NavLink></del> -->


        <!-- <del><NavLink :href="'#'" icon='<i class="fas fa-user"></i>' title="Perfil" v-if="$page.props.auth.user"></NavLink></del> -->

        <!-- INSTITUCION -->
        <NavLink :href="route('institution.index')" icon='<i class="fas fa-landmark"></i>' title="Instituciones" v-if="$page.props.auth.user"></NavLink>



        <NavLink :href="route('category.index')" icon='<i class="fas fa-th-large"></i>' title="Categorías" v-if="$page.props.auth.user"></NavLink>

        <!-- Editar Perfil -->
        <NavLink :href="route('profile')" icon='<i class="fas fa-user-edit"></i>' title="Perfil" v-if="$page.props.auth.user"></NavLink>
        
        <ThemeToggler />
        
        <NavLink :href="'#'" icon='<i class="fas fa-sign-out-alt"></i>' @click.prevent="logout" title="Salir" v-if="$page.props.auth.user"></NavLink>

        <NavLink :href="route('login')" icon='<i class="fas fa-sign-in-alt"></i>' title="Iniciar Sesión" v-if="!$page.props.auth.user"></NavLink>

        
        <NavLink :href="route('register')" icon='<i class="fas fa-user-plus"></i>' title="Registrarse" v-if="!$page.props.auth.user"></NavLink>
        

    </div>
    <div class="d-block d-md-none py-1"></div>


</template>
