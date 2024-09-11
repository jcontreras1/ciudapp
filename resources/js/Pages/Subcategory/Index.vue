<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SectionTitle from '@/Components/SectionTitle.vue';
import DialogModal from '@/Components/DialogModal.vue';

defineProps({
    
    subcategorias:{
        type: Array,
        // required: true,
    },
    categoria:{
        type: Object,
        // required: true,
    },
});

const destroy = (subcategoria) => {
    showModal.value = true;
    selectedSubcategory.value = subcategoria;
}
const deleteSubcategory = () => {
    router.delete(route('subcategory.destroy', {'category' : category, 'subcategory' : selectedSubcategory.value.id}), {preserveScroll: true});
    ocultarModal();
}

const ocultarModal = () => { showModal.value = false; }
const showModal = ref(false);
const selectedSubcategory = ref(null);

</script>

<template>
    <AppLayout>        
        <Head title="Subcategorías" />

        <SectionTitle>
                <template #title>
                    Crear subcategoría en <strong>{{ categoria.name }}</strong>
                </template>
                <template #aside>
                    <Link :href="route('subcategory.create', categoria)" class="btn btn-primary">Crear subcategoría</Link>
                    </template>
            </SectionTitle>
      
        <hr class="mb-3">
        <div v-if="subcategorias.length">
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
                    <tr v-for="subcategoria in subcategorias" :key="subcategoria.id">
                        <td>{{ subcategoria.name }}</td>
                        <td><span class="fs-3" v-html="subcategoria.icon"></span></td>
                        <td>
                            <Link class="btn btn-primary btn-sm" title="Editar" :href="route('subcategory.edit', {'category' : categoria.id, 'subcategory' : subcategoria.id})">Editar</Link>
                            <button class="btn btn-danger btn-sm" title="Eliminar" @click="destroy(subcategoria)">Eliminar</button>
                            <!-- <button class="btn btn-danger btn-sm" title="Eliminar" @click="destroy(subcategoria.id)">Eliminar</button> -->
                        </td>
                    </tr>
                </tbody>
            </table>            
        </div>
        </div>

        <div v-else>
            <em>No hay subcategorías</em>
        </div>

    </AppLayout>
    <DialogModal :show="showModal">
        <template #title>¿Eliminar la subcategoría <strong>{{ selectedSubcategory.name }}?</strong></template>
        <!-- <template #content>asdasda</template> -->
        <template #footer>
            <button @click="deleteSubcategory" class="btn btn-danger mx-1">Eliminar</button>
            <button @click.prevent="ocultarModal" class="btn btn-secondary">Cerrar</button>
        </template>
    </DialogModal>
</template>