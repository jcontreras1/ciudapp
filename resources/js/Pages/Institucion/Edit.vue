<script setup>
import { ref, defineProps } from 'vue'


const props = defineProps({
    institucion : {
        type: Object,
        required: true
    },
    regiones : {
        type: Object,
        required: true
    }
})
</script>

<template>
        <div class="display-2">Editar {{ institucion.name }}</div>
    <hr>

    <br>
    <br>
    <br>
    <br>
    <br>

    <hr>
    <h3>Lista de regiones:</h3>
    <br>
    <br>
    <br>
    <a :href="route('region.create', institucion)">Crear una región, o polígono o lo que sea</a>
    <div v-for="region in regiones" :key="region.id">
        #{{ region.id }} --- {{ region.name }}  ------>      <a :href="route('region.edit', {'institution' : institucion, 'region' : region})">editar</a>
    </div>

</template>