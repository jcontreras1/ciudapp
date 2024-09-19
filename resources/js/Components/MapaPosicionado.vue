<script setup>
import { onMounted, ref } from 'vue'

const props = defineProps({
    lat: {
        type: Number,
        //default: -42.736414,  // Default to Puerto Madryn if not provided
    },
    lng: {
        type: Number,
        //default: -65.035897,  // Default to Puerto Madryn if not provided
    },
});

const mapContainer = ref(null);
onMounted(() => {
    mapboxgl.accessToken = 'pk.eyJ1IjoiamNvbnRyZXJhczEiLCJhIjoiY20xNTVrcjgyMDVxZTJpcHpzbXpmZWJheCJ9.9ib41gRO1wxGVeIuSu6TBw';
    const map = new mapboxgl.Map({
        container: mapContainer.value, // container ID
        style: 'mapbox://styles/mapbox/streets-v11', // Add a style to your map
        center: [props.lng, props.lat], // Use props for dynamic location
        zoom: 12  // Adjust zoom level for better visibility
    });
    console.log(props.lat , props.lng)
    const marker1 = new mapboxgl.Marker()
        .setLngLat([props.lng, props.lat])
        .addTo(map);
});

</script>
<style>
    .mapContainer {
        height: 300px;
        width: 100%;
    }
</style>
<template>
<div class="mapContainer" ref="mapContainer"></div>
</template>
