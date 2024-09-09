<script setup>
import { reactive, ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3';


defineProps({
    
    categorias:{
        type: Array,
        // required: true,
    },
    
});
</script>

<template>
    <Head title="Categorías" />
    
    <div class="container my-4">
        <h1 class="fs-3">Categorías
            <span class="float-end">
                <a class="btn btn-primary" :href="route('category.create')">Crear</a>
            </span>
        </h1>
        <hr class="mb-3">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="categoria in categorias" :key="categoria.id">
                        <td>{{ categoria.name }}</td>
                        <td>{{ categoria.description }}</td>
                        <td>
                            <button class="btn btn-primary btn-sm" @click="router.push({name: 'category.edit', params: {id: categoria.id}})">Editar</button>
                            <button class="btn btn-danger btn-sm" @click="router.push({name: 'category.delete', params: {id: categoria.id}})">Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            
        </div>
    </div>
</template>