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

// Estado para saber qué comentario estamos editando
const editingCommentId = ref(null); 
const editingCommentText = ref("");

// Función para editar un comentario
const editarComentario = (comentario) => {
    editingCommentId.value = comentario.id;
    editingCommentText.value = comentario.comment; // Cargar el texto del comentario para editarlo
};

// Función para guardar los cambios del comentario
const actualizarComentario = async () => {
    sending.value = true;

    // Asegúrate de incluir tanto el postId como el commentId en la URL
    const response = await CommentService.updateComment(`/post/${props.post.id}/comment/${editingCommentId.value}/actualizar`, { comment: editingCommentText.value });

    sending.value = false;
    myPost.value = response.data;  // Asumiendo que el backend devuelve el post actualizado
    editingCommentId.value = null;  // Resetear el estado de edición
    editingCommentText.value = "";  // Limpiar el texto del comentario
};


// Función para cancelar la edición
const cancelarEdicion = () => {
    editingCommentId.value = null;
    editingCommentText.value = "";
};

// Función para crear un nuevo comentario
const comentar = async () => {
    sending.value = true;
    const response = await CommentService.createComment(`/post/${props.post.id}/comment`, commentForm);
    sending.value = false;
    myPost.value = response.data;
    commentForm.comment = "";
};

// Función para like/unlike comentario
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
                    <strong>{{ comentario.user?.name }} </strong> {{ comentario.created_at }}: 
                    <span class="float-end" v-if="comentario.user.id == $page.props.auth?.user?.id">
                        <div class="dropdown">
                            <a  data-bs-toggle="dropdown" href="#" aria-expanded="false">
                                <i class="fas fa-ellipsis-h"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="#" @click="editarComentario(comentario)"><i class="fas fa-edit text-primary"></i> Editar</a>
                                </li>
                                <li>
                                    <a href="#" class="dropdown-item link-underline link-underline-opacity-0" @click="eliminarComentario(comentario)"><i class="fas fa-trash-alt text-danger"></i> Eliminar</a>
                                </li>
                            </ul>
                        </div>                   
                    </span>
                </div>               
            </div>
            
            <!-- Mostrar campo de edición si estamos editando este comentario -->
            <div v-if="editingCommentId === comentario.id">
                <input 
                v-model="editingCommentText" 
                class="form-control" 
                type="text" 
                @keyup.enter="actualizarComentario" 
                />
                
                <div class="mt-2">
                    <button 
                    class="btn btn-outline-success mr-2" 
                    @click="actualizarComentario" 
                    :disabled="sending">
                    {{ sending ? 'Guardando...' : 'Guardar' }}
                </button>
                <button class="btn btn-outline-secondary" @click="cancelarEdicion">Cancelar</button>
            </div>
        </div>
        
        <!-- Mostrar el comentario normal si no está en modo de edición -->
        <h5 v-else class="card-text">{{ comentario.comment }}</h5>
        
        <div class="mt-2">
            <Tooltip :list="comentario.likes">                         
                <a class="link-underline link-underline-opacity-0 mr-2" href="#" @click.prevent="toggleLike(comentario, $page.props.auth?.user?.id)">
                    <i class="fas fa-heart" 
                    :class="{'text-danger' : comentario.likes.map(element => element.id).includes($page.props.auth?.user?.id)}">
                </i> &nbsp;{{ comentario.likes?.length }}                    
            </a>
        </Tooltip>
        <span class="float-end text-muted" v-if="comentario.created_real !== comentario.updated_real"><em>Comentario editado</em></span>

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
