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
        title: `¿Agregar el post al incidente?`,
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

const guardarNuevoEstadoOK = async (code) => {
    const res = await IncidentService.changeStatus(`/institution/${props.institution.id}/incident/${incident.value.id}/status`, 
    {
        'status_id': nuevoEstado.value,
        'description' : descripcionNuevoEstado.value
    });
    if(code == 'canceled'){
        location.href = '/institution/' + props.institution.id + '/edit';
    }
    incident.value.history = res.data.history;
    nuevoEstado.value = null;
    descripcionNuevoEstado.value = null;
}
const guardarNuevoEstado = async (event) => {
    const estadoSeleccionado = props.estados.find(estado => estado.id === nuevoEstado.value);
    let code = null;
      if (estadoSeleccionado) {
        code = estadoSeleccionado.code;
        if (code === 'canceled') {
          Swal.fire({
            title: '¿Cancelar Incidente?',
            text: 'Una vez cancelado, no se podrá revertir e incluso se eliminarán los posts relacionados.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, cancelar',
            cancelButtonText: 'No'
          }).then((result) => {
            if (result.isConfirmed) {
                guardarNuevoEstadoOK(code);
            }
          });
        }else{
            guardarNuevoEstadoOK(code);
        }
    }    

}

const mostrar = ref(false);

const toggleMostrar = () => {
    mostrar.value = !mostrar.value;
}

const eliminarPost = async (post) => {
    Swal.fire({
        title: `¿Eliminar el post del incidente?`,
        showCancelButton: true,
        confirmButtonText: "Remover",
        cancelButtonText: "Cancelar",
        allowOutsideClick: () => !Swal.isLoading()
    }).then(async (result) => {
        if (result.isConfirmed) {
            let res = await IncidentService.removePost(`/institution/${props.institution.id}/incident/${incident.value.id}/post/${post.id}/remove`);
            incident.value = res.data.incident;
            postsRelacionados.value = res.data.postsRelacionados;
        }
    });
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
                    <i class="fas fa-university"></i> <strong>{{ props.institution.name }}</strong>
                </div>
                <div class="fs-5 mb-2">
                    <small  :style="{ color:  incident.history?.slice().reverse()[0].status.color }"><em> {{ incident.history?.slice().reverse()[0].status.description }} <i :class="incident.history?.slice().reverse()[0].status.icon"></i></em></small>
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
                                    <div class="card-header text-muted"><small># {{post.subcategory.name}}</small></div>
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
                                        <div class="mb-1 d-grid gap-2" >
                                            <button class="btn btn-success" @click="agregarPostsAIncidente(post)">Agregar post al incidente</button>
                                        </div>
                                    </div>
                                    <div class="card-footer" v-if="post.location_long">
                                        <i class="fas fa-map-marker-alt"></i> {{post.location_long}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div>
                    <div>
                        <h5>Posts del incidente</h5>
                    </div>
                    <div class="card-body">              
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3" v-for="post in incident.posts">
                                <div class="card g-0 mb-3 h-100">
                                    <div class="card-header text-muted"><small><b># {{post.subcategory.name}}</b> - Creado el {{ new Date(post.created_at).toLocaleDateString() }}</small></div>
                                    <MapaPuntosSugeridosIncident class="card-img-top" :lat="post.lat" :lng="post.lng"
                                    :puntos="[{'lng' : post.lng, 'lat' : post.lat}, {'lng' : incident.posts[0].lng, 'lat' : incident.posts[0].lat}]" />
                                    <div class="card-body">
                                        <h3>{{ incident.title }}</h3>
                                        <div v-for="img in post.images" class="float-center mb-3">
                                            <img :src="img.file" class="img-thumbnail" alt="Captura">
                                        </div>
                                        <!-- <div class="row">
                                            <div class="col-12 col-md-6" v-for="img in post.images">
                                            </div>
                                        </div> -->
                                        <div  v-if="incident.postOriginal.id == post.id">                                        
                                            <div class="col-12">
                                                <div class="alert alert-info">
                                                    <div class="alert-body">
                                                        <i class="fas fa-info-circle"></i> Este es el post original y no se puede eliminar
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-else class="d-grid gap-2">
                                            <button class="btn btn-danger" @click="eliminarPost(post)"><i class="fas fa-trash-alt"></i> Eliminar post del incidente</button>
                                            <!-- <div class="m-3">
                                            </div> -->
                                        </div>
                                    </div>
                                    <div class="card-footer" v-if="post.location_long">
                                        <i class="fas fa-map-marker-alt"></i> {{post.location_long}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>                
                <!-- posts vigentes -->              
            </div>
            <div class="col-12 col-md-4">                
                <div class="mb-1">
  <label><b>Nuevo Estado</b></label>                    
  <select class="form-control" v-model="nuevoEstado" >
    <option v-for="estado in estados" :key="estado.id" :value="estado.id">
      {{ estado.description }}
    </option>
  </select>
</div>
                <div class="mb-3">
                    <input class="form-control" placeholder="Comentario" v-model="descripcionNuevoEstado">
                </div>
                <div class="mb-3">
                    <span class="float-end">
                        <button class="btn btn-outline-success" @click="guardarNuevoEstado"> Guardar </button>
                    </span>
                </div>
                <div class="clearfix"></div>
                <hr>
                <h5><i class="fas fa-history"></i> <b>Historial</b></h5>
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
