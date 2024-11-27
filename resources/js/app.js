import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';

// Import shared components
import SearchFilter from '@/Shared/SearchFilter.vue';
import Pagination from '@/Shared/Pagination.vue';
import FileInput from '@/Shared/FileInput.vue';
import Icon from '@/Shared/Icon.vue';
import Dropdown from '@/Shared/Dropdown.vue';

import { library } from '@fortawesome/fontawesome-svg-core'
import { fas } from '@fortawesome/free-solid-svg-icons'
import { far } from '@fortawesome/free-regular-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css';
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import VueSweetalert2 from 'vue-sweetalert2';
import { Head, Link } from '@inertiajs/inertia-vue3'
// If you don't need the styles, do not connect
import 'sweetalert2/dist/sweetalert2.min.css';
import Toaster from "@meforma/vue-toaster";

import numeral from 'numeral';

library.add(fas)
library.add(far)

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'YoPractice';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, app, props, plugin }) {
        const vueApp = createApp({ render: () => h(app, props) });
        
        // Register global components
        vueApp.component('SearchFilter', SearchFilter);
        vueApp.component('Pagination', Pagination);
        vueApp.component('FileInput', FileInput);
        vueApp.component('Icon', Icon);
        vueApp.component('Dropdown', Dropdown);
        vueApp.component('font-awesome-icon', FontAwesomeIcon)
        vueApp.component('Multiselect', Multiselect)
        vueApp.component('flat-pickr', flatPickr)
        vueApp.component('InertiaHead', Head)
        vueApp.component('InertiaLink', Link)
        
        vueApp.use(plugin)
            .use(ZiggyVue)
            .use(VueSweetalert2);
        vueApp.use(Toaster);
        vueApp.mixin({
            methods: {
                moment: function (date = '') {
                    return moment(date);
                },
                can: function (permission) {
                    return this.$page.props.user.can.includes(permission);
                },
                hasAnyPermission: function (permissions) {

                    var allPermissions = this.$page.props.user.can;
                    var hasPermission = false;
                    permissions.forEach(function (item) {
                        if (allPermissions[item]) hasPermission = true;
                    });
                    return hasPermission;
                },
            }
        })
        vueApp.config.globalProperties.$filters = {
            currency(value, currency = null, decimals = 2) {
                if (!currency) {
                    return new Intl.NumberFormat('en-IN', {
                        maximumSignificantDigits: 20,
                        minimumFractionDigits: decimals,
                        maximumFractionDigits: decimals,
                    }).format(value)
                }

            },
            formatNumber(value, decimals = 2) {
                if (!decimals) {
                    return numeral(value).format('0,0.00')
                }
                return numeral(value).format('0,0.00')
            },
            timeAgo(date) {
                return moment(date).fromNow()
            },
            date(date, format = "YYYY-MM-DD") {
                return moment(date).format(format)
            },
            time(time, format = "YYYY-MM-DD HH:mm") {
                return moment(time).format(format)
            },
        }
        vueApp.mount(el);
        return vueApp;
    },
});

InertiaProgress.init({ color: '#4B5563' });
