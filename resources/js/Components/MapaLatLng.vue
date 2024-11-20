<script setup>
import { onMounted, ref, watch } from 'vue'

const emit = defineEmits(['update:lat', 'update:lng']);

const props = defineProps({
    lat: {
        type: Number,
        default: 0,
    },
    lng: {
        type: Number,
        default: 0,
    },
});

const apiKey = import.meta.env.VITE_MAPBOX_TOKEN;

const onDragEnd = (event) => {
    const { lng, lat } = event.target.getLngLat();
    emit('update:lat', lat);
    emit('update:lng', lng);
};

const mapContainer = ref(null);
const marker = new mapboxgl.Marker({ draggable: true });
marker.on('dragend', onDragEnd);

const map = ref(null);
const initialZoom = 12; // Zoom inicial cuando se carga el mapa

onMounted(() => {
    map.value = new mapboxgl.Map({
        container: mapContainer.value,
        style: 'mapbox://styles/mapbox/streets-v11',
        center: [props.lng, props.lat],
        zoom: initialZoom,  // Asignamos el zoom inicial
        interactive: true, // Permitimos interacción (zoom, pan, etc.)
    });

    mapboxgl.accessToken = apiKey;

    map.value.once('load', () => {
        map.value.resize();
    });

    marker.setLngLat([props.lng, props.lat]).addTo(map.value);
    
    // Mantener el zoom actualizado solo cuando el usuario interactúe
    map.value.on('zoom', () => {
        // No hacemos nada aquí, el zoom lo controla el usuario.
    });

    // Evitar que el zoom se cambie cuando se mueva el marcador
    marker.on('dragend', () => {
        const currentZoom = map.value.getZoom(); // Obtiene el zoom actual
        map.value.setZoom(currentZoom); // Vuelve a aplicar el mismo zoom
    });
});

// Observa cambios en latitud y longitud
watch(() => [props.lat, props.lng], ([newLat, newLng]) => {
    if (map.value) {
        marker.remove();
        map.value.jumpTo({
            center: [newLng, newLat],
            zoom: map.value.getZoom(), // Mantener el zoom actual
        });
        marker.setLngLat([newLng, newLat]).addTo(map.value);
        setTimeout(() => {
            map.value.resize();
        }, 100); // Ajusta el tiempo si es necesario
    }
});
</script>

<style>
.mapContainer {
    height: 300px;
    width: 100%;
    text-align: left;
}
</style>

<template>
    <div v-show="lat !== 0 && lng !== 0" class="mapContainer" ref="mapContainer"></div>
</template>
