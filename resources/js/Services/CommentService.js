import Api from './Api';

export default {
    createComment(ruta, options){
        return Api.post(ruta, options);
    },
    createLink(ruta){
        return Api.post(ruta);
    },
    destroyLikeComment(ruta,options){
        return Api.delete(ruta);
    }
}