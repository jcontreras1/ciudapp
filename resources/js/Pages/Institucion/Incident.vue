<script setup>

import { defineProps, ref, onMounted, computed } from 'vue';
import MapaCalor from '../Mapa/MapaCalor.vue';
import ReportService from '@/Services/ReportService';
import Checkbox from '@/Components/Checkbox.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import SectionTitle from '@/Components/SectionTitle.vue';
import IncidentService from '@/Services/IncidentService';


const props = defineProps({
    myIncident: {
        type: Object,
        required: true
    },
    institution: {
        type: Object,
        required: true
    },
    postsRelacionados : {
        type: Object,
        required: true
    },
    estados : {
        type: Object,
        required: true
    }
});

const incident = ref(props.myIncident.data);
const nuevoEstado = ref(null)
const descripcionNuevoEstado = ref(null)
const guardarNuevoEstado = async (event) => {
    const res = await IncidentService.changeStatus(`/institution/${props.institution.id}/incident/${incident.value.id}/status`, 
    {
        'status_id': nuevoEstado.value,
        'description' : descripcionNuevoEstado.value
    });
    console.log(res.data);
    incident.value.history = res.data.history;
    nuevoEstado.value = null;
    descripcionNuevoEstado.value = null;
}

</script>

<template>    
    <AppLayout>
        
        <Head title="Incidentes" />
        <SectionTitle>
            <template #title>
                {{ incident.description }}
            </template>
            <template #aside>
                <!-- <Link v-if="$page.props.isAdmin" :href="route('')" class="btn btn-primary" title="Crear institución"><i class="fas fa-plus"></i> </Link> -->
            </template>
        </SectionTitle>
        <hr>
        <div class="row">
            <div class="col-12 col-md-8">
                <div class="fs-5 mb-1">
                    Institucion: <strong>{{ props.institution.name }}</strong>
                </div>
                <div class="fs-5 mb-1">
                    Estado del incidente: <small class="text-secondary"><em>{{ incident.status.description }} <i :class="incident.status.icon"></i></em></small>
                </div>        
                <div class="card" v-if="postsRelacionados.length">
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="far fa-lightbulb fs-4"></i><br>
                            La siguiente es una lista de posts sugeridos que se encuentran en un radio aceptable de distancia de su post original.
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <p class="lead">
                            Nuevo estado
                        </p>
                        <div class="mb-1">
                            <label>Estado</label>
                            <select class="form-control" v-model="nuevoEstado">
                                <option v-for="estado in estados" :key="estado.id" :value="estado.id">{{ estado.description }}</option>
                            </select>
                        </div>
                        <div class="mb-1">
                            <input class="form-control" placeholder="Comentario" v-model="descripcionNuevoEstado">
                        </div>
                        <button class="btn btn-outline-success float-end" @click="guardarNuevoEstado">  Guardar</button>
                    </div>
                </div>
                <!-- {{incident}} -->
                <div class="card mb-1" v-for="estado in incident.history?.slice().reverse()"  :style="{ borderLeft: '3px solid ' + estado.status.color }">
                    <div class="card-body">
                        <div class="fs-6 mb-1">
                            <strong>{{ estado.status.description }}</strong> <small>{{ estado.diff }}</small>
                        </div>
                        <div class="mb-2">
                            {{ estado.description }}
                        </div>
                        <div class="mb-1 float-end text-muted">
                            <em>-{{estado.user.name}}</em>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        
    </AppLayout>
    
</template>

<style>
.card-img-top {
    height: 200px;
    object-fit: cover;
}
</style>