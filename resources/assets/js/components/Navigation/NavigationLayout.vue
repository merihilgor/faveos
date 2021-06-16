<template>
  <aside class="main-sidebar">
    <section id="sideMenu" class="sidebar" :style="sidebarStyle">
      <div class="user-panel">
          <div class="row">
              <div class="center">
                  <a id="sidebar-profile-link" :href="basePath() + '/profile'">
                    <faveo-image-element id="sidebar-profile-pic" :source-url="profilePic" :classes="['img-circle']" alternative-text="User Image"/>
                  </a>
                  <strong><p id="sidebar-fullname" :title="fullName">{{subString(fullName)}}</p></strong>
              </div>
          </div>
      </div>

      <slot></slot>
    </section>
  </aside>
</template>

<script>

  import { getSubStringValue } from 'helpers/extraLogics'

  export default {

    computed : {
        sidebarStyle(){
          if(this.isRtlLayout){
            return {'margin-right': '-8px'}
          }
          return {'margin-left': '-8px'}
        },

        fullName(){
            return sessionStorage.getItem('full_name');
        },

        profilePic(){
            return sessionStorage.getItem('profile_pic');
        }
    },

    methods : {

      subString(value){

        return getSubStringValue(value,25)
      },
    },

    components: {
      'faveo-image-element': require('components/Common/FaveoImageElement')
    }
  }
</script>

<style scoped>

#navigation-panel-label{
  font-size : 1.5em;
  font-family : inherit;
}

.center{
    text-align: center;
    color: #b8c7ce!important;
}

.fixed .sidebar {
  padding-bottom : 100px;
}

#sidebar-profile-pic{
  width: 30% !important;
  margin-top: 15px;
}

.sidebar {
  overflow-x: hidden !important;
  overflow-y: scroll !important;
}
</style>
