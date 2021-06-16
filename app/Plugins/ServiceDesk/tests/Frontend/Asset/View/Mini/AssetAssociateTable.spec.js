import { mount } from '@vue/test-utils';

import AssetAssociateTable from '../../../../../views/js/components/Asset/View/Mini/AssetAssociateTable.vue';

import Vue from 'vue'

import Vuex from 'vuex'

let wrapper;

describe('AssetAssociateTable', () => {

	beforeEach(()=>{

    let store;

    let getters;

    getters = {
        
      formattedTime: () => () => {return ''},
        
      formattedDate:()=> () => {return ''},
    }

    store = new Vuex.Store({
    
      getters
    })

		wrapper = mount(AssetAssociateTable,{
			
			stubs:['data-table','asset-associate-detach'],
		   
		  mocks:{ lang: (string) => string },

		  methods : { basePath : jest.fn() },

      props : { category : 'ticket' }, store
		})
	})

	it('data-table should exists when page created', () => {
    
    expect(wrapper.find('data-table-stub').exists()).toBe(true)
  });

  it("return row->assigned for `assigned` column in template option of datatable", () => {
    
    expect(wrapper.vm.options.templates.assigned('test', { assigned : {'id': 1, full_name : 'name'}})).toEqual("name")
  })

   it("return row->created_at for `created_at` column in template option of datatable", () => {
    
    expect(wrapper.vm.options.templates.created_at('test', {'created_at': '2012-10-12'})).toEqual("")
  })

  it('`status` should return `---` if `status` value empty', () => {
    
    const result = wrapper.vm.options.templates.status('','');
    
    expect(result).toEqual('---');
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

  it("updates `associateId, showModal` values when `onDetach` method called",()=>{

    wrapper.vm.onDetach(1);

    expect(wrapper.vm.associateId).toEqual(1);

    expect(wrapper.vm.showModal).toEqual(true)
  })

  it("makes `showModal` false when `onClose` method called",()=>{

    wrapper.vm.onClose();

    expect(wrapper.vm.showModal).toEqual(false)
  })
})