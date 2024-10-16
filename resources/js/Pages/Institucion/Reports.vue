<script setup>

    import { defineProps, ref, onMounted } from 'vue';

    const props = defineProps({
        institution: {
            type: Object,
            required: true
        },
    });

    //Para agregarle independencia al módulo va y busca las subcategorías por sí solo, de modo que solo necesita la institución
     const allSubcategories = ref();

    const reportes = ref(null);
    const options = ref({
        regiones : [],
        subcategories : []
    });

    const toggleCat = (id) => {
        if (options.value.subcategories.includes(id)) {
            options.value.subcategories = options.value.subcategories.filter(subcat => subcat !== id);
        } else {
            options.value.subcategories.push(id);
        }
        getReports();

    }
    const getReports = () => {
        axios.get(`/api/institution/${props.institution.id}/reports`, { params :  options.value})
        .then(response => {
            reportes.value = response.data;
        }).catch(error => {
            console.error(error);
        });
        
    }

    const getSubcategories = () => {
        axios.get(`/api/institution/${props.institution.id}/subcategories`)
        .then(response => {
            allSubcategories.value = response.data;
        }).catch(error => {
            console.error(error);
        });
    }
    onMounted(() => {
        getSubcategories();
        getReports();
    });

</script>

<template>
    Lista de reportes:
    <div class="row">
        <div class="col-12">
            <button v-for="subcategory in allSubcategories" class="btn mx-1" 
            :class="{
                'btn-outline-success' : options.subcategories.includes(subcategory.id),
                'btn-outline-secondary' : !options.subcategories.includes(subcategory.id)

            }" 
            @click="toggleCat(subcategory.id)">
                {{ subcategory.name }} <i class="fas fa-check" v-if="options.subcategories.includes(subcategory.id)"></i>
            </button>
        </div>
    </div>
    <hr>
    <pre>
        {{ reportes }}
    </pre>
   
</template>