<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';


defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
});
</script>

<template>
    
    
    
    
    
    <AppLayout title="Perfil">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#pepito" type="button" role="tab" aria-controls="home" aria-selected="true">Información</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Contraseña</button>
            </li>
            <!-- <li class="nav-item" role="presentation"> -->
                <!-- <button class="nav-link" id="messages-tab" data-bs-toggle="tab" data-bs-target="#messages" type="button" role="tab" aria-controls="messages" aria-selected="false">Autenticación</button> -->
            <!-- </li> -->
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button" role="tab" aria-controls="settings" aria-selected="false">Eliminar cuenta</button>
            </li>
        </ul>
        
        
        
        
        <div class="tab-content">
            <div class="py-2"></div>
            <div class="tab-pane active" id="pepito" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                <div v-if="true">
                    <UpdateProfileInformationForm :user="$page.props.auth.user" />
                    
                    <SectionBorder />
                </div>
            </div>
            <div class="tab-pane" id="profile" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                <div v-if="true">
                    <!-- <div v-if="$page.props.jetstream.canUpdatePassword"> -->
                        <UpdatePasswordForm />
                        
                        <SectionBorder />
                    </div>
                </div>
                <!-- <div class="tab-pane" id="messages" role="tabpanel" aria-labelledby="messages-tab" tabindex="0">
                        <div v-if="$page.props.jetstream.canManageTwoFactorAuthentication">
                            <TwoFactorAuthenticationForm
                            :requires-confirmation="confirmsTwoFactorAuthentication"
                            />
                            
                            <SectionBorder />
                        </div>
                    </div> -->
                    
                    <!-- <LogoutOtherBrowserSessionsForm :sessions="sessions" class="mt-10 sm:mt-0" /> -->
                    <div class="tab-pane" id="settings" role="tabpanel" aria-labelledby="settings-tab" tabindex="0">
                        <template v-if="true">
                            <!-- <template v-if="$page.props.jetstream.hasAccountDeletionFeatures"> -->
                                <DeleteUserForm />
                                <SectionBorder />
                            </template>
                        </div>
                    <!-- </div> -->
                </div>
            <!-- </div> -->
        </AppLayout>
    </template>
    