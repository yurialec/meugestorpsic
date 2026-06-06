import 'bootstrap';
import axios from 'axios';
import $ from 'jquery';

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.$ = window.jQuery = $;
window.axios = axios;

let base;

if (window.location.hostname === "localhost") {
    base = "http://localhost:8000/";
} else {
    base = "https://meugestorsaude.com.br/";
}

axios.defaults.baseURL = base;
axios.defaults.headers.common['Content-Type'] = 'application/json';
axios.defaults.withCredentials = true;

const token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content || token.getAttribute('content');
} else {
    console.warn('CSRF token não encontrado.');
}

const tenant_domain = window.tenant_domain || null;

window.axiosTenant = axios.create({
    baseURL: `/${tenant_domain}/`,
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token?.content || token?.getAttribute('content'),
    },
    withCredentials: true,
});
