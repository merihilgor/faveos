<style scoped>
.textarea {
  height: 200px;
  font-size: 14px;
  line-height: 18px;
  border: 1px solid #dddddd;
  padding: 10px;
  resize: none;
}
.title-label {
  margin-bottom: 16px;
  font-size: 21px;
  font-weight: 200;
  line-height: 24px;
}
.top-sapce {
  margin-top: 20px;
}
</style>
<template>
	<div>
  <!-- CONTAINER -->
  <div class="container-fluid">
      <!-- HEADR -->
      <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 colxs-12">
          <div class="form-group">
            <h4 class="text-capitalize">
                <span >{{lang('internal_notes')}}</span>
            </h4>
          </div>
      </div>
      </div>
      <!-- HEADR END -->

      <!-- TEXT AREA -->
      <div class="row">
        <div class="col-lg-12 col md-12 col-xs-12 col-sm-12">
          <textarea name="internal_note" class="form-control textarea" placeholder="Add your internal notes"  v-model="obj.internal_notes"></textarea>  
        </div>
      </div>
      <!-- TEXT AREA END -->
  </div>
   <!-- CONTAINER END -->
	</div>
</template>
<script>
export default {
  props: {
    obj: { type: Object, default: null }
  },
  data() {
    return {
      internal_note: ""
    };
  }
};
</script>
