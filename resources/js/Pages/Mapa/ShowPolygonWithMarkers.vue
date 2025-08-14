<script setup>
import { onMounted, watch, defineProps } from 'vue'
import mapboxgl from 'mapbox-gl';
import MapboxDraw from "@mapbox/mapbox-gl-draw";
import '@mapbox/mapbox-gl-draw/dist/mapbox-gl-draw.css';
import * as turf from '@turf/turf';
import MapService from '@/Services/MapService';

const props = defineProps({
    puntos: {
        type: Array,
        required: true
    },
    id: {
        type: String,
        required: true
    },
    markers: {
        type: Array,
        required: true
    }
});

let map, draw;

function renderMap() {
    // let arrayBidimensional = MapService.ordenarPuntosAntihorario(props.puntos);
    const arrayBidimensional = MapService.ordenarPuntosAntihorario(props.puntos);

    const center = turf.center(turf.polygon([arrayBidimensional]));
    mapboxgl.accessToken = import.meta.env.VITE_MAPBOX_TOKEN;

    if (map) {
        map.remove();
    }

    map = new mapboxgl.Map({
        container: props.id,
        style: 'mapbox://styles/mapbox/streets-v11',
        center: [center.geometry.coordinates[0], center.geometry.coordinates[1]],
        zoom: 12,
        interactive: false
    });

    draw = new MapboxDraw({
        displayControlsDefault: false,
        controls: {},
    });

    map.addControl(draw);

    map.on('load', () => {
        // Dibujar el polígono
        if (arrayBidimensional.length > 0) {
            draw.add({
                type: 'Feature',
                geometry: {
                    type: 'Polygon',
                    coordinates: [arrayBidimensional]
                }
            });
            centerMap(arrayBidimensional);
        }

        // Agregar los marcadores
        if (Array.isArray(props.markers)) {
            props.markers.forEach(marker => {
                new mapboxgl.Marker()
                    .setLngLat([marker.lng, marker.lat])
                    .addTo(map);
            });
        }
    });

    function centerMap(coordinates) {
        const bounds = coordinates.reduce((bounds, coord) => {
            return [
                [Math.min(bounds[0][0], coord[0]), Math.min(bounds[0][1], coord[1])],
                [Math.max(bounds[1][0], coord[0]), Math.max(bounds[1][1], coord[1])]
            ];
        }, [[Infinity, Infinity], [-Infinity, -Infinity]]);
        map.fitBounds(bounds, {
            padding: { top: 20, bottom: 20, left: 20, right: 20 },
            maxZoom: 15
        });
    }
}

onMounted(() => {
    renderMap();
});

watch(
    () => [props.puntos, props.markers],
    () => {
        renderMap();
    },
    { deep: true }
);

</script>

<template>
    <div :id="props.id" class="map"></div>
    <div class="calculation-box mb-3"></div>
</template>

<style>
.map {
    height: 30vh;
}
</style>