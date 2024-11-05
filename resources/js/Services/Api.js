import axios from 'axios'

// export default() => {
// 	return axios.create({
// 		baseURL: '/api',
// 		headers: {
// 			'Accept': 'application/json',
// 			'Content-Type': 'application/json'
// 		},
// 	});
// }

export default {
    get(url, options = null) {
        return axios.get('/api' + url, options)
            .catch(error => {
                let errors = null;
                if (error.response.data.errors) {
                    errors = '';
                    for (let aux in error.response.data.errors) {
                        errors += error.response.data.errors[aux] + '<br>';
                    }
                }
                if (error.response) {
                    Swal.fire({
                        allowEnterKey: true,
                        focusConfirm: true,
                        icon: 'error',
                        title: `Error ${error.response.status}`,
                        html: errors ?? (error.response.data),
                    });
                }
            });
    },

    post(url, options = null) {
        return axios.post('/api' + url, options)
            .catch(error => {
                let errors = null;
                if (error.response.data.errors) {
                    errors = '';
                    for (let aux in error.response.data.errors) {
                        errors += error.response.data.errors[aux] + '<br>';
                    }
                }
                if (error.response) {
                    Swal.fire({
                        allowEnterKey: true,
                        focusConfirm: true,
                        icon: 'error',
                        title: `Error ${error.response.status}`,
                        html: errors ?? error.response.data,
                    });
                }
            });
    },

    put(url, options = null) {
        return axios.put('/api' + url, options)
            .catch(error => {
                let errors = null;
                if (error.response.data.errors) {
                    errors = '';
                    for (let aux in error.response.data.errors) {
                        errors += error.response.data.errors[aux] + '<br>';
                    }
                }
                if (error.response) {
                    Swal.fire({
                        allowEnterKey: true,
                        focusConfirm: true,
                        icon: 'error',
                        title: `Error ${error.response.status}`,
                        html: errors ?? error.response.data,
                    });
                }
            });
    },

    delete(url, options = null) {
        return axios.delete('/api' + url, options)
            .catch(error => {
                let errors = null;
                if (error.response.data.errors) {
                    errors = '';
                    for (let aux in error.response.data.errors) {
                        errors += error.response.data.errors[aux] + '<br>';
                    }
                }
                if (error.response) {
                    Swal.fire({
                        allowEnterKey: true,
                        focusConfirm: true,
                        icon: 'error',
                        title: `Error ${error.response.status}`,
                        html: errors ?? error.response.data,
                        
                    });
                }
            });
    }
}