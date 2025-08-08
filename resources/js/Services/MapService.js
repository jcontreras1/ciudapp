import Api from './Api'

export default {

    getBounds(options) {
        return Api.post('/bounds', options);
    }

}