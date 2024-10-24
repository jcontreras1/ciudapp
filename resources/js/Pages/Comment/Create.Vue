<script setup>

import { defineProps, ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import Tooltip from '@/Components/Tooltip.vue';
import Swal from 'sweetalert2';
import CommentService from '@/Services/CommentService';

const props = defineProps({
    post: Object,
});
const myPost = ref(props.post);
const sending = ref(false);
const commentForm = useForm({
    comment: "",
});

// Variable para controlar cuántos comentarios se muestran
const visibleCommentsCount = ref(3); // Muestra 3 comentarios inicialmente

const comentar = async () => {
    sending.value = true;
    const response = await CommentService.createComment(`/post/${props.post.id}/comment`, commentForm);
    sending.value = false;
    myPost.value = response.data;
    commentForm.comment = "";
    
};

const likeComent = async(id) => {
    sending.value = true;
    const response = await CommentService.createLink(`/comment/${id}/like`);
    sending.value = false;
    myPost.value = response.data;
}

const dislikeComment = async (id, like) => {
    sending.value = true;
    const response = await CommentService.destroyLikeComment(`/comment/${id}/like/${like}`);
    sending.value = false;
    myPost.value = response.data;
}

const toggleLike = (comment, yo) => {
    
    if (comment.likes.map(element => element.id).includes(yo)) {
        dislikeComment(comment.id, comment.likes.filter(elem => elem.id === yo)[0]?.pivot.id);
    } else {
        likeComent(comment.id);
    }
};

// Función para mostrar todos los comentarios
const mostrarTodosComentarios = () => {
    visibleCommentsCount.value = myPost.value.comments.length; // Muestra todos los comentarios
};

const eliminarComentario = async (comment) => {
    const result = await Swal.fire({
        title: '¿Eliminar Comentario?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (result.isConfirmed) {
        const response = await CommentService.deleteComment(`/comment/${comment.id}/delete`);
        myPost.value = response.data;
    }
}

</script>

<template>
    <!-- Muestra los comentarios -->
    <span class="fs-5 mx-3">
        <i class="fas fa-comments mr-2"></i> 
        <a class="link-underline link-underline-opacity-0">{{ myPost.comments.length }}</a>
    </span>
    <div class="card m-1" v-for="(comentario, index) in myPost.comments.slice(0, visibleCommentsCount)" :key="comentario.id">
        <div class="card-body">
            <div class="mb-2 ">
                <div class="">                    
                    <strong>{{ comentario.user?.name }}</strong> {{ comentario.created_at }}:
                </div>               
            </div>
            <h5 class="card-text">{{ comentario.comment }}</h5>
            <div class="">
                <Tooltip :list="comentario.likes">                         
                    <a class="link-underline link-underline-opacity-0 mr-2" href="#" @click.prevent="toggleLike(comentario, $page.props.auth?.user?.id)">
                        <i class="fas fa-heart" 
                        :class="{'text-danger' : comentario.likes.map(element => element.id).includes($page.props.auth?.user?.id)}">
                    </i> &nbsp;{{ comentario.likes?.length }}                    
                </a>
                {{  }}
                <span v-if="comentario.user.id == $page.props.auth?.user?.id ">
                    <a href="#" class="link-underline link-underline-opacity-0" @click="eliminarComentario(comentario)"><i class="fas fa-trash-alt text-danger"></i></a>
                </span>
            </Tooltip>
        </div>
    </div>
</div>

<!-- Botón "Cargar más" si hay más comentarios -->
<div v-if="myPost.comments.length > visibleCommentsCount" class="text-left ml-2">
    <a href="#" @click="mostrarTodosComentarios">Cargar {{ myPost.comments.length - visibleCommentsCount}} comentarios más</a>
</div>

<div class="py-1"></div>
<!-- Crea un comentario -->
<form @submit.prevent="comentar">
    <div class="row">
        <div class="col-12">
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-comment"></i></span>
                <input v-model="commentForm.comment" class="form-control" aria-label="With textarea">
                <button :disabled="sending" class="btn btn-outline-success" type="submit">{{ sending ? 'Guardando...' : 'Guardar' }}</button>
            </div>
        </div>
    </div>
</form>
</template>
