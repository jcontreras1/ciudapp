<script setup>
import MapaPosicionado from '@/Components/MapaPosicionado.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import mapboxgl from 'mapbox-gl';
import { onMounted, ref, watch } from 'vue';

const commentForm = useForm({
    comment : "",
});

const props = defineProps({
    post: Object,
});


</script>

<template>

    <i class="fas fa-map-pin"></i> <b>{{ new Date(post?.created_at).toLocaleDateString() }} </b>
    <h5>{{ post?.comment }}</h5>
    <h5 class="d-flex nav-link">
        <span><b> #{{ post?.subcategory?.name }} </b></span>
        <span><b> #{{ post?.category?.name }}</b></span>
    </h5>



    <div v-for="image in post?.images" class="mb-3 text-center">
        <img :src="image.file" alt="Imagen" class="img-fluid">
    </div>

    <MapaPosicionado :post="post" ref="mapContainer" class="mb-4"/>


    <!-- Muestra los comentarios -->



    <!-- <form @submit.prevent="commentForm.post(route('comment.store', post), {preserveScroll: true}); commentForm.comment='';">
        <div class="row mb-4">
            <div class="col-12">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-comment"></i></span>
                    <input v-model="commentForm.comment" class="form-control" aria- />
                    <button :disabled="commentForm.comment == '' || commentForm.processing" class="btn btn-outline-success" type="submit">{{ commentForm.processing ? 'Guardando...' : 'Guardar' }}</button>
                </div>
            </div>
        </div>
    </form> -->


</template>
