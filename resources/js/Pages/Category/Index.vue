<script setup>
import { reactive, ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SectionTitle from '@/Components/SectionTitle.vue';

defineProps({
    
    categorias:{
        type: Array,
        // required: true,
    },
    
});
</script>

<template>
    <AppLayout>        
        <Head title="Categorías" />

        <SectionTitle>
                <template #title>
                    Crear categoría
                </template>
                <template #aside>
                    <Link :href="route('category.create')" class="btn btn-primary">Crear categoría</Link>
                    </template>
            </SectionTitle>
      
        <hr class="mb-3">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Icono</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="categoria in categorias" :key="categoria.id">
                        <td>{{ categoria.name }}</td>
                        <td><span class="fs-3" v-html="categoria.icon"></span></td>
                        <td>
                            <Link class="btn btn-primary btn-sm" :href="route('category.edit', categoria.id)">Editar</Link>
                            <button class="btn btn-danger btn-sm" @click="router.push({name: 'category.delete', params: {id: categoria.id}})">Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            
        </div>
    </AppLayout>
</template>