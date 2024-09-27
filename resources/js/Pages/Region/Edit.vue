<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, defineProps } from 'vue'

const props = defineProps({
    institucion : {
        type: Object,
        required: true
    },
    region : {
        type : Object,
        required : true,
    }
});

const fomrulario = useForm({
    'name' : '', //este hay que llenarlo en el formu
    'institution' : props.institucion.id,
    'puntos' : [], //Este es el arreglo de todas las coordenadas que hacen el polígono. Tienen que estar en formato array así como lo dejo abajo:
});


/**
 * 
{
    "puntos" : [
        {
            "lat" : 1,
            "lng" : 2
        },
        {
            "lat" : 2,
            "lng" : 2
        },
        {
            "lat" : 3,
            "lng" : 3
        }
    ]
}
 */
</script>

<template>
        <div class="display-1">✨ EDITAR institución 💅 </div>
        <hr>
        Metele que no llegás<hr>
        institución: <strong>{{ institucion.name }}</strong> <br>
        region: <strong>{{ region.name }}</strong>
  <br>
  <br>
  <br>
  <br>
    <<<<<<<<<<<<<<<< aca hay que meter el mapingo con los puntos >>>>>>>>>>>>>>

    estos son los puntos:

    <ul>
        <li v-for="punto in region.points" :key="punto.id">{{ punto }}</li>
    </ul>

</template>