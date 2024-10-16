<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, defineProps} from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import SectionTitle from '@/Components/SectionTitle.vue';


const props = defineProps({
    instituciones: {
        type: Object,
        
    }
});

const destroy = (institution) => {
    Swal.fire({
        title: `¿Eliminar institución: ${institution.name}?`,
        text: "También se eliminarán las  regiones y usuarios de esta institución",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('institution.destroy', institution.id));
        }
    });
}
</script>

<template>
    <AppLayout>
        <Head title="Instituciones" />
        <SectionTitle>
            <template #title>
                Instituciones
            </template>
            <template #aside>
                <Link v-if="$page.props.isAdmin" :href="route('institution.create')" class="btn btn-primary" title="Crear institución"><i class="fas fa-plus"></i> </Link>
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
                            <th>Ciudad</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="institucion in instituciones?.data" :key="institucion.id">
                            <td>{{ institucion.name }}</td>
                            <td>{{ institucion.mail }}</td>
                            <td>{{ institucion.address }}</td>
                            <td>{{ institucion.city.name }} - {{ institucion.city.province.name }}</td>
                            <td>
                                <div class="float-end">
                                    <a :href="route('institution.edit', institucion)" class="btn btn-sm btn-primary mr-1"><i class="fas fa-edit"></i></a>
                                    <button v-if="$page.props.isAdmin" @click="destroy(institucion)" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i></button>
                                </div>
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
