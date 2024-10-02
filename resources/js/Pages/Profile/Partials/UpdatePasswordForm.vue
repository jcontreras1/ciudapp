<script setup>
import { ref, defineProps } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import SectionTitle from '@/Components/SectionTitle.vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const props = defineProps({
    user: Object,
});

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('user-password.update'), {
        errorBag: 'updatePassword',

        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }

            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>

    <h3>Actualizar contraseña</h3>
    <p>
        Asegúrese de que su cuenta utilice una contraseña larga y aleatoria para mantenerse segura.
    </p>

    <FormSection @submitted="updatePassword">

        <template #form>
            <div class="col-span-6 sm:col-span-6">
                <i class="fas fa-lock-open mr-2"></i>
                <InputLabel for="current_password" value="Contraseña actual" />
                <TextInput
                id="current_password"
                ref="currentPasswordInput"
                v-model="form.current_password"
                type="password"
                class="mt-1 block w-full"
                autocomplete="current-password"
                />
                <InputError :message="form.errors.current_password" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-6">
                <i class="fas fa-lock mr-2"></i>
                <InputLabel for="password" value="Nueva contraseña" />
                <TextInput
                id="password"
                ref="passwordInput"
                v-model="form.password"
                type="password"
                class="mt-1 block w-full"
                autocomplete="new-password"
                />
                <InputError :message="form.errors.password" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-6">
                <i class="fas fa-lock mr-2"></i>
                <InputLabel for="password_confirmation" value="Confirmar Contraseña" />
                <TextInput
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                class="mt-1 block w-full"
                autocomplete="new-password"
                />
                <InputError :message="form.errors.password_confirmation" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="me-3">
                Guardar
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Guardar
            </PrimaryButton>
        </template>
    </FormSection>
</template>
