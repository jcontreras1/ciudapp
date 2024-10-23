import Api from './Api'

export default {
    createPost(ruta,form,options){
        return Api.post(ruta,form, options);
    },
    
    likePost(ruta){
        return Api.post(ruta);
        
    },
    
    geocodingGet(ruta,options){
        return Api.get(ruta,options);
        
    },
    
}