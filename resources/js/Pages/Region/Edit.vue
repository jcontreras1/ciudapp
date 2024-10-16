<script setup>
import { useForm, Head } from '@inertiajs/vue3';
import { ref, defineProps, onMounted } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import SectionTitle from '@/Components/SectionTitle.vue';
import EditPolygon from '@/Pages/Mapa/EditPolygon.vue';
import axios from 'axios';

const props = defineProps({
    institucion: {
        type: Object,
        required: true
    },
    region: {
        type: Object,
        required: true,
    },
    categories: {
        type: Object,
        required: true,
    },
    notifications: {
        type: Object,
        required: true,
    },
});

const myRegion = ref(props.region);
const myNotifications = ref(props.notifications);

const form = useForm({
    subcategory_ids: [], // Cambiar a un arreglo para permitir múltiples selecciones
});

const setNotify = (id) => {
    axios.post(`/api/regionSubcategory/${id}/userRegionSubcategory`)
    .then(response => {
        myNotifications.value = response.data;
    }).catch(error => {
        console.error(error);
    });
}

const unsetNotify = (id, urc) => {
    axios.delete(`/api/regionSubcategory/${id}/userRegionSubcategory/${urc}`)
    .then(response => {
        myNotifications.value = response.data;
    }).catch(error => {
        console.error(error);
    });
}

const unsetSubcategory = (id) => {
    axios.delete(`/api/region/${myRegion.value.id}/region_subcategory/${id}`)
    .then(response => {
        myRegion.value = response.data;
        form.subcategory_ids = myRegion.value.subcategories.map(subcategory => subcategory.id);
    }).catch(error => {
        console.error(error);
    });
};

const setSubcategory = (id) => {
    axios.post(`/api/region/${myRegion.value.id}/region_subcategory`, {
        subcategory_id: id
    }).then(response => {
        myRegion.value = response.data;
        form.subcategory_ids = myRegion.value.subcategories.map(subcategory => subcategory.id);
    }).catch(error => {
        console.error(error);
    });
};


const setAllSubcategories = () => {
    axios.post(`/api/region/${myRegion.value.id}/region_subcategory/all`).then(response => {
        myRegion.value = response.data;
        form.subcategory_ids = myRegion.value.subcategories.map(subcategory => subcategory.id);
    }).catch(error => {
        console.error(error);
    });
};

const destroyAll = () => {
    axios.delete(`/api/region/${myRegion.value.id}/region_subcategory/allDestroy`).then(response => {
        myRegion.value = response.data;
        form.subcategory_ids = myRegion.value.subcategories.map(subcategory => subcategory.id);
    }).catch(error => {
        console.error(error);
    });
};

const toggleSubcategory = (id) => {
    const index = form.subcategory_ids.indexOf(id);
    if (index > -1) {
        //Acá tengo que encontrar la pivot, basado en el id
        let pivot = myRegion.value.subcategories.find(subcategory => subcategory.id === id).pivot.id;
        unsetSubcategory(pivot)
    } else {
        setSubcategory(id);
    }
};

const fomrulario = useForm({
    'name': myRegion.value.name,
    'institution': props.institucion.id,
    'puntos': [], // Este es el arreglo de todas las coordenadas que hacen el polígono.
});

onMounted(() => {
    form.subcategory_ids = myRegion.value.subcategories.map(subcategory => subcategory.id);
});
</script>

<template>
    <AppLayout>
        <Head title="Editar Región" />
        <SectionTitle>
            <template #title>
                Editar región de la institución <b>{{institucion.name}}</b>
            </template>
        </SectionTitle>
        <hr>
        <form @submit.prevent="fomrulario.put(route('region.update', {
            'region': myRegion.id,
            'institution': props.institucion.id
        }))">
        
        <div class="mb-3">
            <div class="form-group">
                <label for="institution" class="mb-2"><b>Región</b></label>
                <input type="text" class="form-control" id="institution" v-model="fomrulario.name">
            </div>
        </div>
      asd
      {{ notifications }}
      
        <div class="mb-2">
            <div class="form-group">
                <!-- {{ myRegion }} -->
                <EditPolygon :puntos="myRegion.points" v-on:puntos="(puntos) => fomrulario.puntos = puntos" />
                </div>
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-success">Guardar</button>
            </div>
        </form>
        <hr>
        
        <h3 class="mb-3">Subcategorías de la región
            <!--Boton  Suscribir a todo -->
            <span class="float-end"> 
                
                <button type="submit" @click="setAllSubcategories()" class="btn btn-primary mr-1" title="Seleccionar todo"><i class="fas fa-check-double"></i></button>    
                <button type="submit" @click="destroyAll()" class="btn btn-secondary" title="Desmarcar todo"><i class="fas fa-minus"></i></button>         
                
            </span>
        </h3>
        <div class="blockquote-footer mb-3">Seleccione todas las subcategorías acordes a la región</div>        
        <div class="row mb-3">
            <div class="col-4" v-for="category in categories.filter(cat => cat.subcategories.length > 0)" :key="category.id">
                <div class="card h-100">
                    <div class="card-header text-center">
                        
                        <span v-html="category.icon" class="display-4 p-2"></span>
                        <p class="fs-4">{{ category.name }}</p>
                        
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item" v-for="subcategory in category.subcategories" :key="subcategory.id" role="button"
                        @click="toggleSubcategory(subcategory.id)"
                        >
                        {{ subcategory.name }}
                        <span class="float-end">
                            <i class="fas fa-check fs-5 text-success" v-if="form.subcategory_ids.includes(subcategory.id)"></i>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <hr>
    <h3 class="mb-3">Notificaciones
            <!--Boton  Suscribir a todo -->
            <span class="float-end"> 
                
                <button type="submit" @click="setAllSubcategories()" class="btn btn-primary mr-1" title="Seleccionar todo"><i class="fas fa-tasks"></i></button>    
                <button type="submit" @click="destroyAll()" class="btn btn-secondary" title="Desmarcar todo"><i class="fas fa-times"></i></button>         
                
            </span>
        </h3>
        <div class="blockquote-footer mb-3">Seleccione todas las notificaciones que quiere recibir para esta región</div>        

    {{ myNotifications }}
    <table class="table table-striped" v-if="myRegion.subcategories.length">
        <thead>
            <tr>
                <th>Subcategoría</th>
                <th>Suscripción</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="subcategory in myRegion.subcategories" :key="subcategory.id">
                <td>{{ subcategory.name }}</td>
                <td>
                    {{ subcategory.pivot }}
                    <button 
                    v-if="myNotifications.filter(notification => notification.region_subcategory_id == subcategory.pivot.id).length"
                    class="btn btn-secondary"
                    @click="unsetNotify(subcategory.pivot.id, myNotifications.filter(notification => notification.region_subcategory_id == subcategory.pivot.id)[0].id)"
                    >Desuscribir</button>

                    <button class="btn btn-primary" @click="setNotify(subcategory.pivot.id)" v-else>Suscribir</button>
                </td>
            </tr>
        </tbody>
    </table>
</AppLayout>
</template>
