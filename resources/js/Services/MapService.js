import Api from './Api'
import * as turf from '@turf/turf'

export default {
    
    getBounds(calles) {
        return Api.post('/bounds', {"calles" : calles});
    },
    
    
    ordenarPuntosAntihorario(puntos) {
        if (!Array.isArray(puntos) || puntos.length < 3) {
            throw new Error("Se necesitan al menos 3 puntos para formar un polígono");
        }
        
        // Convertir a formato [lng, lat]
        const coords = puntos.map(p => [p.lng, p.lat]);
        
        // Crear un FeatureCollection para usar turf
        const fc = turf.featureCollection(coords.map(c => turf.point(c)));
        
        // Calcular centroide para ordenar alrededor de él
        const centroide = turf.centroid(fc).geometry.coordinates;
        
        // Ordenar por ángulo polar respecto al centroide
        const ordenados = coords.sort((a, b) => {
            const angA = Math.atan2(a[1] - centroide[1], a[0] - centroide[0]);
            const angB = Math.atan2(b[1] - centroide[1], b[0] - centroide[0]);
            return angA - angB;
        });
        
        // Cerrar el polígono repitiendo el primer punto
        ordenados.push(ordenados[0]);
        
        return ordenados;
    }
}