import Api from './Api'

export default {
    changeStatus(ruta, options) {
        return Api.post(ruta, options);
    },

    addPosts(ruta, options){
        return Api.post(ruta, options);
    }
}