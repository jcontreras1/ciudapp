<script setup>
import {  ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import SectionTitle from '@/Components/SectionTitle.vue';
import mapboxgl from 'mapbox-gl';
import '@mapbox/mapbox-gl-draw/dist/mapbox-gl-draw.css';
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed } from 'vue';
import CreatePolygon from '@/Pages/Mapa/CreatePolygon.vue';
import MapService from '@/Services/MapService.js';

const form = useForm({
    calle1: '',
    calle2: '',
    calle3: '',
    calle4: ''
});

const puntos = ref([]);
const consultando = ref(false);

function submitForm() {
    consultando.value = true;
    MapService.getBounds({
        calle1: form.calle1,
        calle2: form.calle2,
        calle3: form.calle3,
        calle4: form.calle4
    }).then(response => {
        puntos.value = response.data.bounds;
    }).finally(() => {
        consultando.value = false;
    });
}

const formInvalid = computed(() => {
    return !form.calle1 || !form.calle2 || !form.calle3 || !form.calle4;
});
</script>

<template>
    <AppLayout>
        <Head title="Subcategorías" />
        <SectionTitle>
            <template #title>
                Buscar reportes entre 4 calles
            </template>
        </SectionTitle>
        <hr class="mb-3">

        <form @submit.prevent="submitForm">
            <div class="row">
                <div class="col-12 col-md-3 mb-2">
                    <label for="calle1">Calle 1:</label>
                    <input id="calle1" type="text" class="form-control form-control-sm" v-model="form.calle1" />
                </div>
                <div class="col-12 col-md-3 mb-2">
                    <label for="calle2">Calle 2:</label>
                    <input id="calle2" type="text" class="form-control form-control-sm" v-model="form.calle2" />
                </div>
                <div class="col-12 col-md-3 mb-2">
                    <label for="calle3">Calle 3:</label>
                    <input id="calle3" type="text" class="form-control form-control-sm" v-model="form.calle3" />
                </div>
                <div class="col-12 col-md-3 mb-2">
                    <label for="calle4">Calle 4:</label>
                    <input id="calle4" type="text" class="form-control form-control-sm" v-model="form.calle4" />
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3 mb-3" :class="{ 'btn-danger': formInvalid }" :disabled="formInvalid">
                <i class="fas fa-search" v-if="!consultando"></i>
                <span v-if="!consultando">Buscar</span>
                <i class="fas fa-spinner fa-spin" v-else></i>
            </button>
        </form>

        <CreatePolygon v-on:puntos="(puntos) => puntos = puntos" ></CreatePolygon>
    </AppLayout>
</template>
