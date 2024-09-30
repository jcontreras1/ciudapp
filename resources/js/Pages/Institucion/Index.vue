<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, defineProps} from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import SectionTitle from '@/Components/SectionTitle.vue';


const props = defineProps({
    instituciones: {
        type: Object,

    }
})
</script>

<template>
    <AppLayout>
        <Head title="Instituciones" />
        <SectionTitle>
            <template #title>
                Instituciones
            </template>
            <template #aside>
                <Link :href="route('institution.create')" class="btn btn-primary" title="Crear institución"><i class="fas fa-plus"></i> </Link>
            </template>
        </SectionTitle>


        <hr class="mb-3">
        <div v-if="instituciones?.data?.length">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Dirección</th>
                            <th>City</th>
                            <th>Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="institucion in instituciones?.data" :key="institucion.id">
                            <td>{{ institucion.name }}</td>
                            <td>{{ institucion.mail }}</td>
                            <td>{{ institucion.address }}</td>
                            <td>{{ institucion.city_id }}</td>
                            <td class="float-end">
                                <a :href="route('institution.edit', institucion)" class="btn btn-primary mr-1"><i class="fas fa-edit"></i></a>
                                <button @click="destroy(institucion)" class="btn btn-danger"><i class="fas fa-trash-alt"></i></button>
                            </td>

                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-else>
            <em>No hay instituciones</em>
        </div>
    </AppLayout>
</template>
