<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<style scoped>
.custom-input {
     /* Hace que el fondo del input sea transparente */
    border: none;
    border-bottom: 1px solid #000; /* Solo el borde inferior */
    border-radius: 0;
    box-shadow: none;
    color: #000; /* Color del texto */
}
.custom-input:focus {
    box-shadow: none;
    border-bottom-color: #000;
}
.custom-input:focus {
    box-shadow: none;
    border-bottom-color: #000;
    /* Cambia el color del texto al enfocar */
}
/* Cambia el color del placeholder a blanco */
.custom-input::placeholder {

  opacity: 1; /* Asegura que el color se vea claramente */
}

</style>

<template>
    <Head title="Log in"/>


    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div v-if="status">
            <div class="alert alert-warning">
                <div class="alert-body">
                    <strong>
                        <i class="fas fa-exclamation-circle me-1"></i>
                        {{ status }}
                    </strong>
                </div>
            </div>
            <hr>
        </div>

        <form @submit.prevent="submit">

            <!--Email -->
            <div class="mb-3">
                <label for="email" class="form-label"><i class="fas fa-envelope"></i></label>
                <input
                id="email"
                v-model="form.email"
                type="email"
                class="form-control custom-input"
                required
                autofocus
                placeholder="Correo electrónico"
                autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <!-- Clave -->
            <div class="mb-3">
                <label for="password" class="form-label"><i class="fas fa-key"></i></label>
                <input
                id="password"
                v-model="form.password"
                type="password"
                class="form-control custom-input"
                required
                placeholder="Clave"
                autocomplete="current-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <!-- Recordame -->
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault"  name="remember">
                <label class="form-check-label" for="flexSwitchCheckDefault">Recordarme</label>
            </div>

            <!-- Olvide mi clave -->
            <div class="flex items-center justify-center mb-3">
                <Link v-if="canResetPassword" :href="route('password.request')" >
                    Olvidé mi clave
                </Link>
            </div>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    INGRESAR
            </PrimaryButton>

        </form>
    </AuthenticationCard>
</template>
