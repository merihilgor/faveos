import { shallow, createLocalVue,  mount } from '@vue/test-utils'

import sinon from 'sinon'

import Vue from 'vue'

import ContractView from '../../../../views/js/components/Agent/Contract/ContractView.vue';

import moxios from 'moxios';

import * as extraLogics from "helpers/extraLogics";

jest.mock('helpers/responseHandler')

window.eventHub = new Vue();

describe('ContractView',() => {

	let wrapper;

	const updateWrapper = () =>{

		extraLogics.getIdFromUrl = () =>{return 1}
		
		wrapper = mount(ContractView,{

			stubs: ['contract-details','contract-associates','alert','loader'],
			
			mocks:{
			
				trans:(string)=>string
			},
		})  
	}
	
	beforeEach(() => {
		
		updateWrapper();
		
		moxios.install();
	})

	afterEach(() => {
		
		moxios.uninstall()
	})

	it("calls `getValues` method when `updateAssociates` method called",()=>{

		updateWrapper();

		wrapper.vm.getValues = jest.fn();

		wrapper.vm.updateAssociates();

		expect(wrapper.vm.getValues).toHaveBeenCalledWith(true)
	});

	it('makes an API call', (done) => {
			
		updateWrapper();
			
		wrapper.vm.getValues(1);
		
		stubRequest();

		setTimeout(()=>{
			
			expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/contract/1')
			
			expect(wrapper.vm.$data.loading).toBe(false)

			done();
		},50)
	})

	it("makes `loading` as false if api returns error response",(done)=>{

		updateWrapper();

		wrapper.vm.getValues(1);

		stubRequest(400);

		setTimeout(()=>{

			expect(wrapper.vm.$data.loading).toBe(false)

			done();
		},50)
	})

  function stubRequest(status = 200,url = '/service-desk/api/contract/1'){
	  
	  moxios.uninstall();
	  
	  moxios.install();
	  
	  moxios.stubRequest(url,{
	    
	    status: status,
	    
	    response : {
	    	data : {}
	    }
	  })
	}
})