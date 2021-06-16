import { mount, createLocalVue, shallowMount } from '@vue/test-utils';

import ProblemIndex from '../../../views/js/components/Problem/ProblemIndex.vue';

import sinon from 'sinon'

import Vue from 'vue'

window.eventHub = new Vue();

let wrapper;

const mockRequestData = {
  'success': true,
  'data': {}
}


function getAssetListAPI() {
  moxios.stubRequest('/service-desk/api/problem-list',{
    status: 200,
    response: mockRequestData
  })
}

describe('ProblemIndex', () => {

	beforeEach(()=>{

		wrapper = mount(ProblemIndex,{
			
			stubs:['data-table','data-table-actions','alert','problem-filter'],
		   
		  mocks:{ lang: (string) => string },

		  methods : {

		  	basePath : jest.fn()
		  }
		})
	})

	it('is a vue instance', () => {
	  
	  expect(wrapper.isVueInstance()).toBeTruthy()
	});

	it('data-table should exists when page created', () => {
    
    expect(wrapper.find('data-table-stub').exists()).toBe(true)
  });

  it('`from` should return `---` if `requester` value nulll', () => {
  	
  	const result = wrapper.vm.options.templates.requester('','');
  	
  	expect(result).toEqual('---');
  })

   it('`from` should return `---` if `assignedTo` value nulll', () => {
    
    const result = wrapper.vm.options.templates.assignedTo('','');
    
    expect(result).toEqual('---');
  })

  it('`department` should return `---` if `department` value empty', () => {
  	
  	const result = wrapper.vm.options.templates.department('','');
  	
  	expect(result).toEqual('---');
  });

  it('`status_type_id` should return `---` if `status_type_id` value empty', () => {
    
    const result = wrapper.vm.options.templates.status_type_id('','');
    
    expect(result).toEqual('---');
  });

  it('`status_type_id` should return value if `status_type_id` value not equal empty', () => {
    
    const result = wrapper.vm.options.templates.status_type_id('',{status : { id : 1, name :'Open'}});
    
    expect(result).toEqual('Open');
  });

   it('`priority` should return `---` if `priority` value empty', () => {
    
    const result = wrapper.vm.options.templates.priority('','');
    
    expect(result).toEqual('---');
  });

  it('`priority` should return value if `priority` value not equal empty', () => {
    
    const result = wrapper.vm.options.templates.priority('',{priority : { id : 1, priority :'priority'}});
    
    expect(result).toEqual('priority');
  });

  it("requestAdapter method should return `sort-field`, `sort-order`, `search-query`, `page` & `limit`", () => {
    let reqAdptData = {
      "orderBy": "id",
      "ascending": true,
      "query": "something",
      "page": 10,
      "limit": 10
    }
    let reqAdptDataReturn = {
      "sort-field": "id",
      "sort-order": "desc",
      "search-query": "something",
      "page": 10,
      "limit": 10
    }
    expect(wrapper.vm.options.requestAdapter(reqAdptData)).toEqual(reqAdptDataReturn)
  });

  it("`responseAdapter` set edit_url, delete_url and view_url to the data property", () => {

    let responseAdpData = {
      "data": {
        "data": {
          "problems": [
            {"delete_url": "something","view_url": "something","edit_url": "something", "id": 1, "active" : 1, "is_edit" : true, "is_delete" : true},
          ],
          "total": 1
        }
      }
    }
    let responseAdpDataReturn = 
      {"count": 1, "data": [
        {"active": "active","is_edit" : true, "is_delete" : true, "delete_url": "undefined/service-desk/api/problem-delete/1", "edit_url": "undefined/service-desk/problem/1/edit", "id": 1, "view_url": "undefined/service-desk/problem/1/show"}
      ]}
      expect(wrapper.vm.options.responseAdapter(responseAdpData)).toEqual(responseAdpDataReturn)
  });

  it("`isShowFilter` will false if function `selectedFilters` triggered with closeEvent", () => {
  
    wrapper.vm.selectedFilters("closeEvent")
  
    expect(wrapper.vm.isShowFilter).toBe(false)
  });

  it("call resetFilter function if `selectedFilters` is triggered with resetEvent", (done) => {
    
    wrapper.vm.selectedFilters("resetEvent")
    
      setTimeout(()=>{
    
        expect(wrapper.vm.isShowFilter).toBe(false)
    
        done();
      },1)
  })

  it("function `resetFilter` will set the value to blank", (done) => {
    
    wrapper.vm.filterOptions = [{section : [{value: "something"}]},{section : [{value: "something"}]},]
    
    wrapper.vm.selectedFilters("resetEvent")
    
    setTimeout(()=>{
    
    	expect(wrapper.vm.filterOptions[0].section[0].value).toBe("")
    
      expect(wrapper.vm.apiUrl).toBe("/service-desk/api/problem-list")
    
    	done()
    },1)
  })

  it("function `resetFilter` will set the url to default", (done) => {
    
    wrapper.vm.filterOptions = []
    
    setTimeout(()=>{
    
      expect(wrapper.vm.apiUrl).toBe("/service-desk/api/problem-list")
    
      done()
    },1)
  })

  it("`toggleFilterView` should toggle the isShowFilter value", () => {
    
    wrapper.vm.isShowFilter = false
    
    wrapper.vm.toggleFilterView()
    
    expect(wrapper.vm.isShowFilter).toBe(true)
  })

  it("set 0th index of `filterOptions` to value.asset_ids if not null", () => {
   
    let value = {
      "problem_ids": [{
        "id": 1,
        "name": "problem"
      }]
    }
    wrapper.vm.selectedFilters(value);

    expect(wrapper.vm.filterOptions[0].section[0].value).toEqual(value.problem_ids)
    
    expect(wrapper.vm.isShowFilter).toBe(false);
  })
})