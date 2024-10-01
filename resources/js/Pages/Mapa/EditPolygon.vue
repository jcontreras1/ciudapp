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
    mapboxgl.accessToken = import.meta.env.VITE_MAPBOX_TOKEN;
    const map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/streets-v11',
        center: [-65.0385100, -42.7692000],
        zoom: 14
    });

    map.on('load', () => {
        // Add a data source containing GeoJSON data.
        map.addSource('maine', {
            'type': 'geojson',
            'data': {
                'type': 'Feature',
                'geometry': {
                    'type': 'Polygon',
                    // These coordinates outline Maine.
                    'coordinates': [
                    [
                    [-65.0441962831117, -42.77094893620663],
                    [-65.04256550003089, -42.76656985745229],
                    [-65.03763023544333, -42.77116945716514],
                    [-65.0441962831117, -42.77094893620663]
                    ]
                    ]
                }
            }
        });

        // Add a new layer to visualize the polygon.
        map.addLayer({
            'id': 'maine',
            'type': 'fill',
            'source': 'maine', // reference the data source
            'layout': {},
            'paint': {
                'fill-color': '#0080ff', // blue color fill
                'fill-opacity': 0.5
            }
        });
        // Add a black outline around the polygon.
        map.addLayer({
            'id': 'outline',
            'type': 'line',
            'source': 'maine',
            'layout': {},
            'paint': {
                'line-color': '#000',
                'line-width': 3
            }
        });
    });


    const draw = new MapboxDraw({
        displayControlsDefault: false,
        controls: {
            polygon: true,
            trash: true
        },
        defaultMode: 'draw_polygon',
        style: [
        {
            "id": "gl-draw-polygon-fill",
            "type": "fill",
            "paint": {
                "fill-color": "#000000",
            }
        },
        {
            "id": "gl-draw-polygon-and-line-vertex-halo-active",
            "type": "circle",
            "paint": {
                "circle-radius": 5,
                "circle-color": "#000000",
            }
        },
        {
            "id": "gl-draw-polygon-and-line-vertex-active",
            "type": "circle",
            "paint": {
                "circle-radius": 10,
                "circle-color": "#000000",
            }
        }
        ],
    });

    map.addControl(draw);
    map.on('draw.create', updateArea);
    map.on('draw.delete', updateArea);
    map.on('draw.update', updateArea);

    // Función para actualizar el área y emitir los puntos
    function updateArea(e) {
        const data = draw.getAll();
        const answer = document.getElementById('calculated-area');
        if (data.features.length > 0) {
            const coordinates = data.features[0].geometry.coordinates[0]; // Coordenadas del polígono
            emit('puntos', coordinates);
            const area = turf.area(data);
            const rounded_area = Math.round(area * 100) / 100;
            answer.innerHTML = `<p><strong>${rounded_area}</strong></p><p>square meters</p>`;
        } else {
            answer.innerHTML = '';
            if (e.type !== 'draw.delete')
            alert('Click the map to draw a polygon.');
        }
    }

    // Verificar si cambian los puntos y dibujar el polígono
    watch(() => props.puntos, (newPuntos) => {
        if (newPuntos.length > 0) {
            if (map.getSource('polygon')) {
                map.removeLayer('polygon');
                map.removeSource('polygon');
            }

            map.addSource('polygon', {
                'type': 'geojson',
                'data': {
                    'type': 'Feature',
                    'geometry': {
                        'type': 'Polygon',
                        'coordinates': [newPuntos]
                    }
                }
            });

            map.addLayer({
                'id': 'polygon',
                'type': 'fill',
                'source': 'polygon',
                'layout': {},
                'paint': {
                    'fill-color': '#888888',
                    'fill-opacity': 0.4
                }
            });
        }
    });
});
</script>

<template>
    <pre>
        {{puntos}}
    </pre>
    <div id="map"></div>

    <div class="calculation-box">
        <p>Haga clic en el mapa para comenzar a definir la región</p>
        <div id="calculated-area"></div>
    </div>
</template>

<style>
#map{
    height: 50vh;
}
</style>
