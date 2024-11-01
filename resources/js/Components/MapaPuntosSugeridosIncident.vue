<script setup>
import { onMounted, ref, watch } from 'vue'

const props = defineProps({
    lat: {
        type: Number,
        default: 0,
    },
    lng: {
        type: Number,
        default: 0,
    },
    puntos: {
        type: Object,
        default: []

    }
});
const apiKey = import.meta.env.VITE_MAPBOX_TOKEN;
const map = ref(null);
const mapContainer = ref(null);
//mapa que no se debe mover
onMounted(() => {
    mapboxgl.accessToken = apiKey;    
    map.value = new mapboxgl.Map({
        container: mapContainer.value,
        style: 'mapbox://styles/mapbox/streets-v11',
        center: [props.lng, props.lat],
        
        zoom: 14,
    });
    
    map.value.once('load', () => {
        map.value.resize();
        map.value.dragPan.disable();
        map.value.scrollZoom.disable();
        map.value.doubleClickZoom.disable();
        map.value.boxZoom.disable();
        map.value.touchZoomRotate.disable();
    });

    for(let i = 0; i < props.puntos.length; i++){
        let marker = new mapboxgl.Marker()
        .setLngLat([props.puntos[i].lng, props.puntos[i].lat])
        .addTo(map.value);
    }
});

</script>

<style>
.mapContainer {
    min-height: 220px;
    min-width: 100%;

}
</style>

<template>
    <div v-show="lat !== 0 && lng !== 0" class="mapContainer" ref="mapContainer"></div>
</template>
