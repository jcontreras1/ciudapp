<script setup>

import { defineProps, ref, onMounted, computed } from 'vue';
import MapaCalor from '../Mapa/MapaCalor.vue';
import ReportService from '@/Services/ReportService';
import Checkbox from '@/Components/Checkbox.vue';
import AppLayoutFluid from '@/Layouts/AppLayoutFluid.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import SectionTitle from '@/Components/SectionTitle.vue';
import IncidentService from '@/Services/IncidentService';
import MapaPuntosSugeridosIncident from '@/Components/MapaPuntosSugeridosIncident.vue';


const props = defineProps({
    myIncident: {
        type: Object,
        required: true
    },
    institution: {
        type: Object,
        required: true
    },
    myPostsRelacionados : {
        type: Object,
        required: true
    },
    estados : {
        type: Object,
        required: true
    }
});

const incident = ref(props.myIncident.data);
const postsRelacionados = ref(props.myPostsRelacionados);
const nuevoEstado = ref(null)
const descripcionNuevoEstado = ref(null)

const agregarPostsAIncidente = (post) => {
    Swal.fire({
        title: `¿Agregar el post #${post.id} al incidente?`,
        showCancelButton: true,
        confirmButtonText: "Agregar",
        cancelButtonText: "Cancelar",
        allowOutsideClick: () => !Swal.isLoading()
    }).then(async (result) => {
        if (result.isConfirmed) {
            let res = await IncidentService.addPosts(`/institution/${props.institution.id}/incident/${incident.value.id}/addPosts`,
                {   'posts' : [post.id] }
            );
            incident.value = res.data.incident;
            postsRelacionados.value = res.data.postsRelacionados;
        }
    });
    
}
const guardarNuevoEstado = async (event) => {
    const res = await IncidentService.changeStatus(`/institution/${props.institution.id}/incident/${incident.value.id}/status`, 
    {
        'status_id': nuevoEstado.value,
        'description' : descripcionNuevoEstado.value
    });
    incident.value.history = res.data.history;
    nuevoEstado.value = null;
    descripcionNuevoEstado.value = null;
}

const mostrar = ref(false);

const toggleMostrar = () => {
    mostrar.value = !mostrar.value;
}

</script>

<template>    
    <AppLayoutFluid>
        
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
                    Estado del incidente: <small class="text-secondary"><em>{{ incident.history?.slice().reverse()[0].status.description }} <i :class="incident.history?.slice().reverse()[0].status.icon"></i></em></small>
                </div>        
                
                <!-- Card sugerencia -->
                <div class="card mb-3" v-if="postsRelacionados?.length">
                    <div class="card-header" @click="toggleMostrar" role="button">
                        Posts relacionados
                        <span class="float-right">
                            <i class="fas fa-angle-up" role="button" v-if="mostrar"></i>
                            <i class="fas fa-angle-down" role="button" v-else="!mostrar"></i>
                        </span>
                    </div>
                    <div class="card-body" v-show="mostrar">
                        <div class="alert alert-info">
                            <i class="far fa-lightbulb fs-4"></i><br>
                            La siguiente es una lista de posts sugeridos que se encuentran en un radio aceptable de distancia de su post original.
                        </div>
                        
                        <!-- posts sugeridos -->
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3" v-for="post in postsRelacionados">
                                <div class="card g-0 mb-3 h-100">
                                    <div class="card-header text-muted"><small>#{{post.id}}</small></div>
                                    <MapaPuntosSugeridosIncident class="card-img-top" :lat="post.lat" :lng="post.lng"
                                    :puntos="[{'lng' : post.lng, 'lat' : post.lat}, {'lng' : incident.posts[0].lng, 'lat' : incident.posts[0].lat}]" />
                                    <div class="card-body">
                                        <h3>{{ incident.title }}</h3>
                                        <div class="row">
                                            <div class="col-12 col-md-6" v-for="img in post.images">
                                                <img :src="img.file" class="img-thumbnail" alt="Captura">
                                            </div>
                                        </div>
                                        <hr>
                                        <i class="fas fa-arrows-alt-h"></i> Distancia del post original: <strong>{{ Math.round(post.distancia) }}  metros</strong>
                                        <div v-if="!post.enRegion" class="mb-2">
                                            <i class="fas fa-times text-danger" ></i> No está en ninguna región de la institución, pero podría ser tratado como si lo estuviera
                                        </div>
                                        <div v-else class="mb-2">
                                            <i class="fas fa-check text-success"></i> Está en alguna de las regiones de la institución
                                        </div>
                                        <div class="mb-1">
                                            <button class="btn btn-success" @click="agregarPostsAIncidente(post)">Agregar post al incidente</button>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        {{post.location_long}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        Posts del incidente
                    </div>
                    <div class="card-body">
                        
         

                                                <div class="row">
                            <div class="col-12 col-md-6 mb-3" v-for="post in incident.posts">
                                <div class="card g-0 mb-3 h-100">
                                    <div class="card-header text-muted"><small>#{{post.id}}</small></div>
                                    <MapaPuntosSugeridosIncident class="card-img-top" :lat="post.lat" :lng="post.lng"
                                    :puntos="[{'lng' : post.lng, 'lat' : post.lat}, {'lng' : incident.posts[0].lng, 'lat' : incident.posts[0].lat}]" />
                                    <div class="card-body">
                                        <h3>{{ incident.title }}</h3>
                                        <div class="row">
                                            <div class="col-12 col-md-6" v-for="img in post.images">
                                                <img :src="img.file" class="img-thumbnail" alt="Captura">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer" v-if="post.location_long">
                                        {{post.location_long}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- posts vigentes -->
                
                
                
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
                                <option v-for="estado in estados" :key="estado.id" :value="estado.id"><i :class="estado.icon"></i> {{ estado.description }}</option>
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
        
    </AppLayoutFluid>
    
</template>

<style>
.img-thumbnail{
    height: 150px;
    object-fit: cover;
}
</style>
