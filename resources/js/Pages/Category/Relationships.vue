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

// Creamos una variable reactiva `myProps` para almacenar `props.subcategories`
const myProps = ref({
    subcategories: [...props.subcategories]
});

// Usamos un objeto `relaciones` para almacenar las relaciones
const relaciones = ref({}); 

// Rellenamos la matriz `relaciones` con las relaciones
myProps.value.subcategories.forEach(subcategory => {
    subcategory.relationships.forEach(relationship => {
        const origin_id = relationship.pivot.origin_id;
        const destiny_id = relationship.pivot.destiny_id;
        const percentage = relationship.pivot.percentage;
        
        // Si el origin_id no existe aún en el objeto, inicializamos un arreglo vacío
        if (!relaciones.value[origin_id]) {
            relaciones.value[origin_id] = [];
        }
        
        // Verificamos si ya existe una relación con el destino, y la agregamos
        const existingRelationship = relaciones.value[origin_id].find(rel => rel[destiny_id]);
        
        if (existingRelationship) {
            // Si ya existe, actualizamos el porcentaje (en caso de que haya múltiples relaciones con el mismo destiny_id)
            existingRelationship[destiny_id] = percentage;
        } else {
            // Si no existe, la agregamos como un nuevo objeto { destiny_id: percentage }
            relaciones.value[origin_id].push({ [destiny_id]: percentage });
        }
    });
});

// Función para actualizar una relación al perder el foco (blur)
const actualizarFilaColumna = (e, origin, destiny) => {
    axios.post('/api/relationships', {
        origin_id: origin,
        destiny_id: destiny,
        percentage: e.target.value,
    }).then(response => {
        // Actualizamos myProps.subcategories con los nuevos datos que vienen del backend
        myProps.value.subcategories = response.data;
        myProps.value.subcategories.forEach(subcategory => {
    subcategory.relationships.forEach(relationship => {
        const origin_id = relationship.pivot.origin_id;
        const destiny_id = relationship.pivot.destiny_id;
        const percentage = relationship.pivot.percentage;
        
        // Si el origin_id no existe aún en el objeto, inicializamos un arreglo vacío
        if (!relaciones.value[origin_id]) {
            relaciones.value[origin_id] = [];
        }
        
        // Verificamos si ya existe una relación con el destino, y la agregamos
        const existingRelationship = relaciones.value[origin_id].find(rel => rel[destiny_id]);
        
        if (existingRelationship) {
            // Si ya existe, actualizamos el porcentaje (en caso de que haya múltiples relaciones con el mismo destiny_id)
            existingRelationship[destiny_id] = percentage;
        } else {
            // Si no existe, la agregamos como un nuevo objeto { destiny_id: percentage }
            relaciones.value[origin_id].push({ [destiny_id]: percentage });
        }
    });
});
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
        
        <!-- Usamos myProps.subcategories en vez de props.subcategories -->
        <table class="table">
            <thead>
                <tr>
                    <th></th>
                    <th v-for="(subcategory, index) in myProps.subcategories" :key="subcategory.id">{{ subcategory.name }}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(subcategory, rowIndex) in myProps.subcategories" :key="subcategory.id">
                    <td>{{ subcategory.name }}</td>
                    <td v-for="(otherSubcategory, colIndex) in myProps.subcategories" :key="otherSubcategory.id" align="center">
                        <div v-if="rowIndex >= colIndex">
                        <!-- Ocultamos la celda simétrica (cuando rowIndex === colIndex) -->

                             <span v-if="rowIndex === colIndex">100 <i class="fas fa-"></i></span>
                             <input v-else @blur="actualizarFilaColumna($event, rowIndex + 1, colIndex + 1)" 
                             type="number" 
                             :value="relaciones[rowIndex + 1]?.find(rel => rel[colIndex + 1])?.[colIndex + 1] || 0" 
                             class="form-control text-center"
                             v-if="rowIndex !== colIndex"> <!-- Solo mostrar el input si no es la celda simétrica -->
                            </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </AppLayout>
</template>