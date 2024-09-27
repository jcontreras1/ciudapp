<script setup>
import { ref, defineProps} from 'vue'


const props = defineProps({
    instituciones: {
        type: Object,
    
    }
})
</script>

<template>
    <div class="fw-light">
        <div class="display-1">Instituciones</div>
    </div>
<hr>
<a :href="route('institution.create')">crear una</a>
<hr>

    <div v-for="institucion in instituciones?.data" :key="institucion.id">
        {{ institucion.name }}  ------>      <a :href="route('institution.edit', institucion)">editar</a>
    </div>
</template>