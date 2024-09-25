<script setup>
import { computed, ref } from 'vue'
import { useForm } from '@inertiajs/vue3';
import Mapa from '@/Pages/Mapa/Index.vue';

const props = defineProps({
    categorias:{
        type: Array,
    },
    
});

const cantidadSubcategorias = computed(() => {
    return props.categorias.reduce((acc, category) => acc + category.subcategories.length, 0);
});

// Defino mis variables para usarlas en el componente
const form = useForm({
    contenido: null,
    latitud: null,
    longitud: null,
    image: null,
    subcategory_id:null,
});


const imageSrc = ref(null);
const activeCategory = ref(null);

const onFileChange = (event) => {
    const selectedFile = event.target.files[0];
    if (selectedFile) {
        const reader = new FileReader();
        reader.onload = (e) => {
            imageSrc.value = e.target.result;
        };
        reader.readAsDataURL(selectedFile);
        form.image = selectedFile;
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
            form.post(route('posts.store'));
            activeCategory.value = null;
            imageSrc.value = null;
            form.image = null;
            form.subcategory_id = null;
            document.getElementById('image').value = '';
        }, (error) => {
            let mensaje = '';
            switch (error.code) {
                case error.PERMISSION_DENIED:
                mensaje = "Esta aplicación no tiene permisos de geolocalización";
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
    activeCategory.value = activeCategory.value === id ? null : id;
}

</script>

<template>
    <!-- Crear un Post -->
    <div class=" card w-100">
        <div class="card-body">
            <div v-if="categorias.length === 0 || cantidadSubcategorias === 0">
                <div class="alert alert-danger">
                    <div class="alert-body">
                        <i class="fas fa-exclamation-triangle"></i> No se puede crear un reporte porque no hay categorías, o las categorías no tienen subcategorías.
                    </div>
                </div>
            </div>
            <form @submit.prevent="funcionDeSubmit" ref="formCreatePost" v-else>
                <div class="row">
                    <div class="fs-4">Nuevo reporte</div>
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
            <button class="btn btn-primary rounded-4" :disabled="(form.subcategory_id == null && form.image == null) || form.processing">
                {{ form.processing ? 'Guardando...' : 'Guardar' }}
            </button>
        </form>
        <div class="mt-4">

            <Mapa></Mapa>
        </div>
    </div>
</div>
</template>
