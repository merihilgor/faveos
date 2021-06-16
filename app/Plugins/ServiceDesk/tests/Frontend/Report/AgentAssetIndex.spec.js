import { mount, createLocalVue, shallowMount } from '@vue/test-utils';

import AgentAssetIndex from '../../../views/js/components/Report/AgentAssetIndex.vue';

import sinon from 'sinon'

import Vue from 'vue'

import Vuex from 'vuex'

import moxios from 'moxios'

let wrapper;

describe('AgentAssetIndex', () => {

	beforeEach(()=>{

    moxios.install()

    let store;

    let getters;

    getters = {
        
      formattedTime: () => () => {return ''},
        
      formattedDate:()=> () => {return ''},
    }

    store = new Vuex.Store({
    
      getters
    })

		wrapper = mount(AgentAssetIndex,{
			
			stubs:['data-table','alert'],
		   
		  mocks:{ lang: (string) => string },

      propsData : { 

        ApiUrl  : '/service-desk/api/asset-list?config=true&'
      },

		  methods : {

		  	basePath : jest.fn()
		  },

      store
		})
	})

  afterEach(() => {
    moxios.uninstall()
  })

	it('is a vue instance', () => {
	  
	  expect(wrapper.isVueInstance()).toBeTruthy()
	});

	it('data-table should exists when page created', () => {
    
    expect(wrapper.find('data-table-stub').exists()).toBe(true)
  });

  it('`asset_type` should return `--` if `asset_type` value empty', () => {
  	
  	const result = wrapper.vm.options.templates.asset_type('','');
  	
  	expect(result).toEqual('--');
  })

  it('`impact_type` should return `--` if `impact_type` value empty', () => {
    
    const result = wrapper.vm.options.templates.impact_type('','');
    
    expect(result).toEqual('--');
  })

  it('`location` should return `--` if `location` value empty', () => {
    
    const result = wrapper.vm.options.templates.location('','');
    
    expect(result).toEqual('--');
  })

   it("return row->updated_at for `updated_at` column in template option of datatable", () => {
    
    expect(wrapper.vm.options.templates.updated_at('test', {'updated_at': '2012-10-12'})).toEqual("")
  })

  it("return row->created_at for `created_at` column in template option of datatable", () => {
    
    expect(wrapper.vm.options.templates.created_at('test', {'created_at': '2012-10-12'})).toEqual("")
  })

  it("return row->assigned_on for `assigned_on` column in template option of datatable", () => {
    
    expect(wrapper.vm.options.templates.assigned_on('test', {'assigned_on': '2012-10-12'})).toEqual("")
  })

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
          "assets": [  {"id": 1} ],
          "total": 1
        }
      }
    }
    
    let responseAdpDataReturn = {count: 1,data: [ {"id": 1}]}

    expect(wrapper.vm.options.responseAdapter(responseAdpData)).toEqual(responseAdpDataReturn)
  });

  it("updates `columns` value when `getColumns` method called",(done)=>{

    wrapper.vm.getColumns();

    columnRequest();

    setTimeout(()=>{

      expect(wrapper.vm.columns).toEqual(['name'])

      done();
    },50)
  });

  it("updates `columns` value as `empty []` when api returns error response",(done)=>{

    wrapper.vm.getColumns();

    columnRequest(400);

    setTimeout(()=>{

      expect(wrapper.vm.columns).toEqual([])

      done();
    },50)
  })

  function columnRequest(status = 200,url = '/service-desk/api/asset-list?column=true'){
    
    moxios.uninstall();
    moxios.install();
    moxios.stubRequest(url,{
      status: status,
      response : {
        data : {
          asset_columns : ['name']
        }
      }
    })
  }
})
