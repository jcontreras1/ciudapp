<script setup>

import { defineProps } from 'vue';
import SinglePost from '@/Pages/Post/SinglePost.vue';


const props = defineProps({
    post: Object,
});
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
                    <SinglePost :post="post"></SinglePost>
                </div>
            </div>
        </div>
    </div>


    </template>
