import { shallow, createLocalVue,  mount } from '@vue/test-utils'

import sinon from 'sinon'

import Vue from 'vue'

import Vuex from 'vuex'

import VTooltip from 'v-tooltip'
   
Vue.use(VTooltip);

import AssetDetails from '../../../../views/js/components/Asset/View/AssetDetails.vue';

import moxios from 'moxios';

import * as extraLogics from "helpers/extraLogics";

let localVue = createLocalVue()

localVue.use(Vuex)

describe('AssetDetails',() => {

	let wrapper;

	let store;

	let getters;

	let actions;

	getters = {

		formattedTime: () => () => {return ''}
	}

	store = new Vuex.Store({ getters })

	const updateWrapper = () =>{

		wrapper = mount(AssetDetails,{

			stubs: ['asset-actions','asset-description','loader'],
			
			mocks:{
			
				trans:(string)=>string
			},

			methods : {

				basePath : jest.fn()
			},

			propsData : { assetId : 1 },

			attachToDocument: true,

			localVue, store
		})  
	}
	
	beforeEach(() => {
		
		updateWrapper();
		
		moxios.install();
	})

	afterEach(() => {
		
		moxios.uninstall()
	})
	
	it("makes an API Call when `getData` method called",(done)=>{

		wrapper.vm.getData();

		mockGetRequest();

		setTimeout(()=>{

			expect(wrapper.vm.assetData).not.toEqual('');

			expect(wrapper.vm.loading).toEqual(false);

			done();
		})
	});

	it("makes `loading` value as false when api returns error response",(done)=>{

		wrapper.vm.getData();

		mockGetRequest(400);

		setTimeout(()=>{

			expect(wrapper.vm.assetData).toEqual('');

			expect(wrapper.vm.loading).toEqual(false);
				
			done();
		})
	});

	it('returns only 15 characters if value length is more than 15 and returns exact value if option length not more than 15',() => {		
		
		expect(wrapper.vm.subString('name name name name name ')).toEqual('name name name ...');

		expect(wrapper.vm.subString('name')).toEqual('name');
	});

	it("`showModal` should be `false` when `onClose` method called",()=>{

		updateWrapper();

		wrapper.vm.onClose();

		expect(wrapper.vm.showDescription).toBe(false);
	});
	
	function mockGetRequest(status = 200, url = '/service-desk/api/asset/1') {

		moxios.uninstall();
	  
	  	moxios.install();

		moxios.stubRequest(url,{

			status: status,
	    
	    	response : { data : { 
	    		id : 1, name : 'name',
	    		organization : { id : 1, name : 'name'}, 
	    		asset_status : { id : 1, name : 'name'}, 
	    		managed_by : { id : 1, full_name : 'name'}, 
	    		used_by : { id : 1, full_name : 'name'}, 
	    		product : { id : 1, name : 'name'}, 
	    		asset_type : { id : 1, name : 'name'}, 
	    		department : { id : 1, name : 'name'}, 
	    		impact_type : { id : 1, name : 'name'}, 
	    		location : { id : 1, name : 'name'}, 
	    		attachments : [],
	    		custom_field_values_for_asset_form_builder_only : [],
	    		custom_field_values_for_asset_type : [],
	    		custom_field_values_for_department : []
	    	} }
		})
	}
})