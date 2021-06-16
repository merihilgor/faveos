<template>
  <div class="dropdown" style="display:inline-block;margin-left:10px;">
    <button class="btn btn-default dropdown-toggle sortval" type="button" data-toggle="dropdown"style="background-color: #fff;">{{ lang('sort-by') }}
    <span class="caret"></span></button>
    <ul class="dropdown-menu" style="z-index:9999;">
      <li class="dropdown-header">{{ lang('sorting Menu') }}</li>
      <li v-for="(sorting,index) in sortingMenu">
        <a id="sort" href="#" @click="sort(sorting,index)">
          <i :class="sorting.class" aria-hidden="true"></i> {{lang('sort-by')}} {{sorting.name}}
        </a>
      </li>
    </ul>
  </div>
</template>
<script>
  export default{
  props:['name1','value1','name2','value2'],
  data(){
    return{
      sortingMenu:
      [{
          name:this.name1,
          value:this.value1,
          class:'fa fa-sort'
        },
        {
          name:this.name2,
          value:this.value2,
          class:'fa fa-sort'
      }]
    }
  },
  created(){
    if(this.name2 == undefined){
      this.sortingMenu=
      [{
          name:this.name1,
          value:this.value1,
          class:'fa fa-sort'
        }]
    }
  },
  methods:{
  //sorting function
    sort(x,y){
      this.$emit('sort', x);
    }
  }
}
</script>