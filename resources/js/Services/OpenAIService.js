import Api from './Api'

export default {
   
    ask(question){
        console.log(question)
        return Api.post('/ask', {'prompt': question});
    }
}