<script setup>
import { reactive, ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3';

const form = reactive({
    contenido: null,
    latitud: null,
    image: null,
})

defineProps({
    
    categorias:{
        type: Array,
        // required: true,
    },
    
});
</script>

<template>
    <Head title="Nueva categoría" />
    
    <div class="container my-4">
        <h1 class="fs-3">Crear categoría
            <span class="float-end">
                <a class="btn btn-primary" :href="route('category.create')">Crear</a>
            </span>
        </h1>
        <hr class="mb-3">
        <form @submit.prevent="submitForm">
            <div class="mb-3">
                <label for="name" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="name" v-model="form.name">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Descripción</label>
                <textarea class="form-control" id="description" v-model="form.description"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </form>
    </div>
</template>