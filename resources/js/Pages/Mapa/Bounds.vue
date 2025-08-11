<script setup>
import {  ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import SectionTitle from '@/Components/SectionTitle.vue';
import mapboxgl from 'mapbox-gl';
import '@mapbox/mapbox-gl-draw/dist/mapbox-gl-draw.css';
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed } from 'vue';
import CreatePolygon from '@/Pages/Mapa/CreatePolygon.vue';
import MapService from '@/Services/MapService.js';
import ShowInteractivePolygon from './ShowInteractivePolygon.vue';
import debounce from 'lodash/debounce';


const query = ref('')
const sugerencias = ref([])
const selectedCalles = ref([])

const MAPBOX_TOKEN = import.meta.env.VITE_MAPBOX_TOKEN



const buscarCalles = debounce(async () => {
  if (query.value.length < 3) {
    sugerencias.value = [];
    return;
  }
  let transformQuery = query.value.trim() + ', Puerto Madryn, Chubut, Argentina';
  const url = `https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(
  transformQuery
  )}.json?country=ar&types=address,place&autocomplete=true&access_token=${MAPBOX_TOKEN}`;
  
  const res = await fetch(url);
  const data = await res.json();
  
  sugerencias.value = data.features;
}, 500); // medio segundo

function seleccionarCalle(item) {
  selectedCalles.value.push(item.text)
  query.value = ''
  sugerencias.value = []
}

function eliminarCalle(index) {
  selectedCalles.value.splice(index, 1)
}

const puntos = ref([]);


const ponerLosPuntos = () => {
  puntos.value = [
  {
    "lat": -42.7756035,
    "lng": -65.0337991
  },
  {
    "lat": -42.77514,
    "lng": -65.0324658
  },
  {
    "lat": -42.7765843,
    "lng": -65.03318279999999
  },
  {
    "lat": -42.7761305,
    "lng": -65.0318456
  }
  ];
};

const formInvalid = computed(() => {
  return selectedCalles.value.length < 3;
});

const buscarPoligono = async () => {
  console.log("hola")
  if (formInvalid.value) return;
  console.log('Buscando polígono para las calles:', selectedCalles.value);
  const res = await MapService.getBounds(selectedCalles.value);
  if (res) {
    puntos.value = res.data;
  }
}

const llenarConCallesFalsas = () => {
  selectedCalles.value = [
  'Moreno',
  'Villarino',
  'Marcos A. Zar',
  'San Martín',
  ];
}

</script>

<template>
  <AppLayout>
    <Head title="Reportes entre calles" />
    <SectionTitle>
      <template #title>
        Buscar reportes entre calles
      </template>
    </SectionTitle>
    <hr class="mb-3">
    
    <input
    v-model="query"
    @input="buscarCalles"
    placeholder="Escribe el nombre de la calle"
    class="form-control mb-2"
    />
    
    <ul v-if="sugerencias.length" class="list-group mb-3">
      <li v-for="(item, index) in sugerencias" :key="index" class="list-group-item list-group-item-action" @click="seleccionarCalle(item)" >
        {{ item.place_name }}
      </li>
    </ul>
    
    <h5>Calles seleccionadas:</h5>
    <ul class="list-group">
      <li
      v-for="(calle, index) in selectedCalles"
      :key="index"
      class="list-group-item d-flex justify-content-between align-items-center"
      >
      {{ calle }}
      <button class="btn btn-sm btn-danger" @click="eliminarCalle(index)"><i class="fas fa-trash"></i></button>
    </li>
  </ul>
  
  <button class="btn btn-success my-3" :class="{'btn-danger': formInvalid}" @click="buscarPoligono" :disabled="formInvalid">
    TEST
  </button>
  <button class="btn btn-info mx-1" @click="llenarConCallesFalsas">
    Calles Falsas
  </button>

  <button class="btn btn-danger" @click="selectedCalles = []">
    Vaciar Campos
  </button>

  <ShowInteractivePolygon :puntos="puntos" id="esteMapa"></ShowInteractivePolygon>
</AppLayout>
</template>
