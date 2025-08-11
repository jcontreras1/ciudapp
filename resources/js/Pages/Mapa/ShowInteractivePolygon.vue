<script setup>
import { onMounted, ref, watch, defineProps } from 'vue'
import mapboxgl from 'mapbox-gl';
import MapboxDraw from "@mapbox/mapbox-gl-draw";
import '@mapbox/mapbox-gl-draw/dist/mapbox-gl-draw.css';
import MapService from '@/Services/MapService';

const props = defineProps({
    puntos: {
        type: Array,
        required: true
    },
    id : {
        type: String,
        required: true
    }
});

const map = ref(null);
const draw = ref(null);

onMounted(() => {
    mapboxgl.accessToken = import.meta.env.VITE_MAPBOX_TOKEN;

    // Centro por defecto (Buenos Aires por ejemplo)
    const defaultCenter = [-58.3816, -34.6037];

    map.value = new mapboxgl.Map({
        container: props.id,
        style: 'mapbox://styles/mapbox/streets-v11',
        center: defaultCenter,
        zoom: 12,
    });

    draw.value = new MapboxDraw({
        displayControlsDefault: false,
        controls: {}
    });

    map.value.addControl(draw.value);

    map.value.on('load', () => {
        if (props.puntos.length > 0) {
            dibujarPoligono(props.puntos);
        }
    });
});

function centerMap(coordinates) {
    const bounds = coordinates.reduce((bounds, coord) => {
        return [
            [Math.min(bounds[0][0], coord[0]), Math.min(bounds[0][1], coord[1])],
            [Math.max(bounds[1][0], coord[0]), Math.max(bounds[1][1], coord[1])]
        ];
    }, [[Infinity, Infinity], [-Infinity, -Infinity]]);
    
    map.value.fitBounds(bounds, {
        padding: { top: 20, bottom: 20, left: 20, right: 20 },
        maxZoom: 15
    });
}

function dibujarPoligono(puntos) {
    if (!map.value || !draw.value) return;

    const coordsOrdenadas = MapService.ordenarPuntosAntihorario(puntos);

    draw.value.deleteAll();
    draw.value.add({
        type: 'Feature',
        geometry: {
            type: 'Polygon',
            coordinates: [coordsOrdenadas]
        }
    });

    centerMap(coordsOrdenadas);
}

// Detectar cambios en puntos
watch(() => props.puntos, (newPuntos) => {
    if (newPuntos.length > 0) {
        dibujarPoligono(newPuntos);
    }
}, { deep: true });
</script>

<template>
    <div :id="props.id" class="map"></div>
    {{ puntos }}
</template>

<style>
.map {
    height: 30vh;
}
</style>
