<script setup>
import { reactive, ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3';

import SinglePost from '@/Pages/Post/SinglePost.vue';

const form = reactive({
    contenido: null,
    latitud: null,
    longitud: null,
    image: null,
})


let imageSrc = ref(null);
let file = ref(null);

const onFileChange = (event) => {
    const selectedFile = event.target.files[0];
    if (selectedFile) {
        const reader = new FileReader();
        reader.onload = (e) => {
            imageSrc.value = e.target.result;
        };
        reader.readAsDataURL(selectedFile);
        file.value = selectedFile;
    }
};

function funcionDeSubmit() {
    let ubicacion = null;
    return new Promise((resolve, reject) => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                console.log(ubicacion = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                });
                form.image = file.value;
                form.latitud = position.coords.latitude;
                form.longitud = position.coords.longitude;
                router.post('/posts', form);

                //document.getElementById('latitud').value = position.coords.latitude;
                // document.getElementById('longitud').value = position.coords.longitude;
                // // document.getElementById('btn_obtener_ubicacion').innerHTML = `Obtener ubicación <i class="bi bi-geo-alt"></i>`;
                // myMap.panTo(new L.LatLng(position.coords.latitude, position.coords.longitude));
                // marker = L.marker([position.coords.latitude, position.coords.longitude]).addTo(myMap);
                // // document.getElementById("myMap").scrollIntoView();
                // resolve(true);
            },
                (error) => {
                    switch (error.code) {
                        case error.PERMISSION_DENIED:
                            this.error = "Permiso denegado";
                            break;
                        case error.POSITION_UNAVAILABLE:
                            this.error = "Posición no disponible";
                            break;
                        case error.TIMEOUT:
                            this.error = "Tiempo de espera agotado";
                            break;
                        default:
                            this.error = "Error desconocido";
                            break;
                    }

        });

        } else {
            x.innerHTML = "no es compatible tu navegador";
            reject('No es compatible');
        }
    });
    // form.comment = null;
}

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    posts:{
        type: Array,
        // required: true,
    },

});
function subirImagen(){
    console.log('Subir imagen');
}

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}
</script>

<template>

    <Head title="Inicio" />

    <div class="bg-gray-50 text-black/50">
        <!-- Barra -->
        <nav class="navbar navbar-expand-lg bg-dark border-bottom border-bottom-dark ticky-top bg-body-tertiary"
            data-bs-theme="dark">

            <div class="container">

                <a class="navbar-brand fw-light" href="/">
                    <span class="fas fa-brain me-1"></span>
                    Ciudapp
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="/login">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/register">Register</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/profile">Profile</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container py-4">

            <div class="row">

                <!-- Lateral izquierda -->
                <div class="col-12 col-md-3">
                    <!-- <div class="card overflow-hidden"> -->
                    <!-- <div class="card-body pt-3"> -->
                    <ul class="nav nav-link-secondary flex-column fw-bold gap-2">
                        <li class="nav-item">
                            <a class="nav-link text-dark d-block text-white" href="#">
                                <i class="fas fa-bars fa-2x mr-2"></i> <span class="h4 d-none d-sm-inline">
                                    Inicio</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-block" href="{{route() }}">
                                <i class="far fa-plus-square fa-2x mr-2"></i><span class="h4 d-none d-sm-inline"> Nuevo
                                    Post</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-block" href="#">
                                <i class="fas fa-map fa-2x mr-2"></i><span class="h4 d-none d-sm-inline"> Mapa</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-block" href="#">
                                <i class="fas fa-compass fa-2x mr-2"></i><span class="h4 d-none d-sm-inline">
                                    Explorar</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-block" href="#">
                                <i class="fas fa-user fa-2x mr-2"></i><span class="h4 d-none d-sm-inline"> Perfil</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-block" href="#">
                                <i class="fas fa-wrench fa-2x mr-2"></i><span class="h4 d-none d-sm-inline">
                                    Configuración</span>
                            </a>
                        </li>
                    </ul>
                    <!-- </div> -->
                    <!-- <div class="card-footer text-center py-2">
                            <a class="btn btn-link btn-sm" href="#"><i class="fas fa-user"></i> Ver Perfil </a>
                        </div> -->
                    <!-- </div> -->
                </div>

                <!-- LATERAL CENTRO -->
                <div class="col-12 col-md-6" style="height: 100vh; overflow-y: auto;">

                    <!-- Barra de navegación -->
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">Para ti</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Reportes</a>
                        </li>
                    </ul>

                    <!-- Cuadro de texto para el post -->
                    <div class="mt-3">

                        <div class="d-flex align-items-start">
                            <!-- Área de texto -->
                            <div class="w-100">
                                <form @submit.prevent="funcionDeSubmit">
                                    <input type="file" class="form-control border-2 p-2 rounded-5 mb-4" id="image"
                                        name="image" capture="environment" accept="image/png, image/jpeg"
                                        @change="onFileChange">
                                    <!-- Íconos debajo del textarea -->
                                    <button class="btn btn-primary rounded-4">Postear</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Mostrar número de posts -->
                    <div class="mt-3">
                        <a href="#">Mostrar 105 posts</a>
                    </div>
                    <!-- </div> -->

                    <hr>

                    <input type="text" class="form-control">
                    <div class="card col-12" v-for="post in posts" :key="post.id">
                        {{ post }}<br>
                        <div v-for="image in post.images">
                            <img :src="image.file" alt="Imagen" class="img-fluid">
                        </div>
                        <div class="col-12 col-md-4 d-none d-md-block mb-1">
                            LATITUD: <p>{{post.lat }}</p>
                        </div>
                        <div class="col-12 col-md-4 d-none d-md-block mb-1">
                            <input type="text" readonly id="longitud" name="longitud"
                                class="form-control form-control-lg">
                        </div>
                        <!-- <img :src="post.image" alt="Imagen" class="img-fluid"> -->
                    </div>





                    <!-- <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Idea created Successfully
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div> -->
                    <!-- <div class=""> -->
                    <!-- Boton para agregar un nuevo post -->
                    <!-- <button class="btn btn-success btn-sm" @click="karen = 'Hola'"> <i class="fas fa-plus"></i>
                        </button> -->


                    <!-- <form @submit.prevent="funcionDeSubmit">
                        <div class="mb-3">
                            <input type="text" class="form-control " placeholder="Nuevo Post" aria-label="Post"
                                name="post">
                        </div>
                        <div class="form-control col-3">
                            <input type="file" class="form-control" id="image" name="image" accept="image/*"
                                @change="onFileChange">
                        </div>

                        <h4> Nuevo post </h4>
                        <div class="row">
                            <div class="mb-3">
                                <textarea class="form-control" v-model="form.comment" rows="3"></textarea>
                            </div>
                            <div>
                                <input type="text" v-model="form.latitud" class="form-control" placeholder="Latitud">
                            </div>
                            <div class="py-2">
                                <button class="btn btn-dark"> Compartir </button>
                            </div>
                        </div>
                    </form> -->
                    <!-- </div> -->

                    <hr>
                    <!-- <SinglePost v-for="post in posts" :key="post.id" :post="post" /> -->
                </div>

                <!-- LATERAL DEREC -->
                <div class="col-12 col-md-3">
                    <h5 class="py-2"><i class="fas fa-search"></i> Buscar</h5>
                    <input placeholder="Buscar" class="form-control rounded-pill w-100" type="text" id="search">
                </div>
            </div>
        </div>
    </div>
</template>
