<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import CardPost from '@/Components/CardPost.vue';


defineProps({
    title: String,
    posts: Array,
});

const showingNavigationDropdown = ref(false);

//Cerrar sesión (para después)
const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <!-- <CardPost :posts="posts" /> -->
    <div class="card col-12 py-2" v-for="post in posts" :key="post.id">


        {{ post }}<br>
        <div v-for="image in post.images">
            <img :src="image.file" alt="Imagen" class="img-fluid w-50">
        </div>
        <div class="col-12 col-md-4 d-none d-md-block mb-1">
            LATITUD: <p>{{post.lat }}</p>
        </div>
        <div class="col-12 col-md-4 d-none d-md-block mb-1">
            <input type="text" readonly id="longitud" name="longitud"
            class="form-control form-control-lg">
        </div>
        <!-- <img :src="post.image" alt="Imagen" class="img-fluid"> -->
    </div>
</template>
