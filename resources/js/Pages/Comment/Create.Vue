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

    <!-- Diseño para mostrar comentarios -->
    <div>
        <u><i class="fas fa-comments"> Comentarios</i></u>
        <div v-for="comentario in post.comments">
            <div class="d-flex justify-content-between">
                <h4>Karen</h4>
                <h4>- {{ comentario.comment }}</h4>

            </div>
        </div>

    </div>

    {{post.comments}}
    <form @submit.prevent="commentForm.post(route('comment.store', post), {preserveScroll: true}); commentForm.comment='';">
        <div class="row mb-4">
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
