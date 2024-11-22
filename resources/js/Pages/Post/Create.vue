<script setup>
import { computed, onMounted, ref } from 'vue'
import { useForm } from '@inertiajs/vue3';
import MapaLatLng from '@/Components/MapaLatLng.vue';

const WIDTH = 1280;
const HEIGHT = 720;
const fileName = ref('');  // Variable para almacenar el nombre del archivo
const apiKey = import.meta.env.VITE_MAPBOX_TOKEN;
const sending = ref(false)
const props = defineProps({
    categorias:{
        type: Array,
    },
    
});

const emit = defineEmits(['newPost']);


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
        fileName.value = selectedFile.name; // Actualiza el nombre del archivo
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
        })
        .catch((error) => {
            searchingGeocode.value = false;
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

const funcionDeSubmit = () => {
    if (form.subcategory_id === null) {
        Swal.fire({
            icon: 'error',
            title: 'Debe seleccionar una categoría y una subcategoría',
            toast: true,
            position: 'top-end',
            timer: 2500,
            timerProgressBar: true,
            showConfirmButton: false
        });
        return;
    }
    
    if (form.image === null) {
        Swal.fire({
            icon: 'error',
            title: 'Debe seleccionar una imagen',
            toast: true,
            position: 'top-end',
            timer: 2500,
            timerProgressBar: true,
            showConfirmButton: false
        });
        return;
    }
    sending.value = true;
    // Crear el post usando Axios
    axios.post('/api/post', form,  { headers: {
        'accept': 'application/json',
        'Content-Type': `multipart/form-data;`,
    }})
    .then((response) => {
        sending.value = false;
        // Emitir el nuevo post creado
        emit('newPost', response.data);
        activeCategory.value = null;
        imageSrc.value = null;
        form.image = null;
        form.
        fullAddress = null;
        form.latitud = 0;
        form.longitud = 0;
        form.subcategory_id = null;
        document.getElementById('image').value = ''; 
    })
    .catch(error => {
        sending.value = false;
        Swal.fire({
            icon: 'error',
            title: 'Error al crear el post',
            text: error.response?.data.message || 'Ocurrió un error inesperado.',
        });
    });
};

function toggleCategory(id){
    reposicionarTemporal();
    activeCategory.value = activeCategory.value === id ? null : id;
}

</script>
<template>
    <!-- Crear un Post -->    
    <div id="c"></div>
    <div class="card w-100">
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

                    <!-- Categorías -->
                    <div v-for="category in categorias" :key="category.id" class="col-12 col-md-4 mb-1">
                        <button type="button" class="btn shadow pb-0 rounded-4 w-100" :class="{
                            'bg-success' : category.id === activeCategory,
                        }" @click="toggleCategory(category.id)">
                            <span v-if="!activeCategory" v-html="category.icon" class="fs-3"></span>
                            <p class="fs-4">{{ category.name }}</p>
                        </button>
                    </div>

                    <!-- Subcategorías (se muestra fuera del bloque de categoría activa) -->
                    <div v-if="activeCategory !== null" class="mt-3">
                        <div v-for="category in categorias" :key="category.id" v-show="category.id === activeCategory">
                            <div v-if="category.subcategories.length === 0" class="alert alert-warning">
                                No tiene subcategorías
                            </div>
                            <div v-else>
                                <button type="button" v-for="subcategory in category.subcategories" :key="subcategory.id"
                                class="btn rounded-4 mr-2 mb-2"
                                :class="{
                                    'btn-success' : subcategory.id == form.subcategory_id,
                                    'btn-outline-success' : subcategory.id != form.subcategory_id
                                }"
                                @click="form.subcategory_id = subcategory.id">
                                    <span v-html="subcategory.icon"></span> 
                                    {{ subcategory.name }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Inputs adicionales como la imagen y el mapa -->
                <!-- <input type="file" class="form-control border-2 p-2 rounded-5 mb-1" id="image"
                name="image" capture="environment" accept="image/png, image/jpeg"
                @change="onFileChange"> -->

                     <div class="file-input-container">
        <label for="file-upload" class="file-upload-label">
            <i class="fas fa-camera"></i> <!-- Icono de cámara de Font Awesome -->
            <div class="file-upload-text">Imagen</div>
        </label>
        <input type="file" id="file-upload" class="file-upload-input" @change="onFileChange" accept="image/*">
        <!-- Mostrar el nombre del archivo seleccionado -->
        <div v-if="fileName" class="file-name ml-1 d-none d-md-block">{{ fileName }}</div>
    </div>
                <div class="mb-1">
                    <MapaLatLng :lat="form.latitud" :lng="form.longitud" @update:lat="(lat) => {form.latitud = lat; geocoding()}" @update:lng="(lng) => {form.longitud = lng}" > </MapaLatLng>
                </div>
                
                <div v-if="searchingGeocode" class="d-flex mb-3 ml-2">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
                <div v-else class="mb-3">
                    <span>{{ form.fullAddress }}</span>
                </div>

                <button class="btn btn-primary rounded-4" :disabled="(form.subcategory_id == null && form.image == null) || sending">
                    <span>
                        <i v-if="sending" class="fas fa-circle-notch fa-spin"></i>
                        <span v-else>Guardar</span>
                    </span>
                </button>
            </form>
        </div>
    </div>
</template>

<style>
.file-input-container {
    position: relative;
    display: inline-block;
}

.file-upload-label {
    display: inline-block;
    padding: 20px;
    border: 2px dashed;
    border-radius: 10px;
    background-color: transparent;
    cursor: pointer;
    text-align: center;
    width: 200px;
    font-size: 20px;
    color: #198754;
    transition: all 0.3s ease;
}

/* Modo claro */
.file-upload-label.bg-light {
    background-color: #f8f9fa;
    border-color: #59ff00;
    color: #007bff;
}

.file-upload-label.bg-light i {
    color: #007bff;
}

/* Modo oscuro */
.file-upload-label.bg-dark {
    background-color: #343a40;
    border-color: #007bff;
    color: #fff;
}

.file-upload-label.bg-dark i {
    color: #fff;
}

.file-upload-label:hover {
    background-color: #e9ecef;
}

/* El input real está oculto */
.file-upload-input {
    display: none;
}
</style>

