<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SectionTitle from '@/Components/SectionTitle.vue';

const props = defineProps({
    subcategories: {
        type: Array,
        required: true,
    },
});
const mySubcategories = ref(props.subcategories);

const filteredSubcategories = ref(mySubcategories.value);
const filter = (event) => {
    //filtrar por subcategory.name o por subcategory.category.name
    filteredSubcategories.value = mySubcategories.value.filter(subcategory => subcategory.name.toLowerCase().includes(event.target.value.toLowerCase()) || subcategory.category.name.toLowerCase().includes(event.target.value.toLowerCase()));
}

// Función para actualizar una relación al perder el foco (blur)
const actualizar = (e, origin, destiny) => {
    if(e.target.value < 0 || e.target.value > 100 || isNaN(e.target.value)){
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'El porcentaje debe estar entre 0 y 100',
        });
        e.target.value = 0;
        return;
    }
    axios.post('/api/relationships', {
        origin_id: origin,
        destiny_id: destiny,
        percentage: e.target.value,
    }).then(response => {
        // Actualizamos mymySubcategories.value con los nuevos datos que vienen del backend
        mySubcategories.value = response.data;
        
    }).catch(error => {
        let errors = null;
        if (error.response.data.errors) {
            errors = '';
            for (let aux in error.response.data.errors) {
                errors += error.response.data.errors[aux] + '<br>';
            }
        }
        if (error.response) {
            Swal.fire({
                allowEnterKey: true,
                focusConfirm: true,
                icon: 'error',
                title: `Error ${error.response.status}`,
                html: errors ?? error.response.data,
            });
        }
    });
    
}
</script>

<template>
    <AppLayout>
        <Head title="Categorías" />
        
        <SectionTitle>
            <template #title>
                Relaciones entre subcategorías
            </template>
            <template #aside>
            </template>
        </SectionTitle>
        
        <hr>
        <div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon1"><i class="fas fa-search"></i></span>
            <input class="form-control " type="text" placeholder="Buscar" @input="filter">
        </div>
        <div class="row">
            <div class="col-12 col-md-6" v-for="subcategory in filteredSubcategories">
                <div class="card mb-2">
                    <div class="card-body">
                        <h5 class="card-title">{{ subcategory.name }} - {{ subcategory.category.name }}</h5>
                        <hr>
                        <div class="row">
                            <div class="col-12" v-for="innerSubcategory in mySubcategories">
                                <div v-if="innerSubcategory.id !== subcategory.id">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text col-1" v-html="innerSubcategory.icon"></span>
                                        <span class="input-group-text col-4">{{ innerSubcategory.name }}</span>
                                        <input 
                                        class="form-control form-control-sm" 
                                        type="number"
                                        :value="innerSubcategory.relationships && innerSubcategory.relationships.filter( rel => (rel.pivot.destiny_id === subcategory.id) && (rel.pivot.origin_id === innerSubcategory.id) )?.[0]?.pivot.percentage || 0"
                                        @blur="actualizar($event, innerSubcategory.id, subcategory.id)"
                                        >
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="!filteredSubcategories.length"><em>No hay resultados que coincidan con su búsqueda...</em></div>
        </div>
    </AppLayout>
</template>