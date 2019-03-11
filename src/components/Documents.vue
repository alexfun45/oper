<template>
	<div>
		<v-dialog/>
		<div style="margin-bottom: 20px;"><h1 style="display: inline-block">Документы</h1><file-upload :accept='accesfiles' :btn-label='upload_label' @error='errorUpload' :additional-data='upload_data' :url='urlupload' :headers="headers" @change="onFileChange"></file-upload></div>
		<div class="docs">
			<div class="files" >
				<div class="file" v-model="docs" v-for="d in docs">
					<div class='removing' @click="removeFile(d.id)"><i class='fa fa-trash'></i>удалить</div>
					<div><i class="fa fa-file-pdf"></i></div>
					<a v-bind:href="'./src/data/'+ d.name" class="item_name">
						<span v-if="d.agentname">Договор {{d.agentname}}</span>
						<span v-else>{{d.name}}</span>
					</a>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import Vue from 'vue'
import VModal from 'vue-js-modal'
import FileUpload from 'v-file-upload'
Vue.use(FileUpload)
Vue.use(VModal, { dynamic: true, dialog: true, injectModalsContainer: true })

export default {
  		name: 'SidebarMenu',
  		data() {
  			return {
  				upload_label: 'загрузить файл',
  				upload_data: {oper: 'fileprepare'},
  				accesfiles: "*",
  				urlupload: './src/api.php',
      		//headers: {'access-token': '<your-token>'},
      		filesUploaded: [],
  				api: "./src/api.php",
  				docs: [],
  			}
  		},
  	created() {
  			var _self = this;
			axios.post(_self.api, {'oper':'get_documents', filters: 'all'}).then((response)=>{ _self.$data.docs = response.data; });
  		},
  	methods: {
  		refresh(){
  			var _self = this;
			axios.post(this.api, {'oper':'get_documents', filters: 'all'}).then((response)=>{  _self.$data.docs = response.data; });  		
  		},
  		onFileChange (file) {
  		  		var self = this;
			   axios.post(this.api, {'oper':'_fileupload', data: file}).then((response) => {
					self.refresh();
					});		  
  		  },
		errorUpload: function(){
			console.log('error upload file');  		  
  		  },
  		removeFile(id){
  			 var self = this;
			 axios.post(this.api, {'oper':'removeFile', id: id}).then((response) => {
					self.refresh();
					});  		
  		}
  	}
  	}
  	
  </script>
  <style>
	@import '../scss/documents.css';
</style>