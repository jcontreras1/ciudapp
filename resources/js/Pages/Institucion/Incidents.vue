<script setup>

import { defineProps, ref, onMounted, computed } from 'vue';
import MapaCalor from '../Mapa/MapaCalor.vue';
import ReportService from '@/Services/ReportService';
import Checkbox from '@/Components/Checkbox.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import SectionTitle from '@/Components/SectionTitle.vue';


const props = defineProps({
    incidents: {
        type: Object,
        required: true
    },
    institution: {
        type: Object,
        required: true
    }
});

const myIncidents = ref(props.incidents);


</script>

<template>    
    <AppLayout>

 <Head title="Incidentes" />
        <SectionTitle>
            <template #title>
                Instituciones
            </template>
            <template #aside>
                <!-- <Link v-if="$page.props.isAdmin" :href="route('')" class="btn btn-primary" title="Crear institución"><i class="fas fa-plus"></i> </Link> -->
            </template>
        </SectionTitle>  


        <div v-if="!myIncidents.length">
            <em>No hay incidentes creados</em>
        </div>
        <div v-else>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="incident in incidents" :key="incident.id">
                        <td>{{ incident.name }}</td>
                        <td>{{ incident.description }}</td>
                        <td>{{ incident.date }}</td>
                        <td>
                            <a class="btn btn-primary mr-1" :href="route('incidents.show', {'institution' : incident.institution_id, 'incident' : incident.id})">Ver</a>
                            <!-- <button class="btn btn-danger">Eliminar</button> -->
                        </td>
                </tr>
            </tbody>
        </table>
    </div>
</AppLayout>
    
</template>

<style>
.card-img-top {
    height: 200px;
    object-fit: cover;
}
</style>