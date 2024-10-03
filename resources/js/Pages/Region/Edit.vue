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
    'name' : props.region.name,
    'institution' : props.institucion.id,
    'puntos' : [], //Este es el arreglo de todas las coordenadas que hacen el polígono. Tienen que estar en formato array así como lo dejo abajo:
});


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
        <form @submit.prevent="fomrulario.put(route('region.update', {
            'region' : props.region.id,
            'institution' : props.institucion.id
        }))">

                <div class="mb-3">
                    <div class="form-group">
                        <label for="institution" class="mb-2"><b>Región</b></label>
                        <input type="text" class="form-control" id="institution" v-model="fomrulario.name">
                    </div>
                </div>
                 <div class="mb-2">
                    <div class="form-group">
                        <label for="institution" class="mb-2"><b>Coordenadas</b></label>
                        <EditPolygon :puntos="region.points"  v-on:puntos="(puntos) => fomrulario.puntos = puntos" />
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Guardar</button>




        </form>
        <!-- institución: <strong>{{ institucion.name }}</strong> <br> -->
        <!-- region: <strong>{{ region.name }}</strong> -->

</AppLayout>
</template>
