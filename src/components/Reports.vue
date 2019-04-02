<template>
	<div class='main-block'>	
		<div class='page-title'><h1>Отчеты</h1></div>
		<div class='report-tab'>
			<div class='filters'>
				<span><date-picker :shortcuts="shortcuts" :range="range" @change="changeDate" v-model="selectedDate" lang="ru" type="month" format="YYYY-MM"></date-picker></span>
				<span><v-selectpage v-model="selectedAgents" :language="lang" :placeholder="placeholder" :multiple="true" @values="onSelect" :data="agents" key-field="value" show-field="text" ></v-selectpage>
 				</span>
			</div>
			<v-server-table ref='table' url="./src/api.php?oper=get_agents_res&tab=1" :columns="columns" :options="options">
         </v-server-table>		 
		</div>
		<div class='chart'>
  			<apexchart width="80%" height="400px" ref="chart" type="bar" :options="options_chart" :series="series"></apexchart>
		</div>
		<div>
			<span><date-picker :shortcuts="shortcuts" :range="range" @change="changeDateStat" v-model="selectedDate" lang="ru" type="month" format="YYYY-MM"></date-picker></span>		
		</div>
		<div class='chart'>
  			<apexchart width="80%" height="400px" ref="chart2" type="line" :options="options_chart2" :series="series2"></apexchart>
		</div>
		</div>
</template>
<script>
import Vue from 'vue'
var VueResource = require('vue-resource');
Vue.use(VueResource)
import DatePicker from 'vue2-datepicker'
import VueApexCharts from 'vue-apexcharts'
Vue.use(VueApexCharts)

Vue.component('apexchart', VueApexCharts)
var month = ['Январь', 'Февраль', 'Март', 'Апрель' ,'Май' ,'Июнь' ,'Июль' ,'Август' ,'Сентябрь' ,'Октябрь' ,'Ноябрь' ,'Декабрь'];
function formatDate(date) {

  var dd = date.getDate();
  if (dd < 10) dd = '0' + dd;

  var mm = date.getMonth() + 1;
  if (mm < 10) mm = '0' + mm;

  var yy = date.getFullYear() % 100;
  if (yy < 10) yy = '0' + yy;
  
  var hh = date.getHours();
  if (hh < 10) hh = '0' + hh;
  
  var tm = date.getMinutes();
  if (tm < 10) tm = '0' + tm;
  
  return dd + '.' + mm + '.' + yy + " " + hh+":"+tm;
}
var selectedAgents = [],
	 start_date = +(new Date()),
	 end_date,
	 start_date2 = +(new Date(new Date().getFullYear(), 0, 1)),
	 end_date2 = +(new Date(new Date().getFullYear(), 12, 30)),
	 agentTicks = [],
	 agentsData = [],
	 monthsSumm = [],
	 months = [];
export default {
  		name: 'SidebarMenu',
  		components: { DatePicker},
  		data() {
  			//let agentTicks = [];
  			//let agentsData = [];
  			return {
	  			api: "./src/api.php",
	  			url: "./src/api.php?oper=get_agents_res",
	  			agents:[],
	  			alltime: false,
	  			range: true,
	  			lang: "en",
	  			//agentTicks: [],
	  			//agentsData: [],
	  			selectedAgents: [],
	  			placeholder: "выберите агента",
	  			searchable: true,
	  			start_date: new Date(),
	  			shortcuts: [
		        {
		          text: 'Все время',
		          onClick: () => {
		            this.alltime = true
		          }
		        }
		      ],
		      options_chart2: {
					chart: {
		          id: 'vuechart-agents2'
		        },
		        xaxis:{
			        categories: months,
			        labels: {
							 style: {
								 fontSize: '11px'						 
							 }		          
			           }
			        }	  			     
		      },
		      series2: [{
		        name: 'сумма',
		        data: monthsSumm
		      }],
	  			options_chart:{
					chart: {
		          id: 'vuechart-agents'
		        },
		        xaxis: {
		          categories: agentTicks,
		          labels: {
						 style: {
							 fontSize: '11px'						 
						 }		          
		           }
		        }	  			
	  			},
	  			series: [{
		        name: 'сумма',
		        data: agentsData
		      }],
	  			options:{
	  				requestAdapter: function(){
	  					return {"selectedAgents":selectedAgents, "tab":"tab", "start_date": start_date, "end_date": end_date};
	  				},
	  				//params: {"selectedAgents": JSON.stringify(selectedAgents), "number": 3},
	  				//customFilters: ['selectedAgents'],
	  				compileTemplates: true,
	  				headings: {
							name: 'агент',
							'monthyear': 'месяц',
							'summ': 'сумма'
						}
	 			},
	 			columns: ['name', 'monthyear', 'summ'],
	  		}
  		},
  		created() {
  			var _self = this;
			axios.post(_self.api, {'oper':'get_agents_list'}).then((response)=>{ 
				_self.$data.agents = response.data.data;
				 });
			axios.get(_self.url+"&start_date="+start_date, {'oper':'get_agents_res', 'selectedDate': start_date}).then((response)=>{ 
				var data = (response.data.data);
				for(var i=0;i<data.length;i++){
					agentTicks.push(data[i].name);
					agentsData.push(data[i].summ);				
				}
			}); 
			axios.get(_self.api+"?oper=get_months_stats"+"&start_date="+start_date2+"&end_date="+end_date2, {'oper':'get_months_stats'}).then((response)=>{ 	
				var data = (response.data.data);
				for(var i=0;i<data.length;i++){
					monthsSumm.push(data[i].summ);
					months.push(data[i].monthyear);				
					}
				this.$refs.chart2.updateOptions({ xaxis: { categories : months } });
				this.$refs.chart2.updateSeries([{
				  data: monthsSumm
				}]);
			});	
  		},
  		methods: {
  		getAgentsTicks(){
			return this.$data.agentTicks;  		
  		},
  		changeDate(date){
			start_date = (+date[0]);
			end_date = (+date[1]);
			this.$refs.table.refresh();
			agentTicks = [];
			agentsData = [];
			axios.get(this.url+'&start_date='+start_date+'&end_date='+end_date, {'oper':'get_agents_res'}).then((response)=>{ 
				var data = (response.data.data);
				for(var i=0;i<data.length;i++){
					agentTicks.push(data[i].name);
					agentsData.push(data[i].summ);				
				}
				//this.options_chart.xaxis.categories = agentTicks;
				this.$refs.chart.updateOptions({ xaxis: { categories : agentTicks } });
				this.$refs.chart.updateSeries([{
				  data: agentsData
				}]);
				//this.series[0].data = agentsData;
			}); 
							 		
  		},
  		changeDateStat(date){
			start_date = (+date[0]);
			end_date = (+date[1]);
			
			axios.get(self.api+"?oper=get_months_stats"+'&start_date='+start_date+'&end_date='+end_date, {'oper':'get_months_stats'}).then((response)=> {
				var data = (response.data.data);
				monthsSumm = [];
				months = [];
				for(var i=0;i<data.length;i++){
					monthsSumm.push(data[i].summ);
					months.push(data[i].monthyear);				
				}
				this.$refs.chart2.updateOptions({ xaxis: { categories : months } });
				this.$refs.chart2.updateSeries([{
				  data: monthsSumm
				}]);
			});		
  		},
	  	customLabel (option) {
	      return `${option.library} - ${option.language}`
	    },
	    onSelect (option, id) {
	    	console.log(this.$data.selectedAgents)
	    	selectedAgents = this.$data.selectedAgents;
	    	this.$refs.table.refresh();
	    	axios.get(this.url+'&start_date='+start_date+'&end_date='+end_date+'&selectedAgents='+selectedAgents, {'oper':'get_agents_res'}).then((response)=>{ 
				var data = (response.data.data);
				agentTicks = [];
				agentsData = [];
				for(var i=0;i<data.length;i++){
					agentTicks.push(data[i].name);
					agentsData.push(data[i].summ);				
				}
				//console.log(agentTicks);
				//console.log(agentsData);
				this.$refs.chart.updateOptions({ xaxis: { categories : agentTicks } });
				this.$refs.chart.updateSeries([{
				  data: agentsData
				}]);
				//this.options_chart.xaxis.categories = agentTicks;
				//this.series[0].data = agentsData;
			}); 
	    }
  }
  	}
  	</script>