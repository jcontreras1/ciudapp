<script setup>
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import CardPost from '@/Components/CardPost.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SinglePost from './SinglePost.vue';

const commentForm = useForm({
    comment : "",
});


defineProps({
    title: String,
    post: Object,
});


</script>

<template>
    <!-- <CardPost :posts="posts" /> -->
   
        <pre>
            {{ post.comments }}
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
        <span class="nav-link text-primary">#{{ post.subcategory.name }}</span>
        <span class="nav-link text-primary">#{{ post.category?.name }}</span>

        <div v-for="image in post.images">
            <a href="#" @click.prevent="selectedPost = post; showModal = true">
            <img :src="image.file" alt="Imagen" class="img-fluid w-50">
            </a>
        </div>
        <div class="col-12 col-md-4 d-none d-md-block mb-1">
            LATITUD: <p>{{post.lat }}</p>
        </div>
        <div class="col-12 col-md-4 d-none d-md-block mb-1">
            <input type="text" readonly id="longitud" name="longitud"
            class="form-control form-control-lg">
        </div>
        <!-- <img :src="post.image" alt="Imagen" class="img-fluid"> -->

</template>
