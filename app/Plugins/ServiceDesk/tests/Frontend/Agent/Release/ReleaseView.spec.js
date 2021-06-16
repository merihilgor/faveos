import { shallow, createLocalVue,  mount } from '@vue/test-utils'

import sinon from 'sinon'

import Vue from 'vue'

import ReleaseView from '../../../../views/js/components/Agent/Release/ReleaseView.vue';

import moxios from 'moxios';

import * as extraLogics from "helpers/extraLogics";

jest.mock('helpers/responseHandler');

window.eventHub = new Vue();

describe('ProblemView',() => {

	let wrapper;

	const updateWrapper = () =>{

		extraLogics.getIdFromUrl = () =>{return 1}
		
		wrapper = mount(ReleaseView,{

			stubs: ['release-details','release-associates','alert','loader'],
			
			mocks:{
			
				lang:(string)=>string
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

	it("calls `getValues` method when `refreshData` method called",()=>{

		wrapper.vm.getValues = jest.fn();

		wrapper.vm.refreshData();

		expect(wrapper.vm.getValues).toHaveBeenCalled();
	});

	it("calls `getValues` method when `updateAssociates` method called",()=>{
		
		wrapper.vm.getValues = jest.fn();

		wrapper.vm.updateAssociates();

		expect(wrapper.vm.getValues).toHaveBeenCalled();
	});

	it('makes an API call', (done) => {
			
		updateWrapper();
			
		wrapper.vm.getValues(1);
		
		stubRequest();

		setTimeout(()=>{
			
			expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/release/1')
			
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

  function stubRequest(status = 200,url = '/service-desk/api/release/1'){
	  
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