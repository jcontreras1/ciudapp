<script setup>
import { useForm, Head } from '@inertiajs/vue3';
import { ref, defineProps } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import SectionTitle from '@/Components/SectionTitle.vue';
import EditPolygon from '@/Pages/Mapa/EditPolygon.vue';

const props = defineProps({
    institucion : {
        type: Object,
        required: true
    },
    region : {
        type : Object,
        required : true,
    },

});

const fomrulario = useForm({
    'name' : '', //este hay que llenarlo en el formu
    'institution' : props.institucion.id,
    'region' : props.region.name,
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
    <AppLayout>
        <Head title="Editar Región" />
        <SectionTitle>
            <template #title>
                Editar región de la institución <b>{{institucion.name}}</b>
            </template>
        </SectionTitle>
        <hr>
        <form @submit.prevent="fomrulario.post(route('region.update', props.region.id))">

                <div class="mb-3">
                    <div class="form-group">
                        <label for="institution" class="mb-2"><b>Región</b></label>
                        <input type="text" class="form-control" id="institution" v-model="fomrulario.region">
                    </div>
                </div>
                 <div class="mb-2">
                    <div class="form-group">
                        <label for="institution" class="mb-2"><b>Coordenadas</b></label>
                        <EditPolygon :puntos="region.points"/>
                    </div>
                </div>




        </form>
    <!-- {{institucion}}} -->
        institución: <strong>{{ institucion.name }}</strong> <br>
        region: <strong>{{ region.name }}</strong>
  <br>
  <br>
  <br>
  <br>
    <<<<<<<<<<<<<<<< aca hay que meter el mapingo con los puntos >>>>>>>>>>>>>>

    estos son los puntos:

    <!-- <ul> -->
        <!-- <li v-for="punto in region.points" :key="punto.id">{{ punto }}</li> -->

    <!-- </ul> -->
</AppLayout>
</template>
