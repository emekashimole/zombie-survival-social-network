require('./bootstrap');

import Vue from 'vue'

Vue.component('survivors', require('./components/Survivors.vue').default);
Vue.component('navbar', require('./components/Navbar.vue').default);
Vue.component('add_survivors', require('./components/AddSurvivor.vue').default);
Vue.component('add_items', require('./components/AddItem.vue').default);
Vue.component('trade_items', require('./components/TradeItems.vue').default);
Vue.component('view_report', require('./components/ViewReport.vue').default);

const app = new Vue({
    el: '#app'
});