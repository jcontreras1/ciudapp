<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<style scoped>
.custom-input {
    background-color:  #fff; /* Hace que el fondo del input sea transparente */
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
    <Head title="Forgot Password" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            <!-- Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one. -->
            ¿Olvidaste tu contraseña? No hay problema. Solo dinos tu dirección de correo electrónico y te enviaremos un enlace de restablecimiento
            de contraseña por correo electrónico que te permitirá elegir una nueva.
        </div>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
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

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Enviar email para restablecer contraseña
            </PrimaryButton>

        </form>
    </AuthenticationCard>
</template>
