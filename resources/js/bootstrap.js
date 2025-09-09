/**
 * Bootstrap - Axios Defaults
 * 
 * Configure global axios instance for AJAX requests.
 * 
 * Author: SlowWebDev
 */
import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
