
<script setup>
import { ref } from 'vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import axios from 'axios'
import AppLayout from '@/Layouts/AppLayout.vue'
import SectionTitle from '@/Components/SectionTitle.vue';

const form = useForm({
    name: '',
    mail: '',
    address: '',
    city_id: '',
    city_name: '',
})

const cities = ref([])
const searchCity = async (query) => {
    if (query.length > 2) {
        const response = await axios.get(`/api/cities?query=${query}`)
        cities.value = response.data
    }
}

const selectCity = (city) => {
    form.city_id = city.id
    // Si deseas limpiar el input después de seleccionar, puedes usar esto:
    form.city_name = city.name + ' - ' + city.province?.name // O asignar el nombre de la ciudad al input visible si lo deseas
    cities.value = [] // Limpiar la lista de ciudades
}
</script>

<template>
    <AppLayout>
        <Head title="Crear Institución" />
        <SectionTitle>
            <template #title>
                Crear Institución
            </template>
        </SectionTitle>
        <hr>

        <form @submit.prevent="form.post('/institution')">
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label"><b>Nombre</b></label>
                        <input v-model="form.name" type="text" class="form-control" id="name" required>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="mb-3">
                        <label for="mail" class="form-label"><b>Correo</b></label>
                        <input v-model="form.mail" type="email" class="form-control" id="mail">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label"><b>Dirección</b></label>
                    <input v-model="form.address" type="text" class="form-control" id="address">
                </div>
                <div class="mb-3">
                    <label for="city_id" class="form-label"><b>Ciudad</b></label>
                    <input type="hidden" v-model="form.city_id" /> <!-- Campo oculto para el city_id -->
                    <input
                    type="text"
                    class="form-control"
                    autocomplete="nope"
                    id="city_id"
                    @input="searchCity($event.target.value)"
                    required
                    v-model="form.city_name"
                    >
                    <ul v-if="cities.length" class="list-group">
                        <li
                        role="button"
                        v-for="city in cities"
                        :key="city.id"
                        class="list-group-item"
                        @click="selectCity(city)"
                        >
                        {{ city.name }} - {{ city.province.name }}
                    </li>
                </ul>
            </div>
        </div>
        <div class="text-end">
        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Guardar</button>
        </div>
    </form>

</AppLayout>
</template>
