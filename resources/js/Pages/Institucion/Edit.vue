<script setup>
import { useForm } from '@inertiajs/vue3';
import { Modal } from 'bootstrap';
import { ref, defineProps } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue'
import SectionTitle from '@/Components/SectionTitle.vue';

const props = defineProps({
    institucion : {
        type: Object,
        required: true
    },
    regiones : {
        type: Object,
        required: true
    },
    users : {
        type : Object,
        required : true
    }
});
const cities = ref([])
const searchCity = async (query) => {
    if (query.length > 2) {
        const response = await axios.get(`/api/cities?query=${query}`)
        cities.value = response.data
    }
}

const selectCity = (city) => {
    updateIntitutionForm.city_id = city.id
    // Si deseas limpiar el input después de seleccionar, puedes usar esto:
    updateIntitutionForm.city_name = city.name + ' - ' + city.province?.name // O asignar el nombre de la ciudad al input visible si lo deseas
    cities.value = [] // Limpiar la lista de ciudades
}

const modalCreate = ref(null);
const genericForm = useForm({is_admin : 0});
const toggleAdmin = (user) => {
    genericForm.is_admin = user.pivot.is_admin ? 0 : 1
    genericForm.put(route('userInstitution.update', {'institution' : props.institucion, 'userInstitution' : user.pivot.id}), {preserveScroll: true});
}
const newUser = useForm({
    name : '',
    email: '',
    is_admin: 0,
});

const eliminarUsuario = (user) => {
    Swal.fire({
        title: `¿Remover a ${user.name} de esta institución?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            genericForm.delete(route('userInstitution.destroy', {'institution' : props.institucion, 'userInstitution' : user.pivot.id}), {preserveScroll: true});
        }
    });
}

const eliminarRegion = (region) => {
    Swal.fire({
        title: `¿Eliminar región`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            genericForm.delete(route('region.destroy', {'institution' : props.institucion, 'region' : region.id}), {preserveScroll: true});
        }
    });
}

const agregarUsuario = () =>{
    newUser.post(route('userInstitution.store', props.institucion), {preserveScroll: true, onSuccess: () => {
        newUser.name = '';
        newUser.email = '';
        newUser.is_admin = 0;
        Modal.getInstance(modalCreate.value)?.hide();
    }});
}

const updateIntitutionForm = useForm({
    name: props.institucion.name,
    mail: props.institucion.mail,
    address: props.institucion.address,
    city_id: props.institucion.city_id,
    city_name: props.institucion.city.name + ' - ' + props.institucion.city?.province?.name,
});

</script>

<template>
    <AppLayout>
        <Head title="Editar Institución" />
        <SectionTitle>
            <template #title>
                Editar Institución
            </template>
        </SectionTitle>

        <!-- Modal create user - institution -->
        <form @submit.prevent="agregarUsuario">
            <div class="modal fade" id="mdlCreateUserInstitution" ref="modalCreate" tabindex="-1" aria-labelledby="mdlCreateUserInstitutionLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="mdlCreateUserInstitutionLabel">Agregar usuario a <strong>{{ institucion.name }}</strong></h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="username" class="form-label">Nombre de usuario</label>
                                <input type="text" autocomplete="nope" class="form-control" id="username" v-model="newUser.name">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo electrónico</label>
                                <input type="email" class="form-control" id="email" v-model="newUser.email">
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" v-model="newUser.is_admin" type="checkbox" role="switch" id="switchAdministraRegion">
                                <label class="form-check-label" for="switchAdministraRegion">¿Administra la Región?</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" :disabled="newUser.processing">Cerrar</button>
                            <button type="submit" class="btn btn-success" :disabled="newUser.processing">
                                <span v-if="newUser.processing">Notificando ususario... <i class="fas fa-spin fa-spinner"></i></span>
                                <span v-else><i class="fas fa-user-plus"></i>Guardar</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <hr>

        <div>
            <form @submit.prevent="updateIntitutionForm.put(route('institution.update', institucion), {preserveScroll: true})">
                <div class="row">
                    <div class="col-12 col-md-6 mb-3">
                        <label for="name" class="form-label"><b>Nombre de la institución</b></label>
                        <input type="text" class="form-control" id="name" v-model="updateIntitutionForm.name">
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label for="mail" class="form-label"><b>Correo electrónico</b></label>
                        <input type="email" class="form-control" id="mail" v-model="updateIntitutionForm.mail">
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label for="address" class="form-label"><b>Dirección</b></label>
                        <input type="text" class="form-control" id="address" v-model="updateIntitutionForm.address">
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label for="city_id" class="form-label"><b>Ciudad</b></label>
                        <input type="hidden" v-model="updateIntitutionForm.city_id" /> <!-- Campo oculto para el city_id -->
                        <input
                        type="text"
                        class="form-control"
                        autocomplete="nope"
                        id="city_id"
                        @input="searchCity($event.target.value)"
                        required
                        v-model="updateIntitutionForm.city_name"
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
    </div>


    <hr>
 <h3>Regiones definidas
    <a class="btn btn-success float-end" :href="route('region.create', institucion)" title="Crear región">
        <i class="fas fa-plus"></i>
    </a>
</h3>
<br>
<div class="row mb-4">
    <div class="col-12 col-md-4" v-for="region in regiones" :key="region.id" v-if="regiones.length > 0">
        <div class="card text-center h-100">
            <div class="card-body">
                <h4 class="mb-3">{{ region.name }}</h4>
                <div class="">
                    <a :href="route('region.edit', {'institution' : institucion, 'region' : region})" class="btn btn-primary mr-1">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button @click="eliminarRegion(region)" class="btn btn-danger">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-12" v-if="regiones.length == 0">
        <p class="fst-italic text-muted">No hay regiones definidas a esta institución.</p>
    </div>
</div>
    <hr>
    <h3>
        Lista de usuarios asociados a  esta institución
        <span class="float-end">
            <button data-bs-target="#mdlCreateUserInstitution" data-bs-toggle="modal" href="#" class="btn btn-success" title="Agregar usuario"><i class="fas fa-plus"></i> </button>
        </span>
    </h3>
    <br>
    <div class="col-12 col-md-12" v-if="users.length == 0">
         <p class="fst-italic text-muted">No hay usuarios asociados a esta institución.</p>

    </div>
    <table class="table table-sm" v-if="users.length > 0">
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Puede administrar</th>
                <th>Opciones</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="user in users" :key="user.id">
                <td>{{ user.name }} - {{ user.email }}</td>
                <td>
                    <div class="form-check form-switch">
                        <input @change="toggleAdmin(user)" class="form-check-input" :checked="user.pivot.is_admin" type="checkbox" role="switch" :id="'check' + user.pivot.id ">
                        <label class="form-check-label" :for="'check' + user.pivot.id">{{ user.pivot.is_admin ? 'Si' : 'No' }}</label>
                    </div>
                </td>
                <td class="text-end">
                    <a href="#" class="btn btn-danger" @click="eliminarUsuario(user)"><i class="fas fa-trash-alt"></i></a>
                </td>
            </tr>
        </tbody>
    </table>
</AppLayout>

</template>
