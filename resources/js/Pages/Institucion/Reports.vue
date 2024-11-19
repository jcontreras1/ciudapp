<script setup>

import { defineProps, ref, onMounted, computed } from 'vue';
import MapaCalor from '../Mapa/MapaCalor.vue';
import ReportService from '@/Services/ReportService';
import Checkbox from '@/Components/Checkbox.vue';
import PostService from '@/Services/PostService';

const props = defineProps({
    institution: {
        type: Object,
        required: true
    },
    redibujar : {
        type : Number,
        required : false
    }
});

const reporteSeleccionado = ref();

//Para agregarle independencia al módulo va y busca las subcategorías por sí solo, de modo que solo necesita la institución
const allSubcategories = ref();
const mostrarIncidentes = ref(true);
const reportes = ref([]);
const fechaDesde = ref();
const fechaHasta = ref();

const toggleWithIncidents = () => {
    mostrarIncidentes.value = !mostrarIncidentes.value;
    getReports();
}

const options = ref({
    regiones : [],
    subcategories : [],
    withIncidents : mostrarIncidentes,
    from : null,
    to: null,
});

const toggleCat = (id) => {
    if (options.value.subcategories.includes(id)) {
        options.value.subcategories = options.value.subcategories.filter(subcat => subcat !== id);
    } else {
        options.value.subcategories.push(id);
    }
    getReports();
    
}

const validarFechas = () => {
    
    options.value.from = fechaDesde.value ?? null;
    options.value.to = fechaHasta.value ?? null;
    getReports();
}

const getReports = async () => {
    const response = await ReportService.getReport(`/institution/${props.institution.id}/reports`, { params :  options.value});
    reportes.value = response.data;
}

const getSubcategories = async () => {
    const response = await ReportService.getSubCategory(`/institution/${props.institution.id}/subcategories`);
    allSubcategories.value = response.data;
}

//crear incidente a partir de un post
const createIncident = (postId) => {
    Swal.fire({
        title: "Descripción del incidente",
        input: "text",
        inputAttributes: {
            autocapitalize: "off"
        },
        showCancelButton: true,
        confirmButtonText: "Crear Incidente",
        cancelButtonText: "Cancelar",
        allowOutsideClick: () => !Swal.isLoading()
    }).then(async (result) => {
        if (result.isConfirmed) {
            const response = await PostService.createIncidentFromPost(`/institution/${props.institution.id}/post/${postId}/makeIncident`, {'description' : result.value});
            const res = response.data;
            location.href = `/institution/${props.institution.id}/incidents/${res.id}`;
        }
    });
}

// Escuchar nuevos posts creados y almacenarlos temporalmente
Echo.channel('post').listen('.created', (response) => {
    setTimeout(getReports, "600");    
});

Echo.channel('post').listen('.updated', (response) => {
    setTimeout(getReports, "600");    
});

Echo.channel('post').listen('.deleted', (response) => {
    let id = response.post;
    if(reportes.value.reportes.filter(report => report.post.id === id).length > 0){
        setTimeout(getReports, "600");
    }
});

onMounted(() => {
    getSubcategories();
    getReports();
});



</script>

<template>    
    <div class="row mb-4">        
        <div class="col-12 col-md-9">
            <MapaCalor :redibujar="redibujar" :reportes="reportes.reportes" :regiones="reportes.regiones" :reporteSeleccionado="reporteSeleccionado" />   
            
        </div>
        <div class="col-12 col-md-3 ">            
            <div class="col-12 mb-3 ">
                <div class="card">
                    <div class="card-body ">
                        <Checkbox 
                        v-for="subcategory in allSubcategories" 
                        :key="subcategory.id" 
                        class="fs-5 mb-2" 
                        :name="subcategory.name"
                        :checked="options.subcategories.includes(subcategory.id)"
                        @click="toggleCat(subcategory.id)"
                        />
                    </div>
                </div>
            </div>
            
            <!-- <div class="row" v-if="reportes.reportes?.length"> -->
                <div class="row">
                    <div class="col-12">				
                        <div class="form-floating mb-3">
                            <input type="date" class="form-control" id="floatingInput" v-model="fechaDesde" placeholder="Fecha desde">
                            <label for="floatingInput">Fecha desde</label>					
                        </div>
                    </div>
                    <div class="col-12">				
                        <div class="form-floating mb-2">
                            <input type="date" class="form-control" v-model="fechaHasta" id="floatingInput" placeholder="Fecha hasta">
                            <label for="floatingInput">Fecha hasta</label>					
                        </div>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary" @click="validarFechas">Filtrar <i class="fas fa-filter"></i></button>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <label>Seleccionar todos los reportes o sólo los que no tengan incidentes</label>
                        <Checkbox  
                        class="fs-5 mb-2" 
                        :name="'Todos los reportes'"
                        :checked="true"
                        @click="toggleWithIncidents"
                        />
                    </div>                
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-3 mb-2" v-for="reporte in reportes.reportes?.sort((a,b) => a.post.subcategory.name.localeCompare(b.post.subcategory?.name))" :key="reporte.id">
                <div class="card h-100" role="button">
                    <img :src="reporte.post.images.map(report => report.file)" class="card-img-top" alt="" >
                    <div class="card-body">
                        <div class="card-text mb-1">
                            <h4 @click="reporteSeleccionado = reporte"><u>{{ reporte.post.subcategory.name }}</u></h4><br>                       
                            <i class="fas fa-calendar-alt"></i> {{ new Date(reporte.post.created_at).toLocaleDateString() }}<br>
                            <i class="fas fa-map-marker-alt" v-if="reporte.post.location_long"></i> {{reporte.post.location_long }}
                        </div>
                        <div v-if="!reporte.post.incident_id">
                            <button class="btn btn-xs btn-success" @click="createIncident(reporte.post.id)">Crear incidente</button>
                        </div>
                        <div v-else>
                            <a :href="route('incidents.show', {'incident' : reporte.post.incident_id, 'institution' : props.institution.id})" class="btn btn-primary">Ver incidente</a>
                        </div>
                    </div>
                </div>            
            </div>		
        </div> 
        
    </template>
    
    <style>
    .card-img-top {
        height: 200px;
        object-fit: cover;
    }
</style>