<script setup>

import { defineProps, ref } from 'vue';

const props = defineProps({
    post: Object,
});

const myPost = ref(props.post);
const sending = ref(false)

const likePost = () => {
    sending.value = true;
    axios.post(`/api/post/${props.post.id}/like`)
    .then((response) => {
        sending.value = false;
        myPost.value = response.data;
    })
    .catch(error => {
        sending.value = false;
        console.error(error)
        Swal.fire({
            title: '¡Error!',
            text: 'Ocurrió un error al sumarse al post: ' + error.message,
            icon: 'error',
            confirmButtonText: 'Ok'
        });
    });
}

</script>

<template>
    <span class="float-end">
        <a class="link-underline link-underline-opacity-0" href="#" @click.prevent="likePost">
            <i class="fas fa-heart" :class="{'text-danger' : myPost.likes?.map(like => like.user_id).includes($page.props.auth?.user?.id)}"></i> {{ myPost.likes?.length }}
        </a>
    </span>
</template>