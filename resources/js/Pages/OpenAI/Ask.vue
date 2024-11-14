<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayoutHome from '@/Layouts/AppLayoutHome.vue';
import { onMounted, ref, watch } from 'vue';
import OpenAIService from '@/Services/OpenAIService';

const props = defineProps({
    
});

const text = ref('');
const consultando = ref(false);
const messages = ref([]);


const submit = () => {
    consultando.value = true;
    messages.value.push({ text: text.value, sender: 'user' });
    const userMessage = text.value;
    text.value = '';
    OpenAIService.ask(userMessage)
    .then(response => {
        messages.value.push({ text: response.data.response, query: response.data.query, sender: 'ai' });
        consultando.value = false;
    })
    .catch(error => {
        console.error(error);
        consultando.value = false;
    });
}

</script>

<template>
    <AppLayoutHome>
        <Head title="Inicio"></Head>
        
        <div class="chat-container">
            <h2 class="">Preguntas a la IA</h2>
            
            <div class="row">
                <div class="col-12 mb-3">                    
                    <form id="chat-form" @submit.prevent="submit" class="mb-3">
                        <div class="mb-3">
                            <textarea class="form-control" id="user-input" rows="3" v-model="text" placeholder="Escribe tu mensaje..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-success" :disabled="!text.length || consultando">
                            <i class="fas fa-cog fa-spin" v-if="consultando"></i>
                            <span v-else>Enviar</span>
                        </button>
                    </form>
                </div>
                <div class="col-12">
                    
                    <div ref="chatbox" class="chat-box" id="chat-box">
                        <!-- Mostrar los mensajes -->
                        <div class="col-12">
                            
                            <div  class="card mb-2" v-for="(message, index) in messages" :key="index">
                                <div class="card-body" :class="{'d-flex align-items-end flex-column' : message.sender === 'user'}">
                                    <div class="badge bg-success mb-2">
                                        <span v-if="message.sender === 'user'">Tú</span>
                                        <span v-else>IA</span>
                                    </div>
                                    <div >{{ message.text }}</div>
                                </div>
                                <div class="card-footer" v-if="message.sender === 'ai'">
                                    {{ message.query }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Mensajes de chat irán aquí -->
        </div>
        
        
        
    </AppLayoutHome>
</template>


<style>
.chat-box {
    max-height: 60vh; /* o cualquier altura que necesites */
    overflow-y: auto;
    padding-right: 10px; /* Para espacio entre los mensajes y el borde */
    display: flex;
    flex-direction: column-reverse; /* Esta es la clave para que los mensajes más recientes estén al final */
}
</style>