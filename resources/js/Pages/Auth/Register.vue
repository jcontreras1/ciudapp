<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
// import input from '@/Components/input.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
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
    <Head title="Register" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <form @submit.prevent="submit">
            <!-- Nombre -->
            <div class="mb-3">
                <label for="name" class="form-label"><i class="fas fa-user"></i></label>
                <input
                id="name"
                v-model="form.name"
                type="text"
                class="form-control custom-input"
                required
                autofocus
                placeholder="Nombre"
                autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

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


            <!-- Clave -->
            <div class="mb-3">
                <label for="password_confirmation" class="form-label"><i class="fas fa-key"></i></label>
                <input
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                class="form-control custom-input"
                required
                placeholder="Confirmar clave"
                autocomplete="current-password"
                />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="mt-4">
                <InputLabel for="terms">
                    <div class="flex items-center">
                        <Checkbox id="terms" v-model:checked="form.terms" name="terms" required />

                        <div class="ms-2">
                            I agree to the <a target="_blank" :href="route('terms.show')" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">Terms of Service</a> and <a target="_blank" :href="route('policy.show')" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">Privacy Policy</a>
                        </div>
                    </div>
                    <InputError class="mt-2" :message="form.errors.terms" />
                </InputLabel>
            </div>

            <!-- Olvide mi clave -->
            <div class="flex items-center justify-center mb-3">
                <Link :href="route('login')" class="link">
                    Ya tengo cuenta
                </Link>
            </div>


            <PrimaryButton :class="{ 'disabled': form.processing }" :disabled="form.processing">
                REGISTRAR
            </PrimaryButton>
        </form>
    </AuthenticationCard>
</template>
