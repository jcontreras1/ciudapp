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
// Función para convertir los reportes a formato GeoJSON
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
				'likes': report.likes || 1, // Si tienes otras propiedades que usar
				'comment' : report.post.comment || ''
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
		
		
		/**
		* 
		* _____          _      ____  _____  
		/ ____|   /\   | |    / __ \|  __ \ 
		| |       /  \  | |   | |  | | |__) |
		| |      / /\ \ | |   | |  | |  _  / 
		| |____ / ____ \| |___| |__| | | \ \ 
		\_____/_/    \_\______\____/|_|  \_\ 
		*/
		map.value.addLayer(
		{
			'id': 'reportes',
			'type': 'heatmap',
			'source': 'reportes',
			'minzoom': 7,
			'paint': {
				'heatmap-weight': [
				'interpolate',
				['linear'],
				['get', 'likes'],
				0, 0,  // 0 likes -> 0 weight
				10, 2,  // 10 likes -> weight 1
				20, 4,  // 20 likes -> weight 2
				30, 6   // 30 likes -> weight 3
				],
				// Increase the heatmap color weight weight by zoom level
				// heatmap-intensity is a multiplier on top of heatmap-weight
				'heatmap-intensity': [
				'interpolate',
				['linear'],
				['zoom'],
				0, 1,
				15, 5  // Cambia de 3 a 5 para aumentar la intensidad
				],
				// Color ramp for heatmap.  Domain is 0 (low) to 1 (high).
				// Begin color ramp at 0-stop with a 0-transparancy color
				// to create a blur-like effect.
				'heatmap-color': [
				'interpolate',
				['linear'],
				['heatmap-density'],
				0, 'rgba(33,102,172,0)',
				0.2, 'rgb(103,169,207)',
				0.4, 'rgb(209,229,240)',
				0.6, 'rgb(253,219,199)',
				0.8, 'rgb(239,138,98)',
				1, 'rgb(178,24,43)'  // El rojo final puede ser más intenso
				],
				// Adjust the heatmap radius by zoom level
				'heatmap-radius': [
				'interpolate',
				['linear'],
				['zoom'],
				0,15, 		//radio base
				10,25		// radio máximo
				],
				// Transition from heatmap to circle layer by zoom level
				'heatmap-opacity': [
				'interpolate',
				['linear'],
				['zoom'],
				0,
				3,
				17,
				0
				]
			}
		},
		'waterway-label'
		);
		
		
		/**
		_____ _____ _____   _____ _    _ _      ____   _____ 
		/ ____|_   _|  __ \ / ____| |  | | |    / __ \ / ____|
		| |      | | | |__) | |    | |  | | |   | |  | | (___  
		| |      | | |  _  /| |    | |  | | |   | |  | |\___ \ 
		| |____ _| |_| | \ \| |____| |__| | |___| |__| |____) |
		\_____|_____|_|  \_\\_____|\____/|______\____/|_____/ 
		*/
		map.value.addLayer(
		{
			'id': 'earthquakes-point',
			'type': 'circle',
			'source': 'reportes',
			'minzoom': 7,
			'paint': {
				// Size circle radius by earthquake magnitude and zoom level
				'circle-radius': [
				'interpolate',
				['linear'],
				['zoom'],
				7,
				['interpolate', ['linear'], ['get', 'likes'], 1, 4, 6, 4],
				16,
				['interpolate', ['linear'], ['get', 'likes'], 1, 8, 6, 25]
				],
				// Color circle by earthquake likesnitude
				'circle-color': [
				'interpolate',
				['linear'],
				['get', 'likes'],
				1,
				'rgba(33,102,172,0)',
				2,
				'rgb(103,169,207)',
				3,
				'rgb(209,229,240)',
				4,
				'rgb(253,219,199)',
				5,
				'rgb(239,138,98)',
				6,
				'rgba(078,24,43,0)'
				],
				'circle-stroke-color': 'white',
				'circle-stroke-width': 1,
				// Transition from heatmap to circle layer by zoom level
				'circle-opacity': [
				'interpolate',
				['linear'],
				['zoom'],
				7,
				0,
				20,
				3
				]
			}
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
		
		map.value.on('click', 'reportes', (e) => {
			const coordinates = e.features[0].geometry.coordinates.slice();
			const likes = e.features[0].properties.likes;
			const textoCorto = e.features[0].properties.comment;
			
			// Ensure that if the map is zoomed out such that
			// multiple copies of the feature are visible, the
			// popup appears over the copy being pointed to.
			if (['mercator', 'equirectangular'].includes(map.value.getProjection().name)) {
				while (Math.abs(e.lngLat.lng - coordinates[0]) > 180) {
					coordinates[0] += e.lngLat.lng > coordinates[0] ? 360 : -360;
				}
			}
			
			new mapboxgl.Popup()
			.setLngLat(coordinates)
			.setHTML(
			`<div class="custom-popup">
           	Visivilización: ${likes}<br>
            ${textoCorto}
       		 </div>`
				)
				.addTo(map.value);
			});
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
	
	
	
	<pre>{{ reportes }}</pre>
</template>

<style>
#map {
	position: relative;
	width: 1000px;
	height: 500px;
	margin: 20px auto;
}
.mapboxgl-popup-close-button {
	color: black; /* Cambia el color del texto a negro */
}
.custom-popup {
	color: black; /* Cambia el color del texto a negro */
}
</style>
