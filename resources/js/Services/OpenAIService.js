import Api from './Api'

export default {
   
    ask(question){
        return Api.post('/ask', {'prompt': question});
    }
}