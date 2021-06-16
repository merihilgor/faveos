<template>
    
    <div v-if="siteKey">

      <vue-recaptcha :ref="version == 'v2' ? 'recaptcha' : 'invisibleRecaptcha'"
         name="form-recaptcha" 
         v-model="recaptchaVerified" 
        @verify="markRecaptchaAsVerified"  
        @expired="onExpired"
         v-validate="version == 'v2' ? 'required' : ''"
         :sitekey="siteKey ? siteKey : ''"
         :size="version == 'v2' ? 'normal' : 'invisible'">
      </vue-recaptcha>
      
      <span v-show="errors.has('form-recaptcha')" class="help is-danger">
        {{ errors.first('form-recaptcha') }}
      </span>
    </div>
</template>
<script>

import VueRecaptcha from "vue-recaptcha";

export default {
  
  name : 'recaptcha-field',

  description : 'Recaptcha Field component',

  props: {

    siteKeyValue : { type : String, default : '' },

    captchaVersion : { type : String, default : '' },
    
    from : { type : String, default : ''},

    node: {type: Object, default : ()=> {}},

    category : {type: String, default: 'ticket'},

    verifyCaptcha : { type : Function, default :()=>{} }
  },

  data() {
    
    return {
    
      recaptchaVerified : "",
    }
  },

  components: {
    
    VueRecaptcha
  },

  created(){
    
    window.eventHub.$on(this.category+'FormSubmitted', ()=>{
    
      this.$emit("assignToModel", 'value', "");
    
      window.grecaptcha.reset();
    })
  },

  mounted() {

      if(this.version != 'v2' && this.siteKey)
      {
        this.$refs.invisibleRecaptcha.execute();
      }
  },

  computed: {

    siteKey() {

      return this.siteKeyValue ? this.siteKeyValue : this.recaptchaSiteKey
    },

    version() {

      return this.captchaVersion ? this.captchaVersion : this.recaptchaVersion
    }
  },

  methods: {

    markRecaptchaAsVerified(response) {
      
      this.recaptchaVerified = response;

      this.$emit("assignToModel", 'value',this.recaptchaVerified);

      this.verifyCaptcha(response);
    },

    onExpired() {

      this.verifyCaptcha('');
    }
  }
};
</script>
