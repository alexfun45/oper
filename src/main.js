import Vue from 'vue'
//import VueSidebarMenu from 'vue-sidebar-menu'
import VueSidebarMenu from './index'
import Сounteragent from './components/Сounteragent.vue'
import Organizations from './components/Organizations.vue'
import Objects from './components/Objects.vue'
import '../dist/vue-sidebar-menu.css'
import App from './App.vue'
Vue.use(VueSidebarMenu)
import Router from 'vue-router'
Vue.use(Router)
import axios from 'axios'
import VueAxios from 'vue-axios'
Vue.use(VueAxios, axios)
window.axios = require('axios');
import VueBus from 'vue-bus'
Vue.use(VueBus)
import { library } from '@fortawesome/fontawesome-svg-core'
import { faUserSecret } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
library.add(faUserSecret)
Vue.component('font-awesome-icon', FontAwesomeIcon)
const Payments = { template: '<div>Payments Page</div>' }
const Services = { template: '<div>Services Page</div>' }
const Handbook = { template: '<div>handbook <router-view/></div>' }
const Documents = { template: '<div>Documents Page</div>' }
//const Сounteragent = { template: '<div>Сounteragent Page</div>' }
const Organization = { template: '<div>Organization Page</div>' }
//const Objects = { template: '<div>Objects Page</div>' }

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
    },
	  {
      path: '/handbook',
      name: 'handbook',
      component: Handbook,
      children: [
        {
          path: '/handbook/documents',
          name: 'Documents',
          component: Documents,
        },
        {
          path: '/handbook/counteragents',
          name: 'Сounteragent',
          component: Сounteragent,
        },
        {
          path: '/handbook/organizations',
          name: 'Organizations',
          component: Organizations,
        },
        {
          path: '/handbook/objects',
          name: 'Objects',
          component: Objects,
        }
      ]
    }    
    ]
   });
   

new Vue({
  el: '#app',
  router: router,
  data() {
  	return {
  		api: "./src/api.php"
  			}
  	},
  methods:{
		
  },
  render: h => h(App)
})
