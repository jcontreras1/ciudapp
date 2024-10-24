<script setup>
import { onMounted, ref, watch } from 'vue';
import mapboxgl from 'mapbox-gl';
import MapboxDraw from "@mapbox/mapbox-gl-draw";
import '@mapbox/mapbox-gl-draw/dist/mapbox-gl-draw.css';
import * as turf from '@turf/turf';

const props = defineProps({
  reportes: {
    type: Array,
    required: false
  },
  regiones: {
    type: Object,
    required: false
  },
  redibujar: {
    type: Number,
    required: false
  }
});
const geoJsonReports = ref(null);
const map = ref(null); 

watch(
  () => props.redibujar,
  () => {
    map.value.resize();
  }
)


// Función para convertir las regiones a formato GeoJSON
function convertToGeoJSON(regions) {
  return {
    'type': 'FeatureCollection',
    'features': regions.map(region => ({
      'type': 'Feature',
      'geometry': {
        'type': 'Polygon',
        'coordinates': [region.points]
      },
      'properties': {
        'name': region.name
      }
    }))
  };
}



function convertReportsToGeoJSON(reportes) {
  return {
    'type': 'FeatureCollection',
    'features': reportes.map(report => ({
      'type': 'Feature',
      'geometry': {
        'type': 'Point', // Cambia esto a 'Point'
        'coordinates': [report.lng, report.lat]
      },
      'properties': {
        'intensidad': report.intensidad || 1 // Si tienes otras propiedades que usar
      }
    }))
  };
}





onMounted(() => {
  mapboxgl.accessToken = import.meta.env.VITE_MAPBOX_TOKEN;
  
  map.value = new mapboxgl.Map({
    container: 'map',
    style: 'mapbox://styles/mapbox/dark-v11',
    center: [-65.0385, -42.7692], // Coordenadas de Puerto Madryn
    zoom: 12
  });
  
  
  
  map.value.on('load', () => {
    // Convertir las regiones a GeoJSON
    const geojsonRegions = convertToGeoJSON(props.regiones);
    geoJsonReports.value = convertReportsToGeoJSON(props.reportes) ;
    
    // Agregar la fuente para las regiones
    map.value.addSource('regiones', {
      'type': 'geojson',
      'data': geojsonRegions
    });
    
    map.value.addSource('reportes', {
      'type': 'geojson',
      'data': geoJsonReports.value
    });
    
    map.value.addLayer(
    {
      'id': 'reportes',
      'type': 'heatmap',
      'source': 'reportes',
      'minzoom': 7,

    },
    'waterway-label'
    );
    
    // Dibujar las capas de polígonos
    map.value.addLayer({
      'id': 'regiones-fill',
      'type': 'fill',
      'source': 'regiones',
      'layout': {},
      'paint': {
        'fill-color': '#055',
        'fill-opacity': 0.2
      }
    });
    
    map.value.addLayer({
      'id': 'regiones-outline',
      'type': 'line',
      'source': 'regiones',
      'layout': {},
      'paint': {
        'line-color': '#fff',
        'line-opacity': 0.4,
        'line-width': 1
      }
    });
    
    map.value.addLayer({
      'id': 'regiones-labels',
      'type': 'symbol',
      'source': 'regiones',
      'layout': {
        'text-field': ['get', 'name'],
        'text-size': 12,
        'text-anchor': 'center',
        'text-offset': [0, 0]
      },
      'paint': {
        'text-color': '#fff',
        'text-halo-color': '#000',
        'text-halo-width': 1
      }
    });
    
    // Agregar capa de heatmap para reportes   
  });
});

watch(() => props.reportes, (newValue) => {
  // Convertir los reportes a GeoJSON
  geoJsonReports.value = convertReportsToGeoJSON(newValue);
  // Actualizar la fuente del mapa si existe
  const source = map.value.getSource('reportes');
  if (source) {
    source.setData(geoJsonReports.value);  // Actualiza los datos en la fuente del mapa
  }
}, { deep: true });


</script>

<template>
  <div id="map"></div>
  
</template>

<style>
#map {
  position: relative;
  width: 1000px;
  height: 500px;
  margin: 20px auto;
}
</style>
