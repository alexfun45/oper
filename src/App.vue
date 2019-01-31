import { SidebarMenu } from 'vue-sidebar-menu'
<template>
	 <div id="demo" :class="[{'collapsed' : collapsed}]">
    <div class="demo">
      <router-view></router-view>
    </div>
  	 <sidebar-menu :menu="menu" />
  	 </div>
</template>

<script>
 export default {
        data() {
            return {
                menu: [
                    {
                        header: true,
                        title: 'Main Navigation',
                        // component: componentName
                        // visibleOnCollapse: true
                    },
                    {
                        href: '/',
                        title: 'Оплаты',
                        icon: 'fa fa-user',
                        /*
                        disabled: true
                        badge: {
                            text: 'new',
                            // class:''
                        }
                        */
                    },
                    {
                        title: 'Услуги',
                        icon: 'fa fa-chart-area',
                        href: '/services'
                    }
                ]
            }
        }
    }
</script>

<style lang="scss">
@import url('https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600');
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}

h1, h2 {
  font-weight: normal;
}

ul {
  list-style-type: none;
  padding: 0;
}

li {
  display: inline-block;
  margin: 0 10px;
}

a {
  color: #42b983;
}

body,
html {
  margin: 0;
  padding: 0;
}

body {
  font-family: 'Source Sans Pro', sans-serif;
  background-color: #f2f4f7;
}

#demo {
  padding-left: 350px;
}
#demo.collapsed {
  padding-left: 50px;
}

.demo {
  padding: 50px;
}

.badge-danger {
  background-color: #ff2a2a;
  color: #fff;
  }
</style>
