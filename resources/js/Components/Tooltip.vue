<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    list: Array, // Asegúrate de que 'list' sea un arreglo
});
</script>

<template>
    <div class="tooltip-container">
        <span class="tooltip-trigger"><slot /></span>
        <div class="tooltip-content" v-if="props.list.length > 0"> <!-- Mostrar solo si la lista no está vacía -->
            <ul>
                <li v-for="item in props.list" :key="item.name">{{ item.name }}</li>
            </ul>
        </div>
    </div>
</template>

<style>
.tooltip-container {
    position: relative;
    display: inline-block;
}
.tooltip-content {
    display: none;
    position: absolute;
    top: 100%; /* Mueve el tooltip debajo del botón */
    left: 50%;
    transform: translateX(-50%);
    background-color: rgba(0, 0, 0, 0.9); /* Fondo negro con opacidad */
    border-radius: 6px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    padding: 10px;
    width: auto; /* Permite que el tooltip se ajuste al contenido */
}
.tooltip-content ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}
.tooltip-content li {
    padding: 0 10px; /* Espaciado vertical entre cada ítem */
    white-space: nowrap; /* Evita que el texto se divida en varias líneas */
}
.tooltip-container:hover .tooltip-content {
    display: block;
}
</style>
