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
        
        <div class="row d-flex justify-content-evenly" v-if="instituciones?.data?.length">
            <div class="col-md-4" v-for="institucion in instituciones?.data" :key="institucion.id">              
                <div class="card border-0 h-100">
                    <div class="card-body text-center">
                        <img class="rounded-circle mb-4" width="100" :src="`https://ui-avatars.com/api/?name=${institucion.name.toUpperCase()[0]}&color=7F9CF5&background=EBF4FF`">
                        <div class="fs-2 text-center mb-3"  >{{ (institucion.name.toUpperCase()) }}</div>
                        <!-- <hr class="text-primary"> -->
                        <strong>{{ institucion.address }}</strong>
                        <p class="text-muted text-center">{{institucion.city.name}} - {{ institucion.city.province.name }}</p>
                    </div>

                    <a :href="route('incidents.index', institucion)" title="Incidentes" class="btn btn-outline-info mb-1">
                        <i class="fas fa-exclamation-triangle"></i> <span class="d-none d-md-inline-block">Incidentes</span> 
                    </a>
                    <a :href="route('institution.edit', institucion)" title="Editar Institución" class="btn btn-outline-primary mb-1">
                        <i class="fas fa-edit"></i> <span class="d-none d-md-inline-block">Editar</span> 
                        
                    </a>
                    <button v-if="$page.props.isAdmin" @click="destroy(institucion)" class="btn btn-outline-danger mb-1" title="Eliminar">
                        <i class="fas fa-trash-alt"></i> <span class="d-none d-md-inline-block">Eliminar</span> 
                    </button>
                    <div class="btn-group" role="group">
                    </div>                
                </div>    
            </div>
        </div>
        
        <div v-else>
            <em>No hay instituciones</em>
        </div>
    </AppLayout>
</template>
