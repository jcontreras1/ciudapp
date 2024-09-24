<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';

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
        <NavLink :href="route('home')" icon='<i class="fas fa-bars"></i>' title="Inicio" ></NavLink>

        <del><NavLink :href="'#'" icon='<i class="far fa-plus-square"></i>' title="Nuevo Post" ></NavLink></del>

        <del><NavLink :href="'#'" icon='<i class="fas fa-map"></i>' title="Mapa" ></NavLink></del>

        <del><NavLink :href="'#'" icon='<i class="fas fa-compass"></i>' title="Explorar" ></NavLink></del>

        <del><NavLink :href="'#'" icon='<i class="fas fa-user"></i>' title="Perfil" ></NavLink></del>

        <del><NavLink :href="'#'" icon='<i class="fas fa-wrench"></i>' title="Configuración" ></NavLink></del>

        <NavLink :href="route('category.index')" icon='<i class="fas fa-th-large"></i>' title="Categorías" ></NavLink>
    </div>
    <div class="d-block d-md-none py-1"></div>
</template>
