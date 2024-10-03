<script setup>
import { onMounted, ref, watch, defineProps } from 'vue'
import mapboxgl from 'mapbox-gl';
import MapboxDraw from "@mapbox/mapbox-gl-draw";
import '@mapbox/mapbox-gl-draw/dist/mapbox-gl-draw.css';
import * as turf from '@turf/turf';

const props = defineProps({
    puntos: {
        type: Array,
        required: true
    }
});

const emit = defineEmits(['puntos'])

onMounted(() => {
    const arrayBidimensional = props.puntos.map(item => [item.lng, item.lat]);
    mapboxgl.accessToken = import.meta.env.VITE_MAPBOX_TOKEN;
    const map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/streets-v11',
        center: [-65.0385100, -42.7692000],
        zoom: 13
    });

    const draw = new MapboxDraw({
        simple_select: true,
        displayControlsDefault: false,
        controls: {
            // polygon: true,
            // trash: true,
        },
    });

    map.addControl(draw);

    map.on('load', () => {
        // Cargar el polígono inicial
        if (arrayBidimensional.length > 0) {
            draw.add({
                type: 'Feature',
                geometry: {
                    type: 'Polygon',
                    coordinates: [arrayBidimensional]
                }
            });
            emit('puntos', arrayBidimensional); // Emitir puntos inicialmente
            centerMap(arrayBidimensional); // Centrar el mapa en el polígono
        }

        // Actualizar el área y emitir puntos en eventos de dibujo
        map.on('draw.create', updateArea);
        map.on('draw.delete', updateArea);
        map.on('draw.update', updateArea);
    });

    // Función para centrar el mapa en el polígono
    function centerMap(coordinates) {
        const center = turf.center(turf.polygon([coordinates]));
        map.setCenter([center.geometry.coordinates[0], center.geometry.coordinates[1]]);
    }

    // Función para actualizar el área y emitir los puntos
    function updateArea(e) {
        const data = draw.getAll();
        const answer = document.getElementById('calculated-area');
        if (data.features.length > 0) {
            const coordinates = data.features[0].geometry.coordinates[0]; // Coordenadas del polígono

            emit('puntos', coordinates); // Emitir puntos cada vez que se actualiza el polígono
            const area = turf.area(data);
            const rounded_area = Math.round(area * 100) / 100;
            answer.innerHTML = `<p><strong>${rounded_area}</strong></p><p>square meters</p>`;
            centerMap(coordinates); // Centrar el mapa en el nuevo polígono
        } else {
            answer.innerHTML = '';
            if (e.type !== 'draw.delete')
                alert('Click the map to draw a polygon.');
        }
    }

    // Verificar si cambian los puntos y actualizar el polígono
    watch(() => props.puntos, (newPuntos) => {
        if (newPuntos.length > 0) {
            draw.deleteAll(); // Eliminar el polígono actual
            const newArrayBidimensional = newPuntos.map(item => [item.lng, item.lat]);
            draw.add({
                type: 'Feature',
                geometry: {
                    type: 'Polygon',
                    coordinates: [newArrayBidimensional]
                }
            });
            emit('puntos', newArrayBidimensional); // Emitir nuevos puntos
            centerMap(newArrayBidimensional); // Centrar el mapa en el nuevo polígono
        }
    });
});
</script>

<template>
    <div id="map"></div>
    <div class="calculation-box">
        <p>Haga clic en la zona y muévalo para editar</p>
        <!-- <div id="calculated-area"></div> -->
    </div>
</template>

<style>
#map {
    height: 50vh;
}
</style>
