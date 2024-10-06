<script setup>
import { computed, ref  } from 'vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SectionTitle from '@/Components/SectionTitle.vue';

const props = defineProps({
    categoria:{
        type: Object,
        required: true,
    },
    errors: {
        type: Object,
    },
});

const dias = ref(0);
const horas = ref(0);
const minutos = ref(0);
const errorMessage = ref(0);

const minutosTotales = computed(() => {
    return 0 + dias.value * 24 * 60 + horas.value * 60 + minutos.value;
})

const form = useForm({
    name: '',
    icon: '',
    relevance_minutes : minutosTotales,
});

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
        <Head title="Nueva subcategoría" />
        <div class="container my-4">
            
            <SectionTitle :errors="errors">
                <template #title>
                    Crear subcategoría en <strong>{{ categoria.name }}</strong>
                </template>
            </SectionTitle>
            <hr class="mb-3">
            
            <form @submit.prevent="form.post(route('subcategory.store', categoria))">
                <div class="mb-3">
                    <label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" autocomplete="off" required class="form-control" :class="{'is-invalid' : errors.name}" id="name" autofocus v-model="form.name">
                    <div class="text-danger">{{ errors.name }}</div>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Ícono <span class="text-danger">*</span> <a href="https://fontawesome.com/v5/search?o=r&m=free" target="_blank"><i class="fas fa-external-link-alt"></i></a></label>
                    <input class="form-control" id="description" v-model="form.icon" :class="{'is-invalid' : errors.icon}">
                    <div class="text-danger">{{ errors.icon }}</div>
                    <span class="fs-1" v-html="form.icon"></span>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <span class="lead">
                            Tiempo que debería durar un reporte de esta subcategoría <em>(Opcional)</em>
                        </span>
                        <div v-if="errorMessage" class="alert alert-danger mt-3">{{ errorMessage }}</div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12 col-md-4">
                        <label for="dias" class="form-label">Días</label>
                        <input min="0" type="number" class="form-control" v-model="dias" id="dias" placeholder="Cantidad de días">
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="horas" class="form-label">Horas</label>
                        <input min="0" max="23" type="number" class="form-control" @input="validarHoras" v-model="horas" id="horas" placeholder="Cantidad de horas">
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="minutos" class="form-label">Minutos</label>
                        <input min="0" max="59" type="number" class="form-control" @input="validarMinutos" v-model="minutos" id="minutos" placeholder="Cantidad de minutos">
                    </div>
                </div>
                <button type="submit" :disabled="form.processing" class="btn btn-primary">{{ form.processing ? 'Guardando...' : 'Guardar' }}</button>
            </form>
            
        </div>
    </AppLayout>
</template>