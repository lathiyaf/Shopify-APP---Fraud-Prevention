import Vue from 'vue';
import VueRouter from 'vue-router';
Vue.use(VueRouter);

const routes = [
    {
        path:'/',
        component: require('../components/pages/Dashboard').default,
        name:'dashboard',
        meta: {
            title: 'Dashboard',
            ignoreInMenu: 0,
            displayRight: 0,
            dafaultActiveClass: '',
        },
    },

    {
        path:'/order-listing',
        component: require('../components/pages/OrderListing').default,
        name:'order-listing',
        meta: {
            title: 'Order Listing',
            ignoreInMenu: 0,
            displayRight: 0,
            dafaultActiveClass: '',
        },
    },
    {
        path:'/order-details/:order_id',
        component: require('../components/pages/OrderDetails').default,
        name:'order-details',
        meta: {
            title: 'Order Details',
            ignoreInMenu: 0,
            displayRight: 0,
            dafaultActiveClass: '',
        },
    },
    {
        path:'/settings',
        component: require('../components/pages/Setting').default,
        name:'settings',
        meta: {
            title: 'Settings',
            ignoreInMenu: 0,
            displayRight: 0,
            dafaultActiveClass: '',
        },
    },
    {
        path:'/plan',
        component: require('../components/pages/Plan').default,
        name:'plan',
        meta: {
            title: 'Plan & Pricing',
            ignoreInMenu: 0,
            displayRight: 0,
            dafaultActiveClass: '',
        },
    },
    {
        path:'/billing',
        component: require('../components/pages/Billing').default,
        name:'billing',
        meta: {
            title: 'Billing',
            ignoreInMenu: 0,
            displayRight: 0,
            dafaultActiveClass: '',
        },
    },
    {
        path:'/threat-intelligence-listing',
        component: require('../components/pages/ThreatIntelligence').default,
        name:'threat-intelligence-listing',
        meta: {
            title: 'Threat Intelligence',
            ignoreInMenu: 0,
            displayRight: 0,
            dafaultActiveClass: '',
        },
    },
];


// This callback runs before every route change, including on page load.


const router = new VueRouter({
    mode:'history',
    routes,
    scrollBehavior() {
        return {
            x: 0,
            y: 0,
        };
    },

});
export default router;
