<script setup>
import { reactive, ref } from 'vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SectionTitle from '@/Components/SectionTitle.vue';

const props = defineProps({
    category: {
        type: Object,
        required: true,
    },
    subcategory: {
        type: Object,
        required: true,
    },
    errors: {
        type: Object,
    },
});

const form = useForm({
    // id: props.subcategory.id,
    name: props.subcategory.name,
    icon: props.subcategory.icon,
});

</script>

<template>
    <AppLayout>        
        <Head title="Editar categoría" />
        <div class="container my-4">
            <SectionTitle :errors="errors">
                <template #title>
                    Editar subcategoría <strong>{{ subcategory.name }}</strong>
                </template>
            </SectionTitle>
            <hr class="mb-3">
            <form @submit.prevent="form.put(route('subcategory.update', {'category' : category.id, 'subcategory' : subcategory.id}))">
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
    </AppLayout>
</template>