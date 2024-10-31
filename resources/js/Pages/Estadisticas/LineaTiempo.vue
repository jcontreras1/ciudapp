<script setup>
import Highcharts from 'highcharts';
import exportingInit from 'highcharts/modules/exporting';
import exportDataInit from 'highcharts/modules/export-data';
import accessibilityInit from 'highcharts/modules/accessibility';
import { onMounted, ref, watch } from 'vue';

// Inicializar los módulos
exportingInit(Highcharts);
exportDataInit(Highcharts);
accessibilityInit(Highcharts);

const props = defineProps({
  reportes: {
    type: Array,
    required: true
  }
});

const reportes = ref(null);

watch(() => props.reportes, (newValue) => {
		// Convertir los reportes a GeoJSON
        reportes = newValue;
	}, { deep: true });

// Traduzco a español
Highcharts.setOptions({
  lang: {
    contextButtonTitle: "Menú de exportación",
    downloadCSV: "Descargar CSV",
    downloadJPEG: "Descargar imagen JPEG",
    downloadPDF: "Descargar documento PDF",
    downloadPNG: "Descargar imagen PNG",
    downloadSVG: "Descargar imagen SVG vectorial",
    downloadXLS: "Descargar Excel",
    printChart: "Imprimir gráfico",
    viewFullscreen: "Ver en pantalla completa",
    viewData: "Ver datos de la tabla",
    loading: "Cargando...",
    months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
    shortMonths: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
    weekdays: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"],
    decimalPoint: ",",
    thousandsSep: "."
  }
});

// Crear una referencia para el gráfico
const chartRef = ref(null);
onMounted(() => {
  Highcharts.chart(chartRef.value, {
    chart: {
      type: 'line'
    },
    title: {
      text: 'Mi gráfico de Highcharts'
    },
    series: [{
      name: 'Ejemplo',
      data: [1, 3, 2, 4]
    }]
  });
});
</script>

<template>
    <pre>{{ reportes }}</pre>
  <div ref="chartRef" style="width:100%; height:400px;"></div>
</template>