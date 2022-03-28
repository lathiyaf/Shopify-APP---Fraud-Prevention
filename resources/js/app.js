require('./bootstrap');
window.Vue = require('vue');

require('jqvmap');
require('jqvmap/dist/maps/jquery.vmap.world.js');

import * as VueGoogleMaps from 'vue2-google-maps'
Vue.use(VueGoogleMaps, {
    load: {
        key: 'AIzaSyBVsQkiV6GdzZ6xYn-lnEbdyompYbS7kBg',
        libraries: 'places',
    },
    installComponents: true
})

import VTooltip from 'v-tooltip';
Vue.use(VTooltip)

import App from './components/layouts';
//import './components'
import router from './routes';
import helper from './helper';
const plugin = {
    install() {
        Vue.prototype.$helpers = helper
    }
}
Vue.use(plugin);
const app = new Vue({
    el: '#app',
    router,
    render: h => h(App),
});
