<template>
	<div>
		<v-dialog/>
		<h1>Услуги</h1>
		<div>
			<v-server-table ref='table' url="./src/api.php?oper=get_services" :columns="columns" :options="options">		
				<span slot="contract" slot-scope="{row}">
					<a class="iconlink fa fa-file" :href="getContract(row.contract)"></a>				
				</span>				
				<span slot="erase" slot-scope="{row}">
					<span v-on:click='setRow(row)'><file-upload :accept='accesfiles' :btn-label='upload_label' @error='errorUpload' :additional-data='upload_data' :url='urlupload' :headers="headers" @change="onFileChange"></file-upload></span>
            	<span class='mini-btn fa fa-edit' v-on:click="edit(row)"></span>-->
            	<span class='mini-btn fa fa-trash' v-on:click="remove(row)"></span>
        	</span>			
			</v-server-table>
		</div>
		<div><span class='btn' v-on:click="openAddWin"><i>+</i>добавить услугу</span></div>
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
import FileUpload from 'v-file-upload'
Vue.use(FileUpload)
var selectedRows = 0;
export default {
  		name: 'SidebarMenu',
  		data() {
  			return {
  				agents: [],
  				api: "./src/api.php",
  				orgs: [],
  				objects: [],
  				types:[],
  				upload_label: '',
  				upload_data: {oper: 'fileprepare'},
  				accesfiles: "*",
  				urlupload: './src/api.php',
      		//headers: {'access-token': '<your-token>'},
      		filesUploaded: [],
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
    				id: '',
    				agent: 'контрагент',
    				cid: 'номер договора',
    				service: 'услуга',
    				org: 'организация',
    				subscription:'абонентская плата',
    				object: 'объект',
    				type: 'тип услуги',
    				contract: 'договор',
    				erase:"операции"
 					},
  				},
  				columns: ['id', 'agent', 'cid', 'org', 'object', 'service', 'subscription', 'contract', 'erase'],
	         tableData: []
  			}
  		},
  		created() {
  			var _self = this;
			this.$bus.$on('add_service', function(data) {
					axios.post(_self.api, {'oper':'add_service', data: JSON.stringify(data)}).then((response) => {
					
					});	  		  
			});
			
			axios.post(_self.api, {'oper':'get_lists'}).then((response)=>{
				_self.$data.agents = response.data.agents;
				_self.$data.orgs = response.data.orgs;
				_self.$data.objects = response.data.objects;
				_self.$data.types = response.data.types;
				})	
  		},
  		methods: {
  		 setRow(row){
  		  	  //console.log(row);
			  //selectedRows.push(row.id);
			  if(selectedRows==0)
			  	selectedRows = row.id;
			  return true;		  
  		  },
  		 getContract(contract){
  		  	if(contract!="" && contract!=null)
				return "./src/data/"+contract;
			else
				return "#";  		  
  		  },
  		  onFileChange (file) {
  		  		console.log(selectedRows);
  		  		var self = this;
  		  		file.rowid = selectedRows;
			   axios.post(this.api, {'oper':'fileupload', data: file}).then((response) => {
					self.$refs.table.refresh();
					});	
			   selectedRows = 0;	  
  		  },
  		  errorUpload: function(){
			console.log('error upload file');  		  
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
				      	axios.post(self.api, {'oper':'remove_service', id: row['id']}).then((response) => {
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
  		show () {
  		  var _self = this;
  		  var _orgs = this.$data.orgs,
  		  	   _agents = this.$data.agents,
  		  	   _objects = this.$data.objects;
		   this.$modal.show({
			  template: `
			    <div class='box'>
			    	<div id="partition-register" class="partition">
			    		<div class="partition-title">Добавить Услугу</div> 
			    		<div class="partition-form">
			    			<form autocomplete="false">
			    				<div class='edit-part'>
			    					<span>контрагент: </span>
			    						<select id="agent" v-model="selected_agent" @change="onAgentChange">
			    							<option v-for="a in agents" :value="a">{{a.name}}</option>
			    					 </select>
			    				</div>
			    				<div class='edit-part'>
			    					<span>номер договора: </span>
			    					<input type='text' value='' v-model="cid" id='cid'>
			    				</div>
			    				<div class='edit-part'>
			    					<span>услуга: </span>
			    					<input type='text' value='' v-model="service" id='service'>
								</div>
								<div class='edit-part'>
			    					<span>абонентская плата</span><input type='text' v-model="subscription" value='' id='subscription'>
			    				</div>
			    				<div class='edit-part'>
			    					<span>объект(индекс): </span><select v-model="selected_object" @change="onObjectChange" id="obj">
			    						<option v-for="o in objects" v-bind:value="o">{{o._index}}</option>
			    					</select>
			    				</div>
			    				<div class='edit-part'>
			    					<span>организация</span><input type='text' v-model="organization" value='' id='org' disabled='disabled'>
			    				</div>
			    			</form>
			    			<div style="margin-top: 42px;"></div>
			    			<div class="button-set">
			    				<button @click="add_service" id="goto-signin-btn">Добавить</button>
			    				<button @click="close" id="register-btn">Отмена</button>
			    			</div></div></div></div>`,
			 data() {
			 	return {
			   	api: "./src/api.php",
			   	orgs: _orgs,
			   	service: "",
			   	subscription: "",
			   	cid: "",
			   	organization: '',
			   	subscription: 0,
			   	agents: _agents,
			   	objects: _objects,
			   	selected_org: 0,
			   	selected_agent: {},
			   	selected_object:{}	  
			   }
			  },
			  methods: {
			  		onAgentChange: function(event){
			  			//this.$data.selected_agent = this.$data.agents[event.target.value];
			  			//console.log(this.$data.selected_agent);
						this.$data.service = this.$data.selected_agent.service;
						this.$data.subscription = this.$data.selected_agent.subscription;		  		
			  		},
			  		onObjectChange: function(event){
			  			this.$data.organization = this.$data.orgs[this.$data.selected_object.org_id].name;
			  			this.$data.selected_org = this.$data.orgs[this.$data.selected_object.org_id];		  		
			  		},
			  		onChange: function(event){
						this.$data.selected_org	= event.target.value;		  		
			  		},
					add_service: function(){
						var data = {
							agent: this.$data.selected_agent.id,
							service: this.$data.service,
							subscription: this.$data.subscription,
							cid: this.$data.cid,
							org: this.$data.selected_org.id,
							object: this.$data.selected_object.id
						};
						console.log(data);
						this.$bus.$emit('add_service', data);
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
.file-upload{display: inline-block;}
.file-upload .input-wrapper {
	 position: relative;
    background-color: #fff !important;
    height: auto !important;
    display: inline-block;
}
.file-upload .input-wrapper .file-upload-label {
	font-size: 1em !important;
	line-height: 1em !important;
	color: #000 !important;
	position: relative !important;
	padding: 2px !important;
	width: auto !important;
	margin-left: 4px;
	top: 3px;
}
.file-upload .input-wrapper .file-upload-label .file-upload-icon {
    font-size: 28px !important;
}
.iconlink i{ font-size: 14px; cursor: pointer;}

</style>