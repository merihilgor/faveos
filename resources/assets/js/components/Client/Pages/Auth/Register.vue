<template>
  <div>
    <!-- title and meta title and description component -->
    <meta-component :dynamic_title="lang('registration-page-title')" :dynamic_description="lang('registration-page-description')" :layout="layout"></meta-component>

    <div v-show="userInfo" id="content" class="site-content col-md-12 ng-scope">
    <!-- for alert -->
      <alert/>
    <!-- end alert -->

    <div id="content" class="site-content col-md-12">

      <article class="hentry">

        <div id="form-border" class="comment-respond form-border" style="background : #fff">
              
          <section id="section-categories" class="section">
            
            <h2 class="section-title h4 clearfix mb-10">

              <i class="line" :style="lineStyle"></i>{{lang('register')}}
            </h2>

            <div class="row">
                  
              <create-form buttonClass="btn btn-custom" :buttonStyle="buttonStyle"
                :person="'user'"
                :category="'user'"
                :usedby="'client-panel'"
                submitApiEndpoint="/user/create/api/"
                :color="layout.portal.client_header_color"
              ></create-form>
            </div>
          </section>
        </div>
      </article>
    </div>
    </div>
  </div>
</template>
<script>
export default {
  components: {
    alert: require("components/MiniComponent/Alert"),
    'create-form' : require('components/Common/Form/CreateForm'),
  },

  props : {

    layout : { type : Object, default : ()=>{}},

    auth : { type : Object, default : ()=>{}}
  },

  data() {
    return {
      title: "login_to_start_your_session",
      base: window.axios.defaults.baseURL, // base url of the application
      user_data: "", // for user details

      // initial state of system
      system: null,
      /* for loader */
      loading: false,
      color: "",
      size: 50,

      // layout data
      layout_data: "",

      // for user options from admin panel
      allow_register: "",
      allow_create_ticket: "",
      // after clicking mytickets widgetbox i am storing the path in this variable
      redirectPath: "",
      // for language locale
      lang_locale: "",
      // state of kb
      status: "",

      lineStyle: {

        borderColor : this.layout.portal.client_header_color,
      },

      buttonStyle : {

        borderColor : this.layout.portal.client_button_border_color,

        backgroundColor : this.layout.portal.client_button_color,
      },
    };
  },

  beforeMount(){

    if(!this.layout.user_registration.status || !Array.isArray(this.auth.user_data)){
      
      this.$router.push('/')
    }
  },

  created() {
    // getting locale from localStorage
    this.lang_locale = localStorage.getItem("LANGUAGE");
  },

  computed: {

    /**
     * @return {Object} return user details from vuex store
     */
    userInfo() {
      if (this.$store.getters.getUserData) {
        this.userDetails(this.$store.getters.getUserData);
        return this.$store.getters.getUserData;
      }
    }
  },
  methods: {
    /**
     * getting user details from vuex store
     * @param  {Object} user data from vuex
     * @return {Void}
     */
    userDetails(user) {
      this.user_data = user.user_data;
      if (this.user_data.length != 0) {
        this.$router.push({ name: "Home" });
      }
    },

    /**
     * getting layout details from vuex store
     * @param  {Object} layout - layout details from vuex store
     * @return {Void}
     */
    layoutDetails() {
      this.layout_data = this.layout;
      this.color = this.layout_data.portal.client_header_color;
      this.allow_register = this.layout_data.user_registration.status;
      this.allow_create_ticket = this.layout_data.allow_users_to_create_ticket.status;
      this.status = this.layout_data.kb_settings.status;
      this.system = this.layout_data.system.status;
    }
  }
};
</script>
<style scoped>
#wbox {
  margin-right: -5px !important;
}
.wid {
  margin-top: 2em !important;
  margin-bottom: 1.5em !important;
}
.box-title {
    display: inline-block;
    font-size: 18px;
    margin: 5px 5px 5px 13px;
    line-height: 1;
}
</style>
