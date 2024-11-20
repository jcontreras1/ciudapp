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
        
        <div class="row" v-if="instituciones?.data?.length">
            <div class="col-12 col-md-3" v-for="institucion in instituciones?.data" :key="institucion.id">              

                <div class="card ">
                    <div class="card-body">
                        <h5 class="card-title text-center"  >{{ (institucion.name.toUpperCase()) }}</h5>
                        <p class="text-muted text-center"><em>{{institucion.city.name}} - {{ institucion.city.province.name }}</em></p>
                        <!-- <hr class="text-primary"> -->
                    </div>
                    <div class="btn-group" role="group">
                        <a :href="route('incidents.index', institucion)" title="Incidentes" class="btn btn-sm btn-info "><i class="fas fa-exclamation-triangle"></i></a>
                        <a :href="route('institution.edit', institucion)" title="Editar Institución" class="btn btn-sm btn-primary "><i class="fas fa-edit"></i></a>
                        <button v-if="$page.props.isAdmin" @click="destroy(institucion)" class="btn btn-sm btn-danger" title="Eliminar"><i class="fas fa-trash-alt"></i></button>
                    </div>                
                </div>    
            </div>
        </div>
        
        <div v-else>
            <em>No hay instituciones</em>
        </div>
    </AppLayout>
</template>
