<template>
  <div id="app" v-on:click="closeResults">
    <notifications v-if="connected === 'true'"></notifications>
    <header-component id="header" v-if="connected === 'true'"></header-component>
      <router-view keep-alive></router-view>
    <profil-component></profil-component>
    <footer-component></footer-component>
  </div>
</template>

<script>
import {apiRoot} from '../config/localhost/settings.js'
import HeaderComponent from './components/Header.vue'
import FooterComponent from './components/Footer.vue'
import ProfilComponent from './components/ProfilCPN.vue'
import Notifications from './components/Notifications.vue'

export default {
  components: {
    HeaderComponent,
    FooterComponent,
    ProfilComponent,
    Notifications
  },
  data(){
    return {
      connected: 'true',
      selectedUser: {
        id : '',
        pseudo: '',
        avatar: '',
        firstname: '',
        lastname: '',
        age: '',
        country: '',
        description: '',
        color: '',
        city: '',
        hobbies: '',
        languages: {
          spokenLang: [],
          learningLang: []
        }
      },
      profilShowed: '',
      connectedUser: {
        id : '',
        pseudo: '',
        avatar: '',
        firstname: '',
        lastname: '',
        age: '',
        country: '',
        description: '',
        color: '',
        city: '',
        hobbies: '',
        languages: {
          spokenLang: [],
          learningLang: []
        }
      },
      notifications : ''
    }
  },
  created: function(){
    this.profilShowed = "false";
    var pseudo = this.getCookie("PLUME_pseudo");
    if(pseudo != "") {
      this.getUserState(pseudo);
      this.setConnectedUser(pseudo);
      
      var _this = this;
      setInterval(function() {
        _this.getNotifications(_this.connectedUser.pseudo);
      }, 2000);
    }
  },
  methods: {
    checkAvatar: function(avatar) {
      if(avatar == "") {
        avatar = "/static/avatar/default.jpg";
      }
    },
    closeResults: function(e)
    {
      if(document.getElementById("resultdiv") != null) {
        if (document.getElementById("resultdiv").style.display == "inline")
        {
          if (e.target != document.getElementById("searchbutton"))
          {
            document.getElementById("resultdiv").style.display = "none";
          }
        }
      }
    },
    logout: function(){
      document.cookie = "PLUME_pseudo=; expires=Thu, 01 Jan 1970 00:00:00 GMT";
      this.setUserState(this.connectedUser.pseudo, '');
      this.$router.push('/home/');
    },
    getUserState: function(pseudo) {
      var _this = this;

      fetch(apiRoot() + 'Controllers/User/getUserState.php', {
        method: 'POST',
        headers: {
          'Accept': 'application/json, application/xml, text/plain, text/html, *.*',
          'Content-Type': 'application/json; charset=utf-8'
        },
        dataType: 'JSON',
        body: JSON.stringify({pseudo: pseudo})
      }).then(function(response) {
        return response.json();
      }).then(function(data){
          if(data[0] == "Error"){
            _this.loginError = data[1];
          }
          else {
            if(data[0] == 2) _this.connected = "true";
            else {
              _this.connected = "";
              _this.logout();
            }
          }
      });
    },
    setUserState: function(pseudo, connected) {
      this.connected = connected;

      var state;
      if(connected == "true") state = 2;
      else state = 1;

      var _this = this;
      fetch(apiRoot() + 'Controllers/User/setUserState.php', {
        method: 'POST',
        headers: {
          'Accept': 'application/json, application/xml, text/plain, text/html, *.*',
          'Content-Type': 'application/json; charset=utf-8'
        },
        dataType: 'JSON',
        body: JSON.stringify({pseudo: pseudo, state: state})
      }).then(function(response) {
        return response.json();
      }).then(function(data){
        if(data[0] == "Error"){
          _this.loginError = data[1];
        }
      });
    },
    getSelectedUser: function(){
      var _this = this;
      fetch(apiRoot() + 'Controllers/User/getUser.php', {
        method: 'POST',
        headers: {
          'Accept': 'application/json, application/xml, text/plain, text/html, *.*',
          'Content-Type': 'application/json; charset=utf-8'
        },
        dataType: 'JSON',
        body: JSON.stringify({pseudo: _this.selectedUser.pseudo})
      }).then(function(response) {
        return response.json();
      }).then(function(data){
        if(data[0] == "Error") {

        }
        else {
          _this.selectedUser.pseudo = data['pseudo'];
          _this.selectedUser.avatar = data['avatar'];
          _this.selectedUser.firstname = data['firstname'];
          _this.selectedUser.lastname = data['lastname'];
          _this.selectedUser.age = data['age'];
          _this.selectedUser.country = data['country'];
          _this.selectedUser.city = data['city'];
          _this.selectedUser.description = data['description'];
          _this.selectedUser.color = data['color'];
          _this.selectedUser.hobbies = data['hobbies'];
          _this.selectedUser.languages.learningLang = [];
          for(var i = 0; i < data['languages']['learningLang']['learningLang'].length; i ++) {
            _this.selectedUser.languages.learningLang.push(data['languages']['learningLang']['learningLang'][i]['name_langue']);
          }
          _this.selectedUser.languages.spokenLang = [];
          for(var i = 0; i < data['languages']['spokenLang']['spokenLang'].length; i ++) {
            _this.selectedUser.languages.spokenLang.push(data['languages']['spokenLang']['spokenLang'][i]['name_langue']);
          }
        }

      });
    },
    languagesToFlag: function(country) {
      var flag = {
        Portuguese : '/static/flags/portugal.png',
        English : '/static/flags/united-kingdom.png',
        Chinese : '/static/flags/china.png',
        French : '/static/flags/france.png',
        Japanese : '/static/flags/japan.png',
        German : '/static/flags/germany.png',
        Spanish : '/static/flags/spain.png',
        Italian : '/static/flags/italy.png',
        Russian : '/static/flags/russia.png'
      }
      return flag[country];
    },
    changeSelectedUser: function(pseudo) {
      if(pseudo != '') {
        this.selectedUser.pseudo = pseudo;
        this.getSelectedUser();
        this.profilShowed = "true";
        document.getElementById("overlay").style.display = "block";
        document.getElementById("profile").style.right = "0px";
      }
      else {
        this.selectedUser.pseudo = '';
        this.profilShowed = "false";
        document.getElementById("overlay").style.display = "none";
        document.getElementById("profile").style.right = "-350px";
      }
    },
    getCookie: function(cname) {
      var name = cname + "=";
      var ca = document.cookie.split(';');
      for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') {
          c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
          return c.substring(name.length,c.length);
        }
      }
      return "";
    },
    setCookie: function(cname, cvalue, exdays) {
      var d = new Date();
      d.setTime(d.getTime() + (exdays*24*60*60*1000));
      var expires = "expires="+ d.toUTCString();
      document.cookie = cname + "=" + cvalue + "; " + expires;
    },
    setConnectedUser: function(pseudo) {
      var _this = this;
      fetch(apiRoot() + 'Controllers/User/getUser.php', {
        method: 'POST',
        headers: {
          'Accept': 'application/json, application/xml, text/plain, text/html, *.*',
          'Content-Type': 'application/json; charset=utf-8'
        },
        dataType: 'JSON',
        body: JSON.stringify({pseudo: pseudo})
      }).then(function(response) {
        return response.json();
      }).then(function(data){
        if(data[0] == "Error") {
          //console.log(data[1]);
        }
        else {
          _this.connectedUser.pseudo = data['pseudo'];
          _this.connectedUser.avatar = data['avatar'];
          _this.connectedUser.firstname = data['firstname'];
          _this.connectedUser.lastname = data['lastname'];
          _this.connectedUser.age = data['age'];
          _this.connectedUser.country = data['country'];
          _this.connectedUser.city = data['city'];
          _this.connectedUser.description = data['description'];
          _this.connectedUser.color = data['color'];
          _this.connectedUser.hobbies = data['hobbies'];
          _this.connectedUser.languages.learningLang = [];
          for(var i = 0; i < data['languages']['learningLang']['learningLang'].length; i ++) {
            _this.connectedUser.languages.learningLang.push(data['languages']['learningLang']['learningLang'][i]['name_langue']);
          }
          _this.connectedUser.languages.spokenLang = [];
          for(var i = 0; i < data['languages']['spokenLang']['spokenLang'].length; i ++) {
            _this.connectedUser.languages.spokenLang.push(data['languages']['spokenLang']['spokenLang'][i]['name_langue']);
          }
        }

      });
    },
    getNotifications : function(pseudo) {
      var _this = this;
      fetch(apiRoot() + 'Controllers/Notification/getAllNotif.php', {
        method: 'POST',
        headers: {
          'Accept': 'application/json, application/xml, text/plain, text/html, *.*',
          'Content-Type': 'application/json; charset=utf-8'
        },
        dataType: 'JSON',
        body: JSON.stringify({pseudo : pseudo})
      }).then(function(response) {
        return response.json();
      }).then(function(data){
        if(data[0] == "Error"){
          //console.log(data[1]);
        }
        else {
          _this.notifications = data;
        }
      });
    },
    deleteNotification : function(idNotif) {
      var _this = this;
      fetch(apiRoot() + 'Controllers/Notification/deleteNotif.php', {
        method: 'POST',
        headers: {
          'Accept': 'application/json, application/xml, text/plain, text/html, *.*',
          'Content-Type': 'application/json; charset=utf-8'
        },
        dataType: 'JSON',
        body: JSON.stringify({ID : idNotif})
      }).then(function(response) {
        return response.json();
      }).then(function(data){
        if(data[0] == "Error"){
          //console.log(data[1]);
        }
        else {
          _this.notifications = data["notifications"];
        }
      });
    },
    addNotification : function(pseudo1, pseudo2) {
      var _this = this;
      fetch(apiRoot() + 'Controllers/Notification/addNotif.php', {
        method: 'POST',
        headers: {
          'Accept': 'application/json, application/xml, text/plain, text/html, *.*',
          'Content-Type': 'application/json; charset=utf-8'
        },
        dataType: 'JSON',
        body: JSON.stringify({pseudo1 : pseudo1, pseudo2 : pseudo2, id_notif : 1})
      }).then(function(response) {
        return response.json();
      }).then(function(data){
        if(data[0] == "Error"){
          //console.log(data[1]);
        }
        else {
          _this.notifications = data["notifications"];
        }
      });
    },
    acceptConversation : function(pseudo, idNotif) {
      this.createConversation(pseudo, idNotif);
      this.getNotifications(this.connectedUser.pseudo);
    },
    refuseConversation : function(idNotif) {
      this.deleteNotification(idNotif);
      this.getNotifications(this.connectedUser.pseudo);
    },
    createConversation : function(pseudo, idNotif) {
      var _this = this;
      var pseudo_conv = [_this.connectedUser.pseudo, pseudo];
      fetch(apiRoot() + 'Controllers/Conversation/createNewConversation.php', {
        method: 'POST',
        headers: {
          'Accept': 'application/json, application/xml, text/plain, text/html, *.*',
          'Content-Type': 'application/json; charset=utf-8'
        },
        dataType: 'JSON',
        body: JSON.stringify({pseudo_conv : pseudo_conv})
      }).then(function(response) {
        return response.json();
      }).then(function(data){
        if(data[0] == "Error"){
          //console.log(data[1]);
        }
        else {
          _this.deleteNotification(idNotif);
          _this.getNotifications(_this.connectedUser.pseudo);
        }
      });
    },
    getLightColor(color){
      if (color == "#6A91C9") {
        return "#D0DBF3";
      }
      if (color == "#BA232A") {
        return "#E19296";
      }
      if (color == "#3AAB3C") {
        return "#ABFF97";
      }
      else {
        return "fff";
      }
    },
  }
}
</script>

<style lang="scss">
  @import 'assets/scss/reset.css';
  @import 'assets/scss/design.scss';

  #app
  {
    overflow-x: hidden;
    min-height: 100vh;
  }

</style>
