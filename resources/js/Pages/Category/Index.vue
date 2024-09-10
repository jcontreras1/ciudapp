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
      
        <div v-if="$page.props.flash.message" class="alert alert-success alert-dismissible fade show mt-3 mb-3" role="alert">
            <i class="fas fa-check-circle"></i>
            {{ $page.props.flash.message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        
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
                            <button class="btn btn-primary btn-sm" @click="router.push({name: 'category.edit', params: {id: categoria.id}})">Editar</button>
                            <button class="btn btn-danger btn-sm" @click="router.push({name: 'category.delete', params: {id: categoria.id}})">Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            
        </div>
    </AppLayout>
</template>