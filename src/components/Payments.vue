<template>
		<div>
		<v-dialog/>
		<div style="margin-bottom: 20px;">
			<h1 style="display: inline-block">Оплаты</h1>
		</div>
		<div>
			<v-server-table ref='table' url="./src/api.php?oper=get_payments" :columns="columns" :options="options">
				<span slot="erase" slot-scope="{row}">
            	<span class='mini-btn fa fa-trash' v-on:click="remove(row)"></span>
        		</span>	
        </v-server-table>		 
		</div>
		<div class='btns'><span class='btn' v-on:click="openAddPayment"><i>+</i>добавить оплату</span></div>
		
		</div>
</template>
<script>
import Vue from 'vue'
var VueResource = require('vue-resource');
Vue.use(VueResource)
import VModal from 'vue-js-modal'
Vue.use(VModal, { dynamic: true, dialog: true, injectModalsContainer: true })
import DatePicker from 'vue2-datepicker'
var month = ['Январь', 'Февраль', 'Март', 'Апрель' ,'Май' ,'Июнь' ,'Июль' ,'Август' ,'Сентябрь' ,'Октябрь' ,'Ноябрь' ,'Декабрь'];
export default {
  		name: 'SidebarMenu',
  		components: { DatePicker },
  		data() {
  			return {
  				services: [],
  				value1:Date,
  				urlupload: './src/api.php',
      		//headers: {'access-token': '<your-token>'},
      		filesUploaded: [],
  				api: "./src/api.php",
  				options:{
  					compileTemplates: true,
  					templates:{
						period: function (h, row, index) {
                		var d = new Date(row.period*1000);
                		d = month[d.getMonth()] + " " + d.getFullYear();
                		return d;
            		}  					
  					},
			    	headings: {
    					payment: 'оплата',
    					period: 'период',
    					name: 'агент',
    					service: 'услуга',
    					objectId: 'объект',
    					erase : ''
 						},
 				columnsClasses:{name:"visiblec", id:"hiddenc"}
  				},
  				columns: ['id', 'payment', 'period','name', 'service', 'objectId', 'erase'],
  				format: "MMM-yyyy"
  			}
  		},
  	created() {
  			var _self = this;
			axios.post(_self.api, {'oper':'get_services', filters: 'all'}).then((response)=>{ _self.$data.services = response.data.data; console.log(response.data.data); });
  		},
  	methods: {
  		remove(){
			var self = this;
			this.$modal.show('dialog', {
				  title: 'Вы уверены что хотите удалить запись?',
				  //text: 'You are too awesome',
				  buttons: [
				    {
				      title: 'удалить',
				      handler: () => { 
				      	axios.post(self.api, {'oper':'remove_payment', id: row['id']}).then((response) => {
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
  		openAddPayment(){
			var _self = this,
  		  		 services = this.$data.services;
  		  	
  		  this.$modal.show({
			  template: `
			    <div class='box'>
			    	<div id="partition-register" class="partition">
			    		<div class="partition-title">Добавить Оплату</div> 
			    		<div class="partition-form">
			    			<form autocomplete="false">
			    				<div class='edit-part'>
			    						<table class='listtable'>
			    							<thead><th>агент</th><th>объект</th><th>услуга</th></thead>
			    							<tbody>
												<tr :class="{'active': idx == activeIndex}" v-on:click="onServiceSelect(s, idx)" v-for="(s, idx) in services" :value="s">
													<td>{{s.agent}}</td>
													<td>{{s.object}}</td>
													<td>{{s.service}}</td>
												</tr>	
											</tbody>		    					
			    						</table>
			    				</div>
			    				<div class='edit-part'>
			    					<span>период оплаты</span><date-picker v-model="selectedDate" lang="ru" type="month" format="YYYY-MM"></date-picker></div>
			    				</div>
								<div class='edit-part'>
			    					<span>внесенная плата: </span><input type='text' v-model="payment" value='' id='payment'>
			    				</div>
			    			</form>
			    			<div style="margin-top: 42px;"></div>
			    			<div class="button-set">
			    				<button @click="add_payment" id="goto-signin-btn">Добавить</button>
			    				<button @click="close" id="register-btn">Отмена</button>
			    			</div></div></div></div>`,
			 data() {
			 	return {
			 		activeIndex: null,
			 		selectedDate: new Date(),
			 		selectedService: {},
			   	api: "./src/api.php",
			   	services: services,
			   	payment:0
			   }
			  },
			  components: { DatePicker },
			  methods: {
			  		onServiceSelect: function(s, index){
			  			this.activeIndex = index;
			  			this.$data.selectedService = s;
						//this.$data.selectedService = this.$data.selected_agent.service;
						//this.$data.subscription = this.$data.selected_agent.subscription;		  		
			  		},
					add_payment: function(){
						var data = {
							service: this.$data.selectedService.id,
							payment: this.$data.payment,
							date: this.$data.selectedDate.getTime()/1000
						};
						axios.post(_self.api, {'oper':'add_payment', data: JSON.stringify(data)}).then((response) => {
					
						});
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
			   		
  		
  		
  	}
  		
  	}
 </script>