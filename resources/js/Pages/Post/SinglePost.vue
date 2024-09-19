<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import mapboxgl from 'mapbox-gl';
import { onMounted, ref, watch } from 'vue';

const commentForm = useForm({
    comment : "",
});

const apiKey = import.meta.env.VITE_MAPBOX_TOKEN;
const mapContainer = ref(null);

const props = defineProps({
    post: Object,
});

watch(() => props.post, () => {
    if (props.post) {
        const map = new mapboxgl.Map({
            container: mapContainer.value, // container ID
            style: 'mapbox://styles/mapbox/streets-v12', // style URL
            center: [props.post?.lng, props.post?.lat], // starting position [lng, lat]
            zoom: 9, // starting zoom
        });
    }
});

onMounted (() => {
    mapboxgl.accessToken = apiKey;

});

</script>

<template>

    <i class="fas fa-map-pin"></i> <b>{{ new Date(post?.created_at).toLocaleDateString() }} </b>
    <h5>{{ post?.comment }}</h5>
    <h5 class="d-flex nav-link">
        <span><b> #{{ post?.subcategory?.name }} </b></span>
        <span><b> #{{ post?.category?.name }}</b></span>
    </h5>



    <div v-for="image in post?.images" class="mb-3">
        <img :src="image.file" alt="Imagen" class="img-fluid">
    </div>

    <div class="row">
        <div class="col-12 mb-1"  style="width: 100%; height: 300px;">
            <div ref="mapContainer" style="width: 100%; height: 300px;"></div>
        </div>
        <div class="clearfix"></div>
    </div>


    <!-- Muestra los comentarios -->
    <div class="fs-5 mb-1 ">
        <i class="fas fa-comments"></i> Comentarios
        <span class="badge rounded-pill text-bg-primary">{{post?.comments?.length}}</span>
    </div>
    <!-- <p v-if="post.comments.length == 0">Sin comentarios</p> -->
    <div class="mb-2" v-for="comentario in post?.comments">
        <i class="fas fa-user"></i> - <b>{{ new Date(comentario?.created_at).toLocaleDateString() }}</b>
        <h5 class="card-text">{{comentario?.comment}}</h5>
    </div>
    <div class="py-1"></div>


    <form @submit.prevent="commentForm.post(route('comment.store', post), {preserveScroll: true}); commentForm.comment='';">
        <div class="row mb-4">
            <div class="col-12">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-comment"></i></span>
                    <textarea v-model="commentForm.comment" class="form-control" aria-label="With textarea"></textarea>
                    <button :disabled="commentForm.comment == '' || commentForm.processing" class="btn btn-outline-success" type="submit">{{ commentForm.processing ? 'Guardando...' : 'Guardar' }}</button>
                </div>
            </div>
        </div>
    </form>


</template>
