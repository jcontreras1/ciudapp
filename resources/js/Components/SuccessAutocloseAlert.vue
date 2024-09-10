<script setup>
defineProps({
    visible:{
        type: Boolean,
    },
    message:{
        type: String,
    },    
});

const showAlert = (msg) => {
    message = msg;
    visible = true;
    
    setTimeout(() => {
        visible = false;
    }, 3000); // 3 segundos
}
const hideAlert = () => {
    visible = false;
}

</script>

<template>
    <div v-if="true" class="alert alert-success alert-dismissible fade show" role="alert">
        {{ message }}
        <button type="button" @click="hideAlert" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</template>