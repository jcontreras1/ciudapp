<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import CardPost from '@/Components/CardPost.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SinglePost from './SinglePost.vue';
import Create from '@/Pages/Comment/Create.vue';

// const commentForm = useForm({
//     comment : "",
// });


defineProps({
    title: String,
    post: Object,
});

const emit = defineEmits(['showPostOnModal']);


// const showingNavigationDropdown = ref(false);
let selectedPost = ref(null)
let showModal = ref(false)
//Cerrar sesión (para después)
const logout = () => {
    router.post(route('logout'));
};
</script>

<template>


    <div class="card">
        <div class="card-body">
            <i class="fas fa-map-pin"></i> <b>{{ new Date(post.created_at).toLocaleDateString() }} </b>
            <h5>{{ post.comment }}</h5>
            <h5 class="d-flex nav-link">
                <span><b> #{{ post.subcategory.name }} </b></span>
                <span><b> #{{ post.category?.name }}</b></span>
            </h5>
            <div v-for="image in post.images" class="mb-2">
                <a href="#" @click.prevent="$emit('showPostOnModal', post)" data-bs-toggle="modal" data-bs-target="#modalShowPost">
                    <img :src="image.file" alt="Imagen" class="img-fluid w-30"/>
                </a>
            </div>

            <div>
                <Create :post="post"></Create>
            </div>
        </div>
    </div>
    <!-- <hr> -->
    <!-- <img :src="post.image" alt="Imagen" class="img-fluid"> -->
</template>
