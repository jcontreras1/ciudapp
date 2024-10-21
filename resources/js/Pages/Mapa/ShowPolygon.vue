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
    },
    id : {
        type: String,
        required: true
    }
});

onMounted(() => {
    const arrayBidimensional = props.puntos?.map(item => [item.lng, item.lat]);
    const center = turf.center(turf.polygon([arrayBidimensional]));
    mapboxgl.accessToken = import.meta.env.VITE_MAPBOX_TOKEN;
    
    const map = new mapboxgl.Map({
        container: props.id,
        style: 'mapbox://styles/mapbox/streets-v11',
        center: [center.geometry.coordinates[0], center.geometry.coordinates[1]],
        zoom: 12,
        interactive: false

           // Deshabilitar el arrastre del mapa
    });
    
    const draw = new MapboxDraw({
       displayControlsDefault: false, // Desactiva todos los controles
        controls: {}, // No permite editar ni borrar
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
            // emit('puntos', arrayBidimensional); // Emitir puntos inicialmente
            centerMap(arrayBidimensional); // Centrar el mapa en el polígono
        }
        
       map.on('draw.create', () => {
        return;
    });
    map.on('draw.update', () => {
        return;
    });
    map.on('draw.delete', () => {
        return;
    });
    });
    
    function centerMap(coordinates) {
        // Calcular los límites del polígono
        const bounds = coordinates.reduce((bounds, coord) => {
            return [
            [Math.min(bounds[0][0], coord[0]), Math.min(bounds[0][1], coord[1])],
            [Math.max(bounds[1][0], coord[0]), Math.max(bounds[1][1], coord[1])]
            ];
        }, [[Infinity, Infinity], [-Infinity, -Infinity]]);
        
        // Ajustar el mapa a los límites
        map.fitBounds(bounds, {
            padding: { top: 20, bottom: 20, left: 20, right: 20 },
            maxZoom: 15 // Ajusta el zoom máximo según tus necesidades
        });
    }
    
    // Verificar si cambian los puntos y actualizar el polígono
    // watch(() => props.puntos, (newPuntos) => {
    //     if (newPuntos.length > 0) {
    //         draw.deleteAll(); // Eliminar el polígono actual
    //         const newArrayBidimensional = newPuntos.map(item => [item.lng, item.lat]);
    //         draw.add({
    //             type: 'Feature',
    //             geometry: {
    //                 type: 'Polygon',
    //                 coordinates: [newArrayBidimensional]
    //             }
    //         });
          
    //     }
    // });
});
</script>

<template>
    <div :id="props.id" class="map"></div>
    <div class="calculation-box mb-3">
    </div>
</template>

<style>
.map {
    height: 30vh;
}
</style>
