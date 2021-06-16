<template>
    <div id="associated-problem"  v-if="showAssociatedProblem">
      <div class="col-md-6 info-row">
        <div class="col-md-6"><label>{{ lang('associated_problem') }}</label></div>
        <div class="col-md-6">
          <a id="associated-problem-link" target="_blank" :href="associatedProblem.redirect_to" :title="associatedProblem.name">
            {{associatedProblem.name}}
          </a>
        </div>
      </div>
    </div>
</template>

<script>

export default {
  name : "associated-problem",

  description : 'Shows associated problem related to the ticket',

  props : {
    data: {type: String|Object, required: true}
  },

  computed : {
    showAssociatedProblem(){
      return (JSON.parse(this.data).associated_problem != null);
    },

    associatedProblem(){
      return JSON.parse(this.data).associated_problem;
    }
  }
}


</script>

<style scoped>
  .info-row{
    border-top: 1px solid #f4f4f4; padding: 10px;
  } 
</style>
