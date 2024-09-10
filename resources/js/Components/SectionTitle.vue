<script setup>
import { router } from '@inertiajs/vue3';
import SuccesAutocloseAlert from '@/Components/SuccessAutocloseAlert.vue';

const back = () => {
    window.history.back()
}
</script>
<template>
    <div class="md:col-span-1 flex justify-between">
        <div class="">
            <h3><slot name="title" /></h3>
            
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                <slot name="description" />
            </p>
        </div>
        
        <div class="">
            <!-- Todos los botones propios -->
            <slot name="aside" />
            <!-- Volver -->
            <button @click="back" class="btn btn-primary ml-1" title="Volver"><i class="fas fa-chevron-left"></i></button>
        </div>
    </div>
    
    <div v-if="$page.props.flash.message" class="alert alert-success alert-dismissible fade show mt-3 mb-3" role="alert">
        <i class="fas fa-check-circle"></i>
        {{ $page.props.flash.message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    
</template>
