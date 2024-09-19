<script setup>
import { reactive, ref } from 'vue'
import { Head, Link, router, useForm  } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';


defineProps({
    post: Object,
});

const commentForm = useForm({
    comment : "",
});

</script>
<template>
    <!-- Muestra los comentarios -->
    <div class="fs-5 mb-1 ">
        <i class="fas fa-comments"></i> Comentarios
        <span class="badge rounded-pill text-bg-primary">{{post.comments.length}}</span>
    </div>
    <!-- <p v-if="post.comments.length == 0">Sin comentarios</p> -->
    <div class="mb-2" v-for="comentario in post.comments">
       <i class="fas fa-user"></i> - <b>{{ new Date(comentario.created_at).toLocaleDateString() }}</b>
        <h5 class="card-text">{{comentario.comment}}</h5>
    </div>
    <div class="py-1"></div>
    <!-- Crea un comentario -->
    <form @submit.prevent="commentForm.post(route('comment.store', post), {preserveScroll: true}); commentForm.comment='';">
        <div class="row ">
            <div class="col-12">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-comment"></i></span>
                    <input v-model="commentForm.comment" class="form-control" aria-label="With textarea">
                    <button :disabled="commentForm.comment == '' || commentForm.processing" class="btn btn-outline-success" type="submit">{{ commentForm.processing ? 'Guardando...' : 'Guardar' }}</button>
                </div>
            </div>
        </div>
    </form>
</template>
