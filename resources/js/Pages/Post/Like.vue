<script setup>

import { defineProps, ref } from 'vue';
import Tooltip from '@/Components/Tooltip.vue';
import PostService from '@/Services/PostService';
const props = defineProps({
    post: Object,
});

const myPost = ref(props.post);
const sending = ref(false)

const likePost = async() => {
    sending.value = true;
    const response = await PostService.likePost(`/post/${props.post.id}/like`);
    sending.value = false;
    myPost.value = response.data;
    
}

</script>

<template>
    <span class="float-end">
        <Tooltip :list="myPost.likes">
            <a class="link-underline link-underline-opacity-0" href="#" @click.prevent="likePost">
                <i class="fas fa-heart" :class="{'text-danger' : myPost.likes?.map(like => like.user_id).includes($page.props.auth?.user?.id)}"></i> {{ myPost.likes?.length }}
            </a>
        </Tooltip>
    </span>
    <!-- <span class="float-end"> -->
        <!-- <a class="link-underline link-underline-opacity-0" href="#" @click.prevent="likePost"> -->
            <!-- <i class="fas fa-heart" :class="{'text-danger' : myPost.likes?.map(like => like.user_id).includes($page.props.auth?.user?.id)}"></i> {{ myPost.likes?.length }} -->
        <!-- </a> -->
    <!-- </span> -->
</template>