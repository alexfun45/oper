<template>
	<div>
		<v-dialog/>
		<h1>Контрагенты</h1>
		<div>
			<v-server-table ref='table' url="./src/api.php?oper=get_agents" :columns="columns" :options="options">
				<span slot="contract" slot-scope="{row}">
					<a class='iconlink' class="fa fa-file" :href="getContract(row.contract)"></a>				
				</span>			
				<span slot="erase" slot-scope="{row}">
					<span v-on:click='setRow(row)'><file-upload :accept='accesfiles' :btn-label='upload_label' @error='errorUpload' :additional-data='upload_data' :url='urlupload' :headers="headers" @change="onFileChange"></file-upload></span>
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
import FileUpload from 'v-file-upload'
Vue.use(FileUpload)

Vue.component('delete', {
    props: ['data', 'index', 'column'],
    template: `<a class='delete' @click='erase'>удалить</a>`,
    methods: {
        erase() {
            let id = this.data.id; // delete the item
        }
    }
});
var selectedRows = 0;
export default {
  		name: 'SidebarMenu',
  		data() {
  			return {
  				agents: [],
  				api: "./src/api.php",
  				time:0,
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
    				name: 'имя',
    				cid: 'номер договора',
    				service: 'услуга',
    				subscription:'абонентская плата',
    				phone: 'телефон',
    				address: 'адрес',
    				aname: 'имя',
    				contract: 'договор',
    				erase: 'операции'
 					},
  				},
  				//columns: ['id', 'name', 'cid', 'service', 'subscription', 'erase', 'phone', 'email', 'address', 'aname', 'surname'],
  				columns: ['id', 'name', 'cid', 'service', 'subscription', 'phone', 'email', 'address', 'aname', 'contract', 'erase'],
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
  		methods: {
  		  setRow(row){
  		  	  //console.log(row);
			  //selectedRows.push(row.id);
			  if(selectedRows==0)
			  	selectedRows = row.id;
			  return true;		  
  		  },
  		  onFileChange (file) {
  		  		//console.log(selectedRows);
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
  		  getContract(contract){
  		  	if(contract!="" && contract!=null)
				return "./src/data/"+contract;
			else
				return "#";  		  
  		  },
  		  edit(row){
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
									axios.post(self.api, {'oper':'edit_agent', data: data});
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