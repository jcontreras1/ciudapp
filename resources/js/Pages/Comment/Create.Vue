<script setup>
import { reactive, ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router, useForm  } from '@inertiajs/vue3';


const props = defineProps({
    post: Object,
});
const myPost = ref(props.post);
const sending = ref(false)
const commentForm = useForm({
    comment : "",
});

const comentar = () => {
    sending.value = true
    axios.post(`/api/post/${props.post.id}/comment`, commentForm)
    .then((response) => {
        sending.value = false;
        myPost.value = response.data;
        commentForm.comment = "";
    })
    .catch(error => {
        sending.value = false;
        console.error(error)
        Swal.fire({
            title: '¡Error!',
            text: 'Ocurrió un error al comentar el post: ' + error.message,
            icon: 'error',
            confirmButtonText: 'Ok'
        });
    });
}

</script>
<template>
    <!-- Muestra los comentarios -->
    <div class="fs-5 mb-1 ">
        <i class="fas fa-comments"></i> Comentarios
        <span class="badge rounded-pill text-bg-primary">{{myPost.comments.length}}</span>
    </div>
    <!-- <p v-if="post.comments.length == 0">Sin comentarios</p> -->
    <div class="card" v-for="comentario in myPost.comments">
        <div class="card-body">
            <b>{{ comentario.user? comentario.user.name : 'Usuario no registrado' }}</b> el {{ new Date(comentario.created_at).toLocaleDateString() }}:
            <h5 class="card-text">{{comentario.comment}}</h5>
        </div>
    </div>
    <div class="py-1"></div>
    <!-- Crea un comentario -->

    <form @submit.prevent="comentar" v-if="$page.props.auth.user">
        <div class="row ">
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
