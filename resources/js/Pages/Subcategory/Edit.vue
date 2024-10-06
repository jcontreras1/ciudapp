<script setup>
import { reactive, ref, computed } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SectionTitle from '@/Components/SectionTitle.vue';

const props = defineProps({
    category: {
        type: Object,
        required: true,
    },
    subcategory: {
        type: Object,
        required: true,
    },
    errors: {
        type: Object,
    },
});

// Reactive references for time inputs
const dias = ref(0);
const horas = ref(0);
const minutos = ref(0);
const errorMessage = ref(0);

// Compute the total minutes from the subcategory
const totalMinutes = computed(() => {
    return 0 + dias.value * 24 * 60 + horas.value * 60 + minutos.value; 
});

// Initialize form with existing subcategory data
const form = useForm({
    name: props.subcategory.name,
    icon: props.subcategory.icon,
    relevance_minutes: totalMinutes
});

// Calculate days, hours, and minutes based on total minutes
const calcularTiempo = () => {
    const total = props.subcategory.relevance_minutes || 0; // Adjust this to the actual key for minutes
    dias.value = Math.floor(total / 1440); // 1 día = 1440 minutos
    horas.value = Math.floor((total % 1440) / 60); // 1 hora = 60 minutos
    minutos.value = total % 60; // Restante en minutos
};

// Call the function to calculate time on component setup
calcularTiempo();


const validarHoras = () => {
    if (horas.value < 0 || horas.value > 23) {
        errorMessage.value = 'Las horas deben estar entre 0 y 23.';
        horas.value = horas.value < 0 ? 0 : 23; // Ajustar el valor
    } else {
        errorMessage.value = '';
    }
}
const validarMinutos = () => {
    if (minutos.value < 0 || minutos.value > 59) {
        errorMessage.value = 'Los minutos deben estar entre 0 y 59.';
        minutos.value = minutos.value < 0 ? 0 : 59; // Ajustar el valor
    } else {
        errorMessage.value = '';
    }
}

</script>

<template>
    <AppLayout>        
        <Head title="Editar subcategoría" />
        <div class="container my-4">
            <SectionTitle :errors="errors">
                <template #title>
                    Editar subcategoría <strong>{{ subcategory.name }}</strong>
                </template>
            </SectionTitle>
            <hr class="mb-3">
            <form @submit.prevent="form.put(route('subcategory.update', {'category' : category.id, 'subcategory' : subcategory.id}))">
                <div class="mb-3">
                    <label for="name" class="form-label">Nombre</label>
                    <input type="text" autocomplete="off" required class="form-control" :class="{'is-invalid' : errors.name}" id="name" autofocus v-model="form.name">
                    <div class="text-danger">{{ errors.name }}</div>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Ícono <a href="https://fontawesome.com/v5/search?o=r&m=free" target="_blank"><i class="fas fa-external-link-alt"></i></a></label>
                    <input class="form-control" id="description" v-model="form.icon" :class="{'is-invalid' : errors.icon}">
                    <div class="text-danger">{{ errors.icon }}</div>
                    <span class="fs-1" v-html="form.icon"></span>
                </div>
                <div class="row mb-1">
                    <div class="col">
                        <span class="lead">
                            Tiempo que debería durar un reporte de esta subcategoría <em>(Opcional)</em>
                        </span>
                        <div v-if="errorMessage" class="alert alert-danger mt-1">{{ errorMessage }}</div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12 col-md-4">
                        <label for="dias" class="form-label">Días</label>
                        <input min="0" type="number" class="form-control" v-model="dias" id="dias" placeholder="Cantidad de días">
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="horas" class="form-label">Horas</label>
                        <input min="0" max="23" type="number" class="form-control" v-model="horas" id="horas" placeholder="Cantidad de horas" @input="validarHoras">
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="minutos" class="form-label">Minutos</label>
                        <input min="0" max="59" type="number" class="form-control" v-model="minutos" id="minutos" placeholder="Cantidad de minutos" @input="validarMinutos">
                    </div>
                </div>
                <button type="submit" :disabled="form.processing" class="btn btn-primary">{{ form.processing ? 'Guardando...' : 'Guardar' }}</button>
            </form>
            
        </div>
    </AppLayout>
</template>
