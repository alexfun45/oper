<template>
<div id="app" v-cloak>

 <input class="newuser-input" type="text" ref='newuser' placeholder="Новый пользователь...">
 <input class="newuser-input" type="text" ref='newuserpass' placeholder="Пароль...">
 <i class='fa fa-check save-user-btn' @click.enter.prevent='addUser'></i> 
 <ul class="users">
  <transition-group name='user'>
   <li v-for='u in users' :key='u.id' mode="out-in">{{u.name}} <button class="deleteBtn" @click="confirmDelete(u)">x</button></li>
  </transition-group>
 </ul>
 <transition name="appear">
  <confirmation-modal :user="selectedUser" v-if='confirmModal' @confirm='deleteUser' @cancel="cancelDelete"></confirmation-modal>
 </transition>

</div>

</template>
<script>
import Vue from 'vue'
import VueSession from 'vue-session'
Vue.use(VueSession)
class User {
	constructor(name, id) {
		this.name = name;
		this.id = id;
	}
	getId() {
		return this.id;	
	}
}

User.id = 0;
Vue.component("confirmation-modal", {
	template: `<div class="modal">
	<div class="modal-window">
	  <p>Вы действительно хотите удалить пользователя <strong>{{ user.name }}</strong> ?</p>
	  <div class="actions">
		 <button class="cancel" @click="onCancel">Отмена</button>
		 <button class="confirm" @click="onConfirm">Удалить</button>
	  </div>
	</div>
 </div>`,
	props: ["open", "user"],
	methods: {
		onConfirm() {
			this.$emit("confirm");
		},
		onCancel() {
			this.$emit("cancel");
		}
	}
});
export default {
	name: "users",
	data() {
  		return {
			users: [],
			confirmModal: false,
			selectedUser: null
		}},
	methods: {
		addUser() {
			let user = this.$refs.newuser.value;
			let pass = this.$refs.newuserpass.value;
			var _self = this;
			if (user) {
				axios.post("./", {'oper':'add_user', name: user, pass: pass}).then((response)=>{ var newuserid = response.data; _self.$data.users.push(new User(user, newuserid)); });
				this.$refs.newuser.value = "";
				this.$refs.newuserpass.value = "";
			}
		},
		shuffle() {
			this.$data.users.sort(() => Math.random() > 0.5);
		},
		confirmDelete(u) {
			this.selectedUser = u;
			this.confirmModal = true;
		},
		cancelDelete() {
			this.confirmModal = false;
			this.selectedUser = null;
		},
		deleteUser() {
			this.confirmModal = false;
			let index = this.$data.users.indexOf(this.selectedUser);
			axios.post("./", {'oper': 'delete_user', id: this.selectedUser.getId() });
			this.$data.users.splice(index, 1);
			this.selectedUser = null;
		}
	},
	created () {
		var _self = this;
		axios.post("./", {'oper': 'get_users'}).then((response)=>{ 
			var users = response.data;
			for(var i=0;i<users.length;i++)
				_self.$data.users.push(new User(users[i].login, users[i].id));
		} );
		//this.$data.users.push(new User('Guillaume'))
		//this.$data.users.push(new User('Vianney'))
		//this.$data.users.push(new User('Justin'))
		//this.shuffle()
	}
};

</script>

<style lang="scss">
	 *,
    *::before,
    *::after {
     box-sizing: border-box;
    }
    
    body {
     overflow-x: hidden;
     padding: 0;
     margin: 0;
     font-family: sans-serif;
     color: white;
    }
    .save-user-btn{font-size: 20px; color: green; cursor: pointer;}
    [v-cloak] {
     display: none;
    }
    
    #app {
     max-width: 1080px;
     margin: 0 auto;
     padding: 10px;
     text-align: left !important;
    }
    
    button {
     cursor: pointer;
    }
    
    .users {
     list-style: none;
     margin: 0;
     padding: 0;
     margin: 1em 0;
     line-height: 2;
    }
    
    .users li {
     padding: 4px 2em;
     display: flex;
     justify-content: space-between;
     background: rgba(0, 0, 0, .2);
     margin-bottom: 6px;
    }
    
    .users li:nth-of-type(even) {
     background: rgba(0, 0, 0, .2);
    }
    
    .users li:nth-of-type(odd) {
     background: rgba(0, 0, 0, .4);
    }
    
    .deleteBtn {
     all: unset;
     cursor: pointer;
    }
    
    .newuser-input {
     font-size: inherit;
     padding: 4px;
     display: inline-block;
     width: 90%;
     margin: 0 auto;
    }
    
    .user-enter {
     opacity: 0;
     transform: translateY(-5px);
    }
    
    .user-enter-active {
     transition: .3s;
    }
    
    .user-leave-active {
     opacity: 0;
     transform: translateX(50px);
     transition: .3s;
    }
    
    .user-move {
     transition: .5s;
    }
    
    .modal {
     position: fixed;
     top: 0;
     left: 0;
     bottom: 0;
     right: 0;
     background: rgba(0, 0, 0, .5);
    }
    
    .modal-window {
     position: absolute;
     top: 50%;
     left: 50%;
     transition: .5s;
     width: 100%;
     min-width: 400px;
     max-width: 600px;
     background: white;
     box-shadow: 0 0 10px rgba(0, 0, 0, .5);
     transform: translate(-50%, -50%);
     padding: 1em;
     color: black;
     text-align: center;
    }
    
    .modal-window .actions {
     display: flex;
     justify-content: flex-end;
    }
    
    .actions button {
     font-size: inherit;
     margin: 4px;
     border: none;
     padding: 6px 8px;
     cursor: pointer;
    }
    
    .actions .cancel {
     background: darkred;
     color: white;
    }
    
    .actions .confirm {
     background: darkcyan;
     color: white;
    }
    
    .appear-enter {
     opacity: 0;
    }
    
    .appear-enter .modal-window {
     transform: translate(-75%, -50%);
    }
    
    .appear-enter-active {
     transition: .5s;
    }
    
    .appear-leave-active .modal-window {
     transform: translate(0, -50%);
    }
    
    .appear-leave-active {
     opacity: 0;
     transition: .5s;
    }
</style>