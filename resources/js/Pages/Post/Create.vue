<script setup>
import { computed, onMounted, ref } from 'vue'
import { useForm } from '@inertiajs/vue3';
import MapaLatLng from '@/Components/MapaLatLng.vue';

const WIDTH = 1280;
const HEIGHT = 720;
const apiKey = import.meta.env.VITE_MAPBOX_TOKEN;
const props = defineProps({
    categorias:{
        type: Array,
    },
    
});

const reposicionarTemporal = () => {
    if(navigator.geolocation){
        navigator.geolocation.getCurrentPosition((position) => {
            form.latitud = position.coords.latitude;
            form.longitud = position.coords.longitude;
            geocoding();
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

const cantidadSubcategorias = computed(() => {
    return props.categorias.reduce((acc, category) => acc + category.subcategories.length, 0);
});

// Defino mis variables para usarlas en el componente
const form = useForm({
    contenido: null,
    latitud: 0,
    longitud: 0,
    image: null,
    subcategory_id:null,
    fullAddress : null,
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
        reader.onload = (event) => {
            let img_url = event.target.result; //base64
            let image = document.createElement('img');
            image.src = img_url;
            image.onload = (e) => {
                let canvas = document.createElement('canvas');
                let ctx = canvas.getContext('2d');
                let width = e.target.width;
                let height = e.target.height;
                let ratio;
                
                if (width > height) {
                    ratio = WIDTH / width;
                    canvas.width = WIDTH;
                    canvas.height = height * ratio;
                } else {
                    ratio = HEIGHT / height;
                    canvas.width = width * ratio;
                    canvas.height = HEIGHT;
                }
                
                ctx.drawImage(image, 0, 0, canvas.width, canvas.height);
                
                let newImgUrl = ctx.canvas.toDataURL('image/jpeg', 0.8);
                form.image = urlToFile(newImgUrl);
            }
        }
    }
};
const searchingGeocode = ref(false);
const geocoding = () =>{
    setTimeout(() => {
        searchingGeocode.value = true;
        axios.get('/api/geocoding', {
            params: {
                lng: form.longitud,
                lat: form.latitud
            }
        })
        .then((response) => {
            searchingGeocode.value = false;
            form.fullAddress = response.data.features[0]?.properties.full_address;
            // console.log();
        })
        .catch((error) => {
            searchingGeocode.value = false;
            console.log(error);
        });
    }, 150);
}

const urlToFile = (url) => {
    let arr = url.split(',');
    let mime = arr[0].match(/:(.*?);/)[1];
    let data = arr[1];
    let dataStr = atob(data);
    let n = dataStr.length;
    let dataArr = new Uint8Array(n);
    while (n--) {
        dataArr[n] = dataStr.charCodeAt(n);
    }
    let file = new File([dataArr], 'image.jpg', { type: mime });
    return file;
}

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
    
    form.post(route('posts.store'));
    activeCategory.value = null;
    imageSrc.value = null;
    form.image = null;
    form.
    fullAddress = null;
    form.latitud = 0;
    form.longitud = 0;
    form.subcategory_id = null;
    document.getElementById('image').value = '';    
}

function toggleCategory(id){
    reposicionarTemporal();
    activeCategory.value = activeCategory.value === id ? null : id;
}

</script>

<template>
    <!-- Crear un Post -->
    
    <div id="c"></div>
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
                    <p>Seleccione la categoria y subcategoria del posteo <i class="fas fa-carret-down"></i> </p>
                    <!-- <p for="formFile" class="form-label mr-4">Seleccione la categoria y subcategoria del posteo</p> -->
                    <div v-for="category in categorias" :key="category.id" class="col-12 col-md-4 mb-3">
                        <!-- Boton para elegir las categorias -->
                        <button type="button" class="btn shadow border-2 rounded-4 w-100" :class="{
                                    'bg-success' : category.id === activeCategory,
                                    // 'bg-secondary' : subcategory.id != form.subcategory_id
                                }" @click="toggleCategory(category.id)">
                            <!-- Category icon and name -->
                            <span v-if="!activeCategory" v-html="category.icon" class="fs-3"></span>
                            <p class="fs-4">{{ category.name }}</p>
                        </button>
                        
                        <!-- Subcategorías -->
                        <div v-if="activeCategory === category.id " class="mt-1">
                            <!-- Mensaje si no hay subcategorías -->
                            <div v-if="category.subcategories.length === 0" class="alert alert-warning">
                                <div class="alert-body">
                                    No tiene subcategorías
                                </div>
                            </div>
                            <ul class="list-group" v-else>
                                <li v-for="subcategory in category.subcategories" :key="subcategory.id"
                                class="list-group-item rounded-4"
                                :class="{
                                    'bg-success' : subcategory.id == form.subcategory_id,
                                    // 'bg-secondary' : subcategory.id != form.subcategory_id
                                }"
                                role="button"
                                @click="form.subcategory_id = subcategory.id"
                                >
                                <span v-html="subcategory.icon"></span> 
                                {{ subcategory.name }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <input type="file" class="form-control border-2 p-2 rounded-5 mb-1" id="image"
            name="image" capture="environment" accept="image/png, image/jpeg"
            @change="onFileChange">
            <div class="mb-3">
                <MapaLatLng :lat="form.latitud" :lng="form.longitud" @update:lat="(lat) => {form.latitud = lat; geocoding()}" @update:lng="(lng) => {form.longitud = lng}" > </MapaLatLng>
            </div>
            <div v-if="searchingGeocode" class="d-flex mb-3 ml-2">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
            <input type="text" class="form-control border-2 p-2 rounded-5 mb-4" id="contenido" readonly v-show="form.fullAddress" v-model="form.fullAddress" required>
            
            <!-- Boton para postear -->
            <button class="btn btn-primary rounded-4" :disabled="(form.subcategory_id == null && form.image == null) || form.processing">
                {{ form.processing ? 'Guardando...' : 'Guardar' }}
            </button>
        </form>
    </div>
</div>
</template>
