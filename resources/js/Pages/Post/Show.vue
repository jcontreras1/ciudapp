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

// const showingNavigationDropdown = ref(false);
let selectedPost = ref(null)
let showModal = ref(false)
//Cerrar sesión (para después)
const logout = () => {
    router.post(route('logout'));
};
</script>

<template>

    <!-- Abre el modal al presionar un post -->
    <div class="modal fade" id="modalShowPost" tabindex="-1" aria-labelledby="modalShowPostLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <SinglePost :post="selectedPost"></SinglePost>
                </div>
            </div>
        </div>
    </div>

    <!-- <CardPost :posts="posts" /> -->
        <span class="fs-1">{{ post.id }}</span>
        <span class="nav-link text-primary">#{{ post.subcategory.name }}</span>
        <span class="nav-link text-primary">#{{ post.category?.name }}</span>
        {{ post.comment }}
        <div v-for="image in post.images" class="mb-2">
            <a href="#" @click.prevent="selectedPost = post;" data-bs-toggle="modal" data-bs-target="#modalShowPost">
                <img :src="image.file" alt="Imagen" class="img-fluid w-50" />
            </a>
        </div>

        <!--Crear Comentario -->
        <!-- <form @submit.prevent="commentForm.post(route('comment.store', post), {preserveScroll: true}); commentForm.comment='';">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-comment"></i></span>
                        <input v-model="commentForm.comment" class="form-control" aria-label="With textarea">
                        <button :disabled="commentForm.comment == '' || commentForm.processing" class="btn btn-outline-success" type="submit">{{ commentForm.processing ? 'Guardando...' : 'Guardar' }}</button>
                    </div>
                </div>
            </div>
        </form> -->
        <Create :post="post" />
        <hr>
        <!-- <img :src="post.image" alt="Imagen" class="img-fluid"> -->
</template>
