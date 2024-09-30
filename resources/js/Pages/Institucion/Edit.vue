<script setup>
import { useForm } from '@inertiajs/vue3';
import { Modal } from 'bootstrap';
import { ref, defineProps } from 'vue'

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
const agregarUsuario = () =>{
    newUser.post(route('userInstitution.store', props.institucion), {preserveScroll: true, onSuccess: () => {
        newUser.name = '';
        newUser.email = '';
        newUser.is_admin = 0;
        Modal.getInstance(modalCreate.value)?.hide();
    }});
}

</script>

<template>
    
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
                        <button type="submit" class="btn btn-success" :disabled="newUser.processing"><i class="fas fa-user-plus"></i> Agregar</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    
    
    <div class="display-2">Editar {{ institucion.name }}</div>
    <hr>
    
    <br>
    <br>
    
    <hr>
    <h3>Lista de regiones:</h3>
    <br>
    <a :href="route('region.create', institucion)">Crear una región, o polígono o lo que sea</a>
    <div v-for="region in regiones" :key="region.id">
        #{{ region.id }} --- {{ region.name }}  ------>      <a :href="route('region.edit', {'institution' : institucion, 'region' : region})">editar</a>
    </div>
    <br>
    <br>
    <br>
    <br>
    <hr>
    <h3>
        Lista de usuarios asociados a  esta institución
        <span class="float-end">
            <button data-bs-target="#mdlCreateUserInstitution" data-bs-toggle="modal" href="#" class="btn btn-success"><i class="fas fa-plus"></i> agregar usuario</button>
        </span>
    </h3>
    <table class="table">
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
                <td>
                    <a href="#" class="btn btn-danger" @click="eliminarUsuario(user)">Eliminar</a>
                </td>
            </tr>
        </tbody>
    </table>
    <!-- {{ users }} -->
    
</template>