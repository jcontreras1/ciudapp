<script setup>
import { defineProps, ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import Tooltip from '@/Components/Tooltip.vue';

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

const comentar = () => {
    sending.value = true;
    axios.post(`/api/post/${props.post.id}/comment`, commentForm)
    .then((response) => {
        sending.value = false;
        myPost.value = response.data;
        console.log(myPost.value);
        commentForm.comment = "";
    })
    .catch(error => {
        sending.value = false;
        console.error(error);
        Swal.fire({
            title: '¡Error!',
            text: 'Ocurrió un error al comentar el post: ' + error.message,
            icon: 'error',
            confirmButtonText: 'Ok'
        });
    });
};
const likeComent = (id) => {
    sending.value = true;
    axios.post(`/api/comment/${id}/like`)
    .then((response) => {
        sending.value = false;
        myPost.value = response.data;
        
    })
    .catch(error => {
        sending.value = false;
        Swal.fire({
            title: '¡Error!',
            text: 'Ocurrió un error al sumarse al comentario: ' + error.message,
            icon: 'error',
            confirmButtonText: 'Ok'
        });
    });
}
const dislikeComment = (id, like) => {
    sending.value = true;
    axios.delete(`/api/comment/${id}/like/${like}`)
    .then((response) => {
        sending.value = false;
        myPost.value = response.data;
        
    })
    .catch(error => {
        sending.value = false;
        Swal.fire({
            title: '¡Error!',
            text: 'Ocurrió un error al sumarse al comentario: ' + error.message,
            icon: 'error',
            confirmButtonText: 'Ok'
        });
    });
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
</script>

<template>
    <!-- Muestra los comentarios -->
    <span class="fs-5 mx-3">
        <i class="fas fa-comments mr-2"></i> 
        <a class="link-underline link-underline-opacity-0">{{ myPost.comments.length }}</a>
    </span>
    <div class="card m-1" v-for="(comentario, index) in myPost.comments.slice(0, visibleCommentsCount)" :key="comentario.id">
        <div class="card-body">
            
            
            <div class="mb-2 d-flex justify-content-between">
                <div class="">
                    
                    <strong>{{ comentario.user?.name }}</strong> {{ comentario.created_at }}:
                </div>
                <div class="">
                    <Tooltip :list="comentario.likes">                         
                        <a class="link-underline link-underline-opacity-0 float-end" href="#" @click.prevent="toggleLike(comentario, $page.props.auth?.user?.id)">
                            <i class="fas fa-heart" 
                            :class="{'text-danger' : comentario.likes.map(element => element.id).includes($page.props.auth?.user?.id)}">
                        </i> &nbsp;{{ comentario.likes?.length }}
                    </a>
                </Tooltip>
            </div>
        </div>
        <h5 class="card-text">{{ comentario.comment }}
        </h5>
        
        
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
