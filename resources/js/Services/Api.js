import axios from 'axios'

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
                let html;
                if (error.response) {
                    if (typeof error.response.data === 'object' && error.response.data !== null && 'message' in error.response.data) {
                        html = errors ?? error.response.data.message;
                    } else {
                        html = errors ?? error.response.data;
                    }
                    Swal.fire({
                        allowEnterKey: true,
                        focusConfirm: true,
                        icon: 'error',
                        title: `Error ${error.response.status}`,
                        html: html,
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
                let html;
                if (error.response) {
                    if (typeof error.response.data === 'object' && error.response.data !== null && 'message' in error.response.data) {
                        html = errors ?? error.response.data.message;
                    } else {
                        html = errors ?? error.response.data;
                    }
                    Swal.fire({
                        allowEnterKey: true,
                        focusConfirm: true,
                        icon: 'error',
                        title: `Error ${error.response.status}`,
                        html: html,
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
                let html;
                if (error.response) {
                    if (typeof error.response.data === 'object' && error.response.data !== null && 'message' in error.response.data) {
                        html = errors ?? error.response.data.message;
                    } else {
                        html = errors ?? error.response.data;
                    }
                    Swal.fire({
                        allowEnterKey: true,
                        focusConfirm: true,
                        icon: 'error',
                        title: `Error ${error.response.status}`,
                        html: html,
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
                let html;
                if (error.response) {
                    if (typeof error.response.data === 'object' && error.response.data !== null && 'message' in error.response.data) {
                        html = errors ?? error.response.data.message;
                    } else {
                        html = errors ?? error.response.data;
                    }
                    Swal.fire({
                        allowEnterKey: true,
                        focusConfirm: true,
                        icon: 'error',
                        title: `Error ${error.response.status}`,
                        html: html,
                    });
                }
            });
    }
}