<script setup>
import { computed } from 'vue';

const emit = defineEmits(['update:checked']);
const superRandomId = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
const props = defineProps({
    checked: {
        type: [Array, Boolean],
        default: false,
    },
    value: {
        type: String,
        default: 'Seleccionar',
    },
    name: {
        type: String,
        default: null,
    },
});

const proxyChecked = computed({
    get() {
        return props.checked;
    },
    
    set(val) {
        emit('update:checked', val);
    },
});
</script>


<template>
    <div class="form-check form-switch">
        <input 
        class="form-check-input" 
        v-model="proxyChecked" 
        type="checkbox" 
        role="switch"   
        :value="value"
        :id="superRandomId"
        >
        <label class="form-check-label" :for="superRandomId">{{ name }}</label>
    </div>
</template>
