<template>
  <div class="language-menu dropdown">
    <div class="dropdown btn-group">
      <button class="btn btn-default dropdown-toggle" type="button" id="language-list-dropdown"
        data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" @click="getLanguageList()">
        {{ lang('add_new_language') }}
        <span class="caret"></span>
      </button>
      <ul class="dropdown-menu" aria-labelledby="language-list-dropdown">
        <li v-if="isLoading">
          <relative-loader :size="30" />
        </li>
        <li v-for="lang in languageList" :key="lang.id" v-else>
          <a href="javascript:void(0)" @click="changeFlag(lang.locale)" >
            <img :src='getLocalFlag(lang.locale)'>&nbsp;{{lang.name}}
          </a>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>

import axios from 'axios';

export default {
  name: 'language-dropdown',

  data() {
    return {
      languageList: [],
      isLoading: false
    }
  },

  components: {
    'relative-loader': require('components/Extra/Loader'),
  },

  methods: {
    //getting language
    getLanguageList() {
      this.isLoading = true;
      axios.get('api/dependency/languages?meta=true')
        .then((resp) => {
          this.languageList = resp.data.data.languages.sort((a, b) => {
            if (a.name < b.name) {
              return -1;
            }
            if (b.name < a.name) {
              return 1;
            }
            return 0;
          });
        })
        .catch((err) => {
          this.languageList = [];
        })
        .finally(()=> {
          this.isLoading = false;
        })
    },

    //get flag image by language name
    getLocalFlag(x) {
      return this.basePath() + '/themes/default/common/images/flags/' + x + '.png'
    },

    //Emit event to change language/flag
    changeFlag(newLang) {
      let languageFlag = {};
      languageFlag['language'] = newLang;
      this.$emit('getflag', languageFlag);
    },
  }

};
</script>

<style lang="css" scoped>
.open>.dropdown-menu {
    height: 16vh;
    display: block;
    overflow-y: auto;
}
.language-menu {
    margin-bottom: 0.5rem;
}
</style>
