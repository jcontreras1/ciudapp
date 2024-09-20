<script setup>
import { reactive, ref } from 'vue'
import { router } from '@inertiajs/vue3';

defineProps({
    categorias:{
        type: Array,
    },
    
});

// Defino mis variables para usarlas en el componente
const form = reactive({
    contenido: null,
    latitud: null,
    longitud: null,
    image: null,
    subcategory_id:null,
});

let imageSrc = ref(null);
let file = ref(null);
let activeCategory = ref(null);

const onFileChange = (event) => {
    const selectedFile = event.target.files[0];
    if (selectedFile) {
        const reader = new FileReader();
        reader.onload = (e) => {
            imageSrc.value = e.target.result;
        };
        reader.readAsDataURL(selectedFile);
        file.value = selectedFile;
        form.image = file.value;
    }
};

// Metodo para enviar el formulario
function funcionDeSubmit() {
    if(form.subcategory_id === null){
        Swal.fire({
            'icon' : 'error',
            'title' : 'Debe seleccionar una categoría y una subcategoría',
            'toast': true,
            'position': 'top-end',
            'timer': 2500,
            'timerProgressBar' : true,
            'showConfirmButton' : false
        });
        return;
    }

    if(form.image === null){
        Swal.fire({
            'icon' : 'error',
            'title' : 'Debe seleccionar una imagen',
            'toast': true,
            'position': 'top-end',
            'timer': 2500,
            'timerProgressBar' : true,
            'showConfirmButton' : false
        });
        return;
    }
    if(navigator.geolocation){
        navigator.geolocation.getCurrentPosition((position) => {
            form.latitud = position.coords.latitude;
            form.longitud = position.coords.longitude;
            router.post('/posts', form);
        }, (error) => {
            let mensaje = '';
            switch (error.code) {
                case error.PERMISSION_DENIED:
                mensaje = "No se otorgó permiso para obtener la ubicación";
                break;
                case error.POSITION_UNAVAILABLE:
                mensaje = "Posición no disponible";
                break;
                case error.TIMEOUT:
                mensaje = "Tiempo de espera agotado";
                break;
                default:
                mensaje = "Error desconocido";
                break;
            }
            Swal.fire('Error', mensaje, 'error');
        },{
            enableHighAccuracy: true,
            timeout: 5000,
            maximumAge: 0
        });
    } else {
        Swal.fire('No se pudo obtener la ubicación. ', 'error');
    }
}

function toggleCategory(id){
    this.activeCategory = this.activeCategory === id ? null : id;
}

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}

</script>

<template>
    <!-- Crear un Post -->
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
                                <button type="button" class="btn shadow border-2 rounded-4 w-100" @click="toggleCategory(category.id)">
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
                <button class="btn btn-primary rounded-4" :disabled="form.subcategory_id == null && form.image == null">Postear</button>
            </form>
        </div>
    </div>
</template>
