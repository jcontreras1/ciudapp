<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import CardPost from '@/Components/CardPost.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SinglePost from './SinglePost.vue';
import Create from '@/Pages/Comment/Create.vue';
import Like from '@/Pages/Post/Like.vue';
import Tooltip from '@/Components/Tooltip.vue';
// const commentForm = useForm({
//     comment : "",
// });


defineProps({
    title: String,
    post: Object,
});

const emit = defineEmits(['showPostOnModal', 'deletePost']);

</script>

<template>
    
    <div class="card">
        <div class="card-body">
            <div class="text-muted" v-if="post.private">
                <i class="fas fa-eye-slash"></i> Este post es privado
            </div>
            <div class="d-flex justify-content-between">
                <div>
                    <h3 class="d-flex nav-link">
                        <span v-if="post.category?.name"><b> #{{ post.category?.name }}</b>&nbsp;</span>
                        <span v-if="post.subcategory?.name"><b> #{{ post.subcategory.name }} </b></span>
                    </h3>
                    <div class="mb-2">
                    {{post.comment }}
                    </div>
                    <i class="fas fa-calendar-alt"></i> 
                    {{ new Date(post.created_at).toLocaleDateString() }} 
                    
                    <!-- {{ post.valid_until ? ' - ' + post.valid_until : '' }} -->
                </div>   
                
                <div class="dropdown" v-if="post.user_id == $page.props.auth?.user?.id">
                    <a  data-bs-toggle="dropdown" href="#" aria-expanded="false">
                        <i class="fas fa-ellipsis-h"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="#" class="dropdown-item link-underline link-underline-opacity-0" @click="emit('deletePost', post)"><i role="button" title="Borrar" class="fas fa-trash-alt text-danger"></i> Eliminar reporte</a>
                        </li>
                    </ul>
                </div>           
            </div>
            <div v-if="post.location" class="mb-2">
                <i class="fas fa-map-marker-alt"></i>
                {{ post.location }}
            </div>
            <!-- <div v-if="post.comment">
                <hr>
                <span class="text-info">Esto es temporal:</span>
                <p v-html="post.comment"></p>
                <hr>
            </div>
             -->
            <div v-for="image in post.images" class="mb-2">
                <a href="#" @click.prevent="$emit('showPostOnModal', post)" data-bs-toggle="modal" data-bs-target="#modalShowPost">
                    <img :src="image.file" alt="Imagen" class="img-fluid w-30"/>
                </a>
            </div>
            
            <Like :post="post" v-if="$page.props.auth?.user" />
            
            <Create :post="post" v-if="$page.props.auth?.user"></Create>
        </div>
    </div>
    <!-- <hr> -->
    <!-- <img :src="post.image" alt="Imagen" class="img-fluid"> -->
</template>
