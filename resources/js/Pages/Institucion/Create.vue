
<script setup>
import { ref } from 'vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import axios from 'axios'

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
    <form @submit.prevent="form.post('/institution')">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input v-model="form.name" type="text" class="form-control" id="name" required>
        </div>
        <div class="mb-3">
            <label for="mail" class="form-label">Mail</label>
            <input v-model="form.mail" type="email" class="form-control" id="mail">
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <input v-model="form.address" type="text" class="form-control" id="address">
        </div>
        <div class="mb-3">
            <label for="city_id" class="form-label">City</label>
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
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</template>