import { mount, createLocalVue, shallowMount } from '@vue/test-utils';

import AssetIndex from '../../../views/js/components/Asset/AssetIndex.vue';

import sinon from 'sinon'

import Vue from 'vue'

window.eventHub = new Vue();

let wrapper;

const mockRequestData = {
  'success': true,
  'data': {
  	'assets' : [
  		{ id : 1, name : 'asset', created_at :'2019-05-29 04:52:25',updated_at :'2019-05-29 04:52:25',
  			managed_by : { id : 1, name : 'Sakthi', full_name : 'Sakthi viji'}, used_by : { id : 1, name : 'Sakthi', full_name : 'Sakthi viji'}
  		}
  	]
  }
}


function getAssetListAPI() {
  moxios.stubRequest('/service-desk/api/asset-list',{
    status: 200,
    response: mockRequestData
  })
}

describe('AssetIndex', () => {

	beforeEach(()=>{

		wrapper = mount(AssetIndex,{
			
			stubs:['data-table','data-table-actions','alert','asset-filter'],
		   
		  mocks:{ lang: (string) => string },

		  methods : {

        basePath : jest.fn(),
        
		  }
		})
	})

	it('is a vue instance', () => {
	  
	  expect(wrapper.isVueInstance()).toBeTruthy()
	});

	it('data-table should exists when page created', () => {
    
    expect(wrapper.find('data-table-stub').exists()).toBe(true)
  });

  it('`managed_by` should return `---` if `managed_by` value empty', () => {
  	
  	const result = wrapper.vm.options.templates.managed_by('','');
  	
  	expect(result).toEqual('---');
  })

  it('`used_by` should return `---` if `used_by` value empty', () => {
  	
  	const result = wrapper.vm.options.templates.used_by('','');
  	
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

  it("`responseAdapter` set edit_url, delete_url and view_url to the data property", () => {

    let responseAdpData = {
      "data": {
        "data": {
          "assets": [
            {"delete_url": "something","view_url": "something","edit_url": "something", "id": 1},
          ],
          "total": 1
        }
      }
    }
    let responseAdpDataReturn = 
      {"count": 1, "data": [
      	{"delete_url": "undefined/service-desk/api/asset-delete/1", "edit_url": "undefined/service-desk/assets/1/edit", "id": 1, "view_url": "undefined/service-desk/assets/1/show"}, 
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

  it("function `resetFilter` will set the url to default", (done) => {
    
    wrapper.vm.filterOptions = []
    
    setTimeout(()=>{
    
      expect(wrapper.vm.apiUrl).toBe("/service-desk/api/asset-list")
    
      done()
    },1)
  })

  it("updates `customFields` value when `updateFilterFields` method called",()=>{

      wrapper.vm.updateFilterFields([{id:1,name:'name'}]);

      expect(wrapper.vm.customFields).toEqual([{id:1,name:'name'}])
  })

  it("`toggleFilterView` should toggle the isShowFilter value", () => {
    
    wrapper.vm.isShowFilter = false
    
    wrapper.vm.toggleFilterView()
    
    expect(wrapper.vm.isShowFilter).toBe(true)
  })

  it("set 0th index of `filterOptions` to value.asset_ids if not null", () => {
   
    let value = {
      "asset_ids": [{
        "id": 1,
        "name": "asset"
      }]
    }
    wrapper.vm.selectedFilters(value);

    expect(wrapper.vm.filterOptions[0].section[0].value).toEqual(value.asset_ids)
    
    expect(wrapper.vm.isShowFilter).toBe(false);
  })

  it("redirects to barcode generation page when print labels button is clicked.",() => {
    wrapper.setData({assetIds:[1,2,3]});
    wrapper.vm.redirect = jest.fn();
    let button = wrapper.find('.label-btn');
    button.trigger('click')
    expect(wrapper.vm.redirect).toHaveBeenCalledWith("/service-desk/generate-barcode?ids[0]=1&ids[1]=2&ids[2]=3&")
  })
})
