import { get } from "@vueuse/core";
import Api from "./Api";

export default {
    getReport(ruta, options){
        return Api.get(ruta, options);
    },
    getSubCategory(ruta){
        return Api.get(ruta);
    }
}