<template>
	<div>
		<v-dialog/>
		<h1>Организации</h1>
		<div>
			<v-server-table ref='table' url="./src/api.php?oper=get_organizations" :columns="columns" :options="options">
				<span slot="erase" slot-scope="{row}"> 
            <span class='mini-btn fa fa-edit' v-on:click="edit(row)"></span>
            <span class='mini-btn fa fa-trash' v-on:click="remove(row)"></span>
        </span>			
			</v-server-table>
		</div>
		<div><span class='btn' v-on:click="openAddWin"><i>+</i>добавить организацию</span></div>
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

export default {
  		name: 'SidebarMenu',
  		data() {
  			return {
  				agents: [],
  				api: "./src/api.php",
  				time:0,
  				options:{
  					compileTemplates: true,	
			    headings: {
    				name: 'название',
    				inn: 'ИНН',
    				erase: 'операции'
 					}
  				},
  				columns: ['id', 'name', 'inn','erase'],
	         tableData: []
  			}
  		},
  	template: `
			<slot name='edit'>remove</slot>  	
  	`,
  		created() {
  			var _self = this;
			this.$bus.$on('add_org', function(data) {
					axios.post(_self.api, {'oper':'add_organization', data: data}).then((response) => {
					
					});
					  		  
			})
  		},
  		methods: {
  		  edit (row) {
  		  	var self = this;
			var _row = {}, _id = row['id'], self = this, bindRow = row;
			for(var i=0, key = '', col=this.$data.columns[i];i<this.$data.columns.length;col=this.$data.columns[i], i++){
				 //key = self.$data.options.headings[col];
				 //if(typeof key!==undefined)
					_row[col] = (row.hasOwnProperty(col)) ? row[col]:"";
				}
			delete _row['id'];
			delete _row['erase'];
			this.$modal.show({
			  template: `
			    <div class='box'>
			    	<div id="partition-register" class="partition">
			    		<div class="partition-title">Редактировать Организацию</div> 
			    		<div class="partition-form">
			    			<form autocomplete="false">
			    				<div class='edit-part' v-for="(value, property) in val">
			    					<span>{{property}}</span><input style='width:60%;' v-model="val[property]" type="text" v-bind:value="value">
			    				</div>
			    			</form>
			    			<div class="button-set">
			    				<button @click="edit_org" id="goto-signin-btn">Сохранить</button>
			    				<button @click="close" id="register-btn">Отмена</button>
			    			</div></div></div></div>`,
			    			data() {
			    				return {
									val: _row,
									id: _id
									}			    			
			    			},
			    			methods: {
			    				edit_org: function(){
			    					var data = this.$data.val;
									for(var key in data)
										bindRow[key] = data[key];
			    					data['id'] = this.$data.id;
									axios.post(self.api, {'oper':'edit_org', data: JSON.stringify(data)});
									//bindRow = data;									
									//self.$refs.table.refresh();
									this.$emit('close');			    				
			    				},
								close: function(){ this.$emit('close'); }				    				
			    				}			    			
			    		}, {time: 0}, {height: 'auto'})
			    				
					  
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
				      	axios.post(self.api, {'oper':'remove_organization', id: row['id']}).then((response) => {
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
			    		<div class="partition-title">Добавить Организацию</div> 
			    		<div class="partition-form">
			    			<form autocomplete="false">
			    				<input type="text" id="oname" placeholder="название">
			    				<input type="text" id="inn" placeholder="ИНН">
			    			</form>
			    			<div style="margin-top: 42px;"></div>
			    			<div class="button-set">
			    				<button @click="add_org" id="goto-signin-btn">Добавить</button>
			    				<button @click="close" id="register-btn">Отмена</button>
			    			</div></div></div></div>`,
			  data:{
			   api: "./src/api.php"		  
			  },
			  methods: {
					add_org: function(){
						var data = {
							oname: oname.value,
							inn: inn.value,
						};
						console.log(data);
						this.$bus.$emit('add_org', data);
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
			  'before-close': (event) => { }
			})
		  },
		  hide () {
		    this.$modal.hide('hello-world');
		  },
	    openAddWin () {
	     	this.show();
	    }
	  }
  	}
  	
</script>
<style>
	@import '../scss/dialogs.css';
</style>