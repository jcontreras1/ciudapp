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
                Categorías
            </template>
            <template #aside>
                <Link :href="route('relationships')" class="btn btn-primary mr-1">Relaciones</Link>
                <Link :href="route('category.create')" class="btn btn-primary">Crear categoría</Link>
            </template>
        </SectionTitle>
        <hr>
        <div v-if="categorias.length">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Icono</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="categoria in categorias" :key="categoria.id">
                            <td><Link :href="route('subcategory.index', categoria.id)">
                                <strong>{{ categoria.name }}</strong>
                                <small>
                                    <em>
                                        <span v-if="categoria.subcategories?.length > 1">&nbsp;&nbsp;{{ `[${categoria.subcategories?.length} subcategorías]` }}</span>
                                        <span v-else-if="categoria.subcategories?.length == 1">&nbsp;&nbsp;[1 subcategoría]</span>
                                    </em>
                                </small>
                            </Link></td>
                            <td><span class="fs-3" v-html="categoria.icon"></span></td>
                            <td>
                                <div class="float-end">
                                <Link class="btn btn-primary btn-sm mr-1" title="Editar" :href="route('category.edit', categoria.id)"><i class="fas fa-edit"></i></Link>
                                <button class="btn btn-danger btn-sm" title="Eliminar" @click="destroy(categoria)"><i class="fas fa-trash-alt"></i></button>
                                </div>
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
