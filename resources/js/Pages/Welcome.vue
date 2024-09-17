<script setup>
import { reactive, ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SinglePost from '@/Pages/Post/SinglePost.vue';
import PostShow from '@/Pages/Post/Show.vue';

const form = reactive({
    contenido: null,
    latitud: null,
    longitud: null,
    image: null,
    subcategory_id:null,
})

// Defino mis variables para usarlas en el componente
let imageSrc = ref(null);
let file = ref(null);
let activeCategory = ref(null); // Track the currently active category

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
                form.image = file.value;
                form.latitud = position.coords.latitude;
                form.longitud = position.coords.longitude;
                router.post('/posts', form);
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
    categorias:{
        type: Array,
        // required: true,
    },

});

function toggleCategory(id){
    this.activeCategory = this.activeCategory === id ? null : id;
}

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
                <div class=" card w-100">
                    <div class="card-body">
                        <!-- <textarqea class="form-control border-2 p-2 rounded-5" id="contenido" name="contenido" rows="3" placeholder="¿Qué está pasando?"></textarea> -->
                            <form @submit.prevent="funcionDeSubmit">
                                <div class="row">
                                    <p>Seleccione la categoria y subcategoria del posteo</p>
                                    <!-- <p for="formFile" class="form-label mr-4">Seleccione la categoria y subcategoria del posteo</p> -->
                                    <div v-for="category in categorias" :key="category.id" class="col-12 col-md-4 mb-3">
                                        <div class="mb-2">
                                            <!-- Boton para elegir las categorias -->
                                            <button class="btn shadow border-2 rounded-1 w-100" @click="toggleCategory(category.id)">
                                                <!-- Category icon and name -->
                                                <span v-html="category.icon" class="fs-1"></span>
                                                <p class="fs-3">{{ category.name }}</p>
                                            </button>

                                            <!-- Subcategorías -->
                                            <div v-if="activeCategory === category.id " class="mt-2">
                                                <!-- Mensaje si no hay subcategorías -->
                                                <div v-if="category.subcategories.length === 0" class="alert alert-warning">
                                                    <div class="alert-body">
                                                        No tiene subcategorías
                                                    </div>
                                                </div>
                                                <ul class="list-group" v-else>
                                                    <li v-for="subcategory in category.subcategories" :key="subcategory.id"
                                                    class="list-group-item"
                                                    :class="{
                                                        'bg-success' : subcategory.id == form.subcategory_id,
                                                        // 'bg-secondary' : subcategory.id != form.subcategory_id
                                                    }"
                                                    role="button"
                                                    @click="form.subcategory_id = subcategory.id">
                                                    <span  v-html="subcategory.icon"></span> {{ subcategory.name }}
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <input type="file" class="form-control border-2 p-2 rounded-5 mb-4" id="image"
                            name="image" capture="environment" accept="image/png, image/jpeg"
                            @change="onFileChange">

                            <!-- Boton para postear -->
                            <button class="btn btn-primary rounded-4">Postear</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mostrar número de posts -->
        <div class="mt-3">
            <a href="#">Mostrar 105 posts</a>
        </div>
    <!-- </div> -->

    <hr>
    <PostShow :posts="posts"></PostShow>

</AppLayout>
</template>
