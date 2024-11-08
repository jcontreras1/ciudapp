import Api from './Api'

export default {
   
    ask(question){
        return Api.post('/ask', {'question': question});
    }
}