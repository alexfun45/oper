<template>
	<div>
		<v-dialog/>
		<h1>Контрагенты</h1>
		<div>
			<v-server-table ref='table' url="./src/api.php?oper=get_agents" :columns="columns" :options="options">
				<span slot="erase" slot-scope="{row}"> 
            <span class='mini-btn fa fa-edit' v-on:click="edit(row)"></span>
            <span class='mini-btn fa fa-trash' v-on:click="remove(row)"></span>
        </span>			
			</v-server-table>
		</div>
		<div><span class='btn' v-on:click="openAddWin"><i>+</i>добавить агента</span></div>
	</div>
</template>
<script>
import Vue from 'vue'
import {ServerTable, ClientTable, Event} from 'vue-tables-2'
import BootstrapVue from 'bootstrap-vue'
var VueResource = require('vue-resource');
Vue.use(VueResource)
Vue.use(ServerTable, {})
import VModal from 'vue-js-modal'
Vue.use(VModal, { dynamic: true, dialog: true, injectModalsContainer: true })

Vue.component('delete', {
    props: ['data', 'index', 'column'],
    template: `<a class='delete' @click='erase'>удалить</a>`,
    methods: {
        erase() {
            let id = this.data.id; // delete the item
        }
    }
});
export default {
  		name: 'SidebarMenu',
  		data() {
  			return {
  				agents: [],
  				api: "./src/api.php",
  				time:0,
  				options:{
  					compileTemplates: true,
					requestFunction: function (data) {
			        return axios.get(this.url, {
			            params: data
			        }).catch(function (e) {
			            this.dispatch('error', e);
			        }.bind(this));
			    },	
			    headings: {
    				name: 'имя',
    				cid: 'номер договора',
    				service: 'услуга',
    				subscription:'абонентская плата'
 					},
  				},
  				columns: ['id', 'name', 'cid', 'service', 'subscription', 'erase'],
	         tableData: []
  			}
  		},
  	template: `
			<slot name='edit'>remove</slot>  	
  	`,
  		created() {
  			var _self = this;
  			//this.axios.post(this.api, {'oper':'get_agents'}).then((response) => {
			  //var data = response.data;
			  //_self.tableData = data.data;
			  //_self.refs.tab.refresh();
			  //for(var i=0;i<data.length;i++){				  
			  //}
			//})
			this.$bus.$on('add_agent', function(data) {
					axios.post(_self.api, {'oper':'add_agent', data: data}).then((response) => {
					
					});
					  		  
			})
  		},
		mounted() {
			Ccomponent = this;  		
  		},
  		methods: {
  		  edit (row) {
			//alert(row.name);
			//var _row = row;
			//var _row = [].slice.call(row);
			var _row = {}, _id = row['id'], self = this, bindRow = row;
			_row = Vue.util.extend({}, row);
			delete _row['id'];
			this.$modal.show({
			  template: `
			    <div class='box'>
			    	<div id="partition-register" class="partition">
			    		<div class="partition-title">Добавить Контрагента</div> 
			    		<div class="partition-form">
			    			<form autocomplete="false">
			    				<div class='edit-part' v-for="(value, property) in val">
			    					<span>{{property}}</span><input style='width:60%;' v-model="val[property]" type="text" v-bind:value="value">
			    				</div>
			    			</form>
			    			<div class="button-set">
			    				<button @click="edit_agent" id="goto-signin-btn">Сохранить</button>
			    				<button @click="close" id="register-btn">Отмена</button>
			    			</div></div></div></div>`,
			    			minHeight: 600,
			    			data() {
			    				return {
									val: _row,
									id: _id
									}			    			
			    			},
			    			methods: {
			    				edit_agent: function(){
			    					var data = this.$data.val;
									for(var key in data)
										bindRow[key] = data[key];
			    					data['id'] = this.$data.id;
									axios.post(self.api, {'oper':'edit_agent', data: JSON.stringify(data)});
									//bindRow = data;									
									//self.$refs.table.refresh();
									this.$emit('close');			    				
			    				},
								close: function(){ this.$emit('close'); }				    				
			    				}
			    			
			    		})
			    				
					  
  		  },
  		  remove(row){
  		  		var self = this;
				this.$modal.show('dialog', {
				  title: 'Вы уверены что хотите удалить запись?',
				  //text: 'You are too awesome',
				  buttons: [
				    {
				      title: 'удалить',
				      handler: () => { 
				      	axios.post(self.api, {'oper':'remove_agent', id: row['id']}).then((response) => {
							self.$refs.table.refresh();
							this.$modal.hide('dialog');				
						 });
						 }
				    },
				    {
				      title: '',       // Button title
				      default: true,    // Will be triggered by default if 'Enter' pressed.
				      handler: () => {} // Button click handler
				    },
				    {
				      title: 'отмена'
				    }
				 ]
				})  
  		  },
  		  add_agent(){
			alert('add home agent');  		  
  		  },
  		  ajax: function(){
			alert('do ajax');  		  
  		  },
  		  show () {
  		  var _self = this;
		   this.$modal.show({
			  template: `
			    <div class='box'>
			    	<div id="partition-register" class="partition">
			    		<div class="partition-title">Добавить Контрагента</div> 
			    		<div class="partition-form">
			    			<form autocomplete="false">
			    				<input type="text" id="acompanyname" placeholder="название">
			    				<input type="text" id="anum" placeholder="номер договора">
			    				<input type="text" id="aservice" placeholder="услуга">
			    				<input type="text" id="asubscription" placeholder="абонентская плата">
			    				<input type="text" id="aphone" placeholder="номер телефона">
			    				<input type="text" id="email" placeholder="email">
			    				<input type="text" id="address" placeholder="адрес">
			    				<input type="text" id="aname" placeholder="имя">
			    				<input type="text" id="asurname" placeholder="фамилия">
			    			</form>
			    			<div style="margin-top: 42px;"></div>
			    			<div class="button-set">
			    				<button @click="add_agent" id="goto-signin-btn">Добавить</button>
			    				<button @click="close" id="register-btn">Отмена</button>
			    			</div></div></div></div>`,
			  data:{
			   api: "./src/api.php"		  
			  },
			  methods: {
					add_agent: function(){
						var data = {
							acompanyname: acompanyname.value,
							anum: anum.value,
							aservice: aservice.value,
							asubscription: asubscription.value,
							aphone: aphone.value,
							email: email.value,
							address: address.value,
							aname: aname.value,
							asurname: asurname.value
						};
						this.$bus.$emit('add_agent', data);
						//_self.$methods.add_agent();
						//_self.$root.add_agent(data);
						//_self.$refs.table.refresh();
						setTimeout(function(){ _self.$refs.table.refresh(); }, 2000);		
						this.$emit('close'); },
					close: function(){ this.$emit('close'); }		  
			  },
			  props: ['text']
			}, {
			  text: 'This text is passed as a property'
			}, {
			  height: 'auto'
			}, {
			  'before-close': (event) => { console.log('this will be called before the modal closes'); }
			})
		  },
		  hide () {
		    this.$modal.hide('hello-world');
		  },
		 add_agent: function(){
		 	//this.$emit('add_agent'); 
		 	//this.axios.post(this.api, {'oper':'add_agent'}).then((response) => {
							//alert(response);
			 				//console.log(response.data)
							//});	
		 	},
	    openAddWin () {
	     	this.show();
	    }
	  }
  	}
  	
</script>
<style lang="scss">
	h2 {
  margin-bottom: 30px;
}

th,
td {
  text-align: left;
}

th:nth-child(n+2),
td:nth-child(n+2) {
  text-align: center;
}

thead tr:nth-child(2) th {
  font-weight: normal;
}

.VueTables__sort-icon {
  margin-left: 10px;
}

.VueTables__dropdown-pagination {
  margin-left: 10px;
}

.VueTables__highlight {
  background: yellow;
  font-weight: normal;
}

.VueTables__sortable {
  cursor: pointer;
}

.VueTables__date-filter {
  border: 1px solid #ccc;
  padding: 6px;
  border-radius: 4px;
  cursor: pointer;
}

.VueTables__filter-placeholder {
  color: #aaa;
}

.VueTables__list-filter {
  width: 120px;
}
.btn{
  background-color: #4CAF50; /* Green */
  border: none;
  color: white;
  padding: 12px 25px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px; 
  cursor: pointer;
}
.btn >i {font-size: 20px; margin-right: 2px;}

.box {
    background: #fff none repeat scroll 0 0;
    border-radius: 2px;
    box-shadow: 0 0 40px #000;
    color: #8b8c8d;
    font-size: 0;
    height: 550px;
    overflow: hidden;
    width: 656px;
    font-family: Montserrat;
}
.box .partition {
    height: 100%;
    width: 100%;
}
.box .partition .partition-title {
    box-sizing: border-box;
    font-size: 20px;
    font-weight: 300;
    letter-spacing: 1px;
    padding: 30px;
    text-align: center;
    width: 100%;
}
.box .partition .partition-form {
    box-sizing: border-box;
    padding: 0 20px;
}
.box .button-set {
    margin-bottom: 8px;
}
.box button {
    background: #fff none repeat scroll 0 0;
    border: 1px solid #dddedf;
    border-radius: 4px;
    box-sizing: border-box;
    color: #8b8c8d;
    cursor: pointer;
    font-family: Open Sans,sans-serif;
    font-size: 10px;
    font-weight: 400;
    letter-spacing: 1px;
    width: 100%;
    margin-top: 8px;
    min-width: 140px;
    outline: medium none;
    padding: 10px;
    text-transform: uppercase;
    transition: all 0.1s ease 0s;
}
.box input[type="password"], .box input[type="text"] {
    -moz-border-bottom-colors: none;
    -moz-border-left-colors: none;
    -moz-border-right-colors: none;
    -moz-border-top-colors: none;
    border-color: -moz-use-text-color -moz-use-text-color #dddedf;
    border-image: none;
    border-style: none none solid;
    border-width: 0 0 1px;
    box-sizing: border-box;
    display: block;
    font-family: inherit;
    font-size: 12px;
    line-height: 2;
    margin-bottom: 4px;
    outline: medium none;
    padding: 4px 8px;
    transition: all 0.5s ease 0s;
    width: 100%;
}
.mini-btn{cursor: pointer;}
.box #register-btn, .box #signin-btn, #goto-signin-btn {
    margin-left: 8px;
    width: 40%;
}
.box .github-btn {
    border-color: #dba226;
    color: #dba226;
}
.box .large-btn {
    background: #fff none repeat scroll 0 0;
    width: 100%;
}
.box .large-btn span {
    font-weight: 600;
}
.edit-part > *{display: inline-block !important; margin-left: 4px;}

</style>