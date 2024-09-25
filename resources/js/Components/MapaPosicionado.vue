<script setup>
import { onMounted, ref, watch  } from 'vue'
const apiKey = import.meta.env.VITE_MAPBOX_TOKEN;

const props = defineProps({
    post: Object,
});


onMounted(() => {
    mapboxgl.accessToken = apiKey;

});


const mapContainer = ref(null);

watch(() => props.post, (first,) => {
    const map = new mapboxgl.Map({
        container: mapContainer.value, // container ID
        style: 'mapbox://styles/mapbox/streets-v11', // Add a style to your map
        center: [props.post.lng, props.post.lat], // Use props for dynamic location
        zoom: 12,  // Adjust zoom level for better visibility
        interactive: false
    });

    map.once('load', () => {
        map.resize();
    });

    const marker1 = new mapboxgl.Marker()
    .setLngLat([props.post.lng, props.post.lat])
    .addTo(map);
});

/**
*
*
* mapboxgl.accessToken = apiKey;
const map = new mapboxgl.Map({
container: mapContainer.value, // container ID
style: 'mapbox://styles/mapbox/streets-v11', // Add a style to your map
center: [props.post.lng, props.post.lat], // Use props for dynamic location
zoom: 12  // Adjust zoom level for better visibility
});
//     console.log(props.post.lat , props.post.lng)
//     const marker1 = new mapboxgl.Marker()
//     .setLngLat([props.post.lng, props.post.lat])
//     .addTo(map);
*/

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
