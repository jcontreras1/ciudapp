<script setup>
import { onMounted, ref } from 'vue'
import mapboxgl from 'mapbox-gl';
import MapboxDraw from "@mapbox/mapbox-gl-draw";
import '@mapbox/mapbox-gl-draw/dist/mapbox-gl-draw.css';
import * as turf from '@turf/turf';

const emit = defineEmits(['puntos'])
onMounted(() => {
    mapboxgl.accessToken = import.meta.env.VITE_MAPBOX_TOKEN;
    // Crear el mapa y centrarlo en una ubicación
    const map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/streets-v11', // Estilo del mapa
        center: [-65.0385100, -42.7692000], // Coordenadas [lng, lat]
        zoom: 14
    });


    const draw = new MapboxDraw({
        displayControlsDefault: false,
        // Select which mapbox-gl-draw control buttons to add to the map.
        controls: {
            polygon: true,
            trash: true
        },
        defaultMode: 'draw_polygon',
        style:[{
            "id": "gl-draw-polygon-fill",
            "type": "fill",
            "paint": {
                "fill-color": "#000000", // Color de relleno

            }
        },
        {
            "id": "gl-draw-polygon-and-line-vertex-halo-active",
            "type": "circle",
            "paint": {
                "circle-radius": 5,
                "circle-color": "000000", // Color de los vértices
            }
        },
        {
            "id": "gl-draw-polygon-and-line-vertex-active",
            "type": "circle",
            "paint": {
                "circle-radius": 10,
                "circle-color": "#000000", // Color del interior de los vértices
            }
        }
        ],

        // Set mapbox-gl-draw to draw by default.
        // The user does not have to click the polygon control button first.
    });

    map.addControl(draw);
    map.on('draw.create', updateArea);
    map.on('draw.delete', updateArea);
    map.on('draw.update', updateArea);

    function updateArea(e) {
        const data = draw.getAll();
        const answer = document.getElementById('calculated-area');
        if (data.features.length > 0) {
            const coordinates = data.features[0].geometry.coordinates[0]; // Coordenadas del polígono
            emit('puntos', coordinates);
            const area = turf.area(data);
            // Restrict the area to 2 decimal points.
            const rounded_area = Math.round(area * 100) / 100;
            answer.innerHTML = `<p><strong></strong></p><p></p>`;
        } else {
            answer.innerHTML = '';
            if (e.type !== 'draw.delete')
            alert('Click the map to draw a polygon.');
        }
    }
});
</script>



<template>
    <div id="map"></div>
    <div class="calculation-box">
        <p>Haga clic en el mapa para comenzar a definir la región</p>
        <div id="calculated-area"></div>
    </div>
</template>

<style>
#map{
    height: 80vh;
}
</style>


