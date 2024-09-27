<script setup>
import { ref } from 'vue';

const estaVariable = ref('https://via.placeholder.com/800x400.png') ;

</script>
<style type="text/css">


.no-gutter.row,
.no-gutter.container,
.no-gutter.container-fluid{
    margin-left: 0;
    margin-right: 0;
}

.no-gutter>[class^="col-"]{
    padding-left: 0;
    padding-right: 0;
}
.title {
    font-size: 25px;
    padding-top: 20px;
}
.my-img {
    margin: 0;
    padding: 0;
}

</style>

<template>
    <div class="row no-gutter">
        <div class="col-12 col-lg-8 slide d-none d-lg-block">


            <img :src="'../../assets/img/1.webp'" class="w-100 h-100" alt="Imagen completa" >

        </div>
        <div class="col-12 col-lg-4 d-flex flex-column min-vh-100 justify-content-center align-items-center">
            <div>
                <slot name="logo"/>
            </div>
            <div class="w-full sm:max-w-md mt-6 px-6 py-4   overflow-hidden ">
                <slot></slot>
            </div>
        </div>
    </div>

    <!-- <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
        <div>
            <slot name="logo" />
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
            <slot />
        </div>
    </div> -->



</template>
