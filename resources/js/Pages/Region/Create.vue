<script setup>
import { useForm, Head } from '@inertiajs/vue3';
import { ref, defineProps } from 'vue'
import CreatePolygon from '@/Pages/Mapa/CreatePolygon.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import SectionTitle from '@/Components/SectionTitle.vue';

const props = defineProps({
    institution : {
        type: Object,
        required: true
    }
});

const fomrulario = useForm({
    'name' : '', //este hay que llenarlo en el formu
    'puntos' : [], //Este es el arreglo de todas las coordenadas que hacen el polígono. Tienen que estar en formato array así como lo dejo abajo:
});

const submitForm = () => {
    if(fomrulario.puntos.length < 3){
        Swal.fire({
            title: 'Error',
            text: 'Debe seleccionar al menos 3 puntos para formar un polígono',
            icon: 'error',
            confirmButtonText: 'Aceptar'
        });
        return;
    }
    fomrulario.post(route('region.store', props.institution))
}

</script>

<template>

    <AppLayout>
        <Head title="Definir región" />
        <SectionTitle>
            <template #title>
                Nueva region para <strong>{{ institution.name }}</strong>
            </template>

        </SectionTitle>
        <hr>

        <form @submit.prevent="submitForm" class="mb-2">
            <div class="mb-3">
                <label for="name" class="form-label">Ingresar un nombre para la región y dibujar el polígono correspondiente en el mapa.</label>
                <input type="text" class="form-control" id="name" v-model="fomrulario.name" required>
            </div>
            <div class="">
                <CreatePolygon v-on:puntos="(puntos) => fomrulario.puntos = puntos" ></CreatePolygon>
            </div>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </form>


    </AppLayout>

</template>
