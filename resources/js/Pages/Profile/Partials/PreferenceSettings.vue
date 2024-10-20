<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import { computed, defineProps, ref } from 'vue';

const props = defineProps({
    preferences: Object,
    userPreferences: Object // Cambiado a Array
});

const allPreferences = ref(props.preferences);
const myPreferences = ref(props.userPreferences);

const updatePreference = (preference) => {
    //let value = myPreferences.value?.map(item => item.id).includes(preference.id);
    axios.post(`/api/user/preference/toggle`, {
        preference_id: preference.id
    }).then(response => {
        myPreferences.value = response.data;
    });
}

const pref = computed(() => {
    return myPreferences.value.map(item => ({
        'id' : item.id,
        'value' : item.pivot.value})
    ).filter(item => item.value === 0)
    .map(item => item.id);
});


</script>

<template>
    <div class="tab-pane" id="preference" role="tabpanel" aria-labelledby="preference-tab" tabindex="0">
        <div v-for="preference in allPreferences" :key="preference.id">
            <Checkbox 
                class="fs-4 mb-2" 
                @change="updatePreference(preference)" 
                :checked="!pref.includes(preference.id)" 
                :name="preference.description" 
            />
        </div>    
    </div>    
</template>
