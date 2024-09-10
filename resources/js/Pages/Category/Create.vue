<script setup>
import { reactive, ref } from 'vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    icon: '',
});

defineProps({
    
    categorias:{
        type: Array,
        // required: true,
    },

    errors: {
        type: Object,
    },
    
});
</script>

<template>
    <Head title="Nueva categoría" />
    
    <div class="container my-4">
        <h1 class="fs-3">Crear categoría</h1>
        <hr class="mb-3">
        
        <form @submit.prevent="form.post(route('category.store'))">
            <div class="mb-3">
                <label for="name" class="form-label">Nombre</label>
                <input type="text" autocomplete="off" required class="form-control" :class="{'is-invalid' : errors.name}" id="name" autofocus v-model="form.name">
                <div class="text-danger">{{ errors.name }}</div>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Ícono <a href="https://fontawesome.com/v5/search?o=r&m=free" target="_blank"><i class="fas fa-external-link-alt"></i></a></label>
                <input class="form-control" id="description" v-model="form.icon" :class="{'is-invalid' : errors.icon}">
                <div class="text-danger">{{ errors.icon }}</div>
                <span class="fs-1" v-html="form.icon"></span>
            </div>
            <button type="submit" :disabled="form.processing" class="btn btn-primary">{{ form.processing ? 'Guardando...' : 'Guardar' }}</button>
        </form>

    </div>
</template>