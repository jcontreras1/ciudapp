<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SectionTitle from '@/Components/SectionTitle.vue';
import DialogModal from '@/Components/DialogModal.vue';

defineProps({
    
    categorias:{
        type: Array,
        // required: true,
    },
});

const destroy = (categoria) => {
    showModal.value = true;
    selectedCategory.value = categoria;
}
const deleteCategory = () => {
    router.delete(route('category.destroy', selectedCategory.value.id), {preserveScroll: true});
    ocultarModal();
}

const ocultarModal = () => { showModal.value = false; }
const showModal = ref(false);
const selectedCategory = ref(null);

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
        <div v-if="categorias.length">
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
                            <Link class="btn btn-primary btn-sm" title="Editar" :href="route('category.edit', categoria.id)">Editar</Link>
                            <button class="btn btn-danger btn-sm" title="Eliminar" @click="destroy(categoria)">Eliminar</button>
                            <!-- <button class="btn btn-danger btn-sm" title="Eliminar" @click="destroy(categoria.id)">Eliminar</button> -->
                        </td>
                    </tr>
                </tbody>
            </table>            
        </div>
        </div>

        <div v-else>
            <em>No hay categorías</em>
        </div>
    </AppLayout>
    <DialogModal :show="showModal">
        <template #title>¿Eliminar la categoría <strong>{{ selectedCategory.name }}?</strong></template>
        <!-- <template #content>asdasda</template> -->
        <template #footer>
            <button @click="deleteCategory" class="btn btn-danger mx-1">Eliminar</button>
            <button @click.prevent="ocultarModal" class="btn btn-secondary">Cerrar</button>
        </template>
    </DialogModal>
</template>