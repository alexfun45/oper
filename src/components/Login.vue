<template>
	<input type="submit" value="Login" v-on:click="login">
</template>
<script>
	import Vue from 'vue'
	var VueResource = require('vue-resource');
	Vue.use(VueResource)
	import Router from 'vue-router'
	Vue.use(Router)
	var Auth = {
  		loggedIn: false,
  		login: function() { this.loggedIn = true },
  		logout: function() { this.loggedIn = false }  
		};
	export default {
  		name: 'Login',
  		data() {
  			return {
  				url: "./src/api.php"
  			}
  		},
  		methods: {
  			login: function() {
      		Auth.login();
      		router.push(this.$route.query.redirect);
  		}
  	}
  	}
</script>