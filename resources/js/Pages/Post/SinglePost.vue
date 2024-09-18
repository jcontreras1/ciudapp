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

        <span class="nav-link text-primary">#{{ post?.subcategory?.name }}</span>
        <span class="nav-link text-primary">#{{ post?.category?.name }}</span>

        <div v-for="image in post?.images" class="mb-3">
            <img :src="image.file" alt="Imagen" class="img-fluid">
        </div>

        <div class="row">
            <div class="col-12 mb-1"  style="width: 100%; height: 300px;">
                <div ref="mapContainer" style="width: 100%; height: 300px;"></div>
            </div>
            <div class="clearfix"></div>
        </div>

        <pre>
            {{ post?.comments }}
        </pre>
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
