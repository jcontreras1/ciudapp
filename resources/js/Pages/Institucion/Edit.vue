<script setup>
import { ref, defineProps } from 'vue'


const props = defineProps({
    institucion : {
        type: Object,
        required: true
    }
})
</script>

<template>
       <div class="fw-light">
        <div class="display-1">EDITAR ESTA insTiTuCión.bue</div>
    </div>
    {{ institucion }}
</template>