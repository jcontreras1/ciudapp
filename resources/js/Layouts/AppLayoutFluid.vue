<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import MenuPrincipalLateral from '@/Layouts/MenuPrincipalLateral.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';

defineProps({
    title: String,
    canLogout: Boolean,
});

const showingNavigationDropdown = ref(false);

const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const logout = () => {
    router.post(route('logout'));
};
</script>
<style>
.fixed-column {
    /* height: 100vh; */
    overflow-y: auto;
    position: sticky;
}
.scrollable-column {
    height: 90vh; /* Ajusta la altura según lo necesites */
    overflow-y: auto;
}
</style>
<template>
    <div class="h-100">
        <!-- Sección de titulo -->
        <Head :title="title" />

        <!-- Nav de arriba -->
        <nav class="navbar navbar-expand-lg bg-dark border-bottom border-bottom-dark ticky-top bg-body-tertiary mb-4" data-bs-theme="dark">
            <div class="container">
                <a class="navbar-brand fw-light" href="/">
                    <!-- <span class="fas fa-brain me-1"></span> -->
                    <AuthenticationCardLogo />
                    Ciudapp
                </a>
                <div class="d-flex justify-content-center align-items-center text-white">
                    <img :src="$page.props.auth.user?.profile_photo_url"
                    class="rounded-circle mr-2"
                    style="width: 40px;"
                    alt="Foto de perfil" v-if="$page.props.auth.user">
                    <span class="fs-5">{{$page.props.auth.user?.name}}</span>
                </div>
                <!-- <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button> -->
            </div>
        </nav>

        <div class="container">
            <!-- Layout principal 3 columnas izquierda 6 derecha -->
            <div class="row">
                <!-- Menu principal izquierdo -->
                <div class="col-12 col-md-2 fixed-column" >
                    <MenuPrincipalLateral />
                </div>

                <!-- Contenido principal -->
                <div class="col-12 col-md-10 ">
                <!-- <div class="col-12 col-md-6 scrollable-column"> -->
                    <!-- Page Content -->
                    <main>
                        <slot />
                    </main>
                </div>

               

            </div>
        </div>
    </div>
</template>
