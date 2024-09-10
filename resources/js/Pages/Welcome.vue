<script setup>
import { reactive, ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
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
    
    
    <AppLayout>
        <Head title="Inicio" />

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
    
    
</AppLayout>
</template>
