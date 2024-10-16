<script setup>
import { onMounted } from 'vue';
import mapboxgl from 'mapbox-gl';
import MapboxDraw from "@mapbox/mapbox-gl-draw";
import '@mapbox/mapbox-gl-draw/dist/mapbox-gl-draw.css';
import * as turf from '@turf/turf';

onMounted(() => {
  mapboxgl.accessToken = import.meta.env.VITE_MAPBOX_TOKEN;

  const map = new mapboxgl.Map({
    container: 'map',
    style: 'mapbox://styles/mapbox/dark-v11',
    center: [-65.0385, -42.7692],  // Coordenadas de Puerto Madryn
    zoom: 14  // Zoom ajustado para una vista más cercana
  });

  map.on('load', () => {
    // Fuente de los terremotos (ya existente)
    map.addSource('earthquakes', {
      'type': 'geojson',
      'data': 'https://docs.mapbox.com/mapbox-gl-js/assets/earthquakes.geojson'
    });

    // Capa de calor (ya existente)
    map.addLayer({
      'id': 'earthquakes-heat',
      'type': 'heatmap',
      'source': 'earthquakes',
      'maxzoom': 9,
      'paint': {
        'heatmap-weight': [
          'interpolate',
          ['linear'],
          ['get', 'mag'],
          0,
          0,
          6,
          1
        ],
        'heatmap-intensity': [
          'interpolate',
          ['linear'],
          ['zoom'],
          0,
          1,
          9,
          3
        ],
        'heatmap-color': [
          'interpolate',
          ['linear'],
          ['heatmap-density'],
          0,
          'rgba(33,102,172,0)',
          0.2,
          'rgb(103,169,207)',
          0.4,
          'rgb(209,229,240)',
          0.6,
          'rgb(253,219,199)',
          0.8,
          'rgb(239,138,98)',
          1,
          'rgb(178,24,43)'
        ],
        'heatmap-radius': [
          'interpolate',
          ['linear'],
          ['zoom'],
          0,
          2,
          9,
          20
        ],
        'heatmap-opacity': [
          'interpolate',
          ['linear'],
          ['zoom'],
          7,
          1,
          9,
          0
        ]
      }
    });

    // Agregar fuente para los puntos específicos en Bouchard y España
    map.addSource('puntos-puerto-madryn', {
      'type': 'geojson',
      'data': {
        'type': 'FeatureCollection',
        'features': [
          {
            'type': 'Feature',
            'geometry': {
              'type': 'Point',
              'coordinates': [-65.0385, -42.7692] // Calle Bouchard y España
            },
            'properties': {
              'title': 'Bouchard y España'
            }
          },
          {
            'type': 'Feature',
            'geometry': {
              'type': 'Point',
              'coordinates': [-65.0375, -42.7680] // Otra ubicación cercana
            },
            'properties': {
              'title': 'Otra ubicación en Bouchard y España'
            }
          }
        ]
      }
    });

    // Capa de calor para los puntos específicos
    map.addLayer({
      'id': 'puntos-puerto-madryn-heat',
      'type': 'heatmap',
      'source': 'puntos-puerto-madryn',
      'paint': {
        'heatmap-weight': 1,  // Peso del calor (ajustar si es necesario)
        'heatmap-intensity': [
          'interpolate',
          ['linear'],
          ['zoom'],
          10,
          1,
          15,
          3
        ],
        'heatmap-color': [
          'interpolate',
          ['linear'],
          ['heatmap-density'],
          0,
          'rgba(33,102,172,0)',
          0.2,
          'rgb(103,169,207)',
          0.4,
          'rgb(209,229,240)',
          0.6,
          'rgb(253,219,199)',
          0.8,
          'rgb(239,138,98)',
          1,
          'rgb(178,24,43)'
        ],
        'heatmap-radius': [
          'interpolate',
          ['linear'],
          ['zoom'],
          10,
          15,
          15,
          50
        ],
        'heatmap-opacity': [
          'interpolate',
          ['linear'],
          ['zoom'],
          12,
          0.7,
          15,
          0.3
        ]
      }
    });
  });
});
</script>

<template>
  <div id="map"></div>
</template>

<style>
#map {
  position: absolute;
  top: 0;
  bottom: 0;
  width: 100%;
}
</style>
