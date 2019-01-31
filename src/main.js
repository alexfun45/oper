import Vue from 'vue'
//import VueSidebarMenu from 'vue-sidebar-menu'
import VueSidebarMenu from './index'
import '../dist/vue-sidebar-menu.css'
import App from './App.vue'
Vue.use(VueSidebarMenu)
import Router from 'vue-router'
Vue.use(Router)
const Payments = { template: '<div>Payments Page</div>' }
const Services = { template: '<div>Services Page</div>' }


const router = new Router({
  routes: [
    {
      path: '/',
      name: 'Payments',
      component: Payments,
    },
    {
      path: '/services',
      name: 'Services',
      component: Services,
    }]
   });
   

new Vue({
  el: '#app',
  router: router,
  render: h => h(App)
})
