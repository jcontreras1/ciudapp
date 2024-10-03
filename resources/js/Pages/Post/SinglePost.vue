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
    <div class="fs-5 mb-3">
        <i class="fas fa-comments"></i> Comentarios
        <span class="badge rounded-pill text-bg-primary">{{post?.comments?.length}}</span>
    </div>
    <!-- <p v-if="post.comments.length == 0">Sin comentarios</p> -->
    <div class="mb-2" v-for="comentario in post?.comments">
        <i class="fas fa-user"></i> - <b>{{ new Date(comentario?.created_at).toLocaleDateString() }}</b>
        <h5 class="card-text">{{comentario?.comment}}</h5>
        <hr>
    </div>
    
    
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
