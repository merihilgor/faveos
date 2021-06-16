import {  mount } from '@vue/test-utils'

import Vue from 'vue'

import ContractActionModal from '../../../../../../../views/js/components/Agent/Contract/View/Mini/Child/ContractActionModal.vue';

import moxios from 'moxios';

import * as extraLogics from "helpers/extraLogics";

jest.mock('helpers/responseHandler');

window.eventHub = new Vue();

describe('ContractActionModal',() => {

	let wrapper;

	const updateWrapper = () =>{

		wrapper = mount(ContractActionModal,{

			stubs: ['loader','modal','ckeditor'],
			
			mocks:{ trans:(string)=>string },

			propsData : { contractId : 1, type : 'reject', onClose : jest.fn(), updateData : jest.fn() },
		})  
	}
	
	beforeEach(() => {
		
		updateWrapper();
		
		moxios.install();
	})

	afterEach(() => {
		
		moxios.uninstall()
	})

	it('updates `statusDisabled` value when onChange method is called',()=>{

		wrapper.vm.onChange('description', 'reason');

		expect(wrapper.vm.reason).toEqual('description');

		expect(wrapper.vm.isDisabled).toEqual(false);
	})

	it('makes an API call', (done) => {

		updateWrapper();
		
		wrapper.vm.afterAction = jest.fn();

		wrapper.vm.onSubmit();

		expect(wrapper.vm.loading).toBe(true)

		expect(wrapper.vm.isDisabled).toBe(true)

		stubRequest();

		setTimeout(()=>{
				
			expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/contract-reject/1')

			expect(wrapper.vm.afterAction).toHaveBeenCalled()

			done();
		},50)
	})

	it("updates `data` values if api returns error response",(done)=>{

		updateWrapper();
		
		wrapper.vm.afterAction = jest.fn();
		
		wrapper.vm.onSubmit();

		expect(wrapper.vm.loading).toBe(true)

		expect(wrapper.vm.isDisabled).toBe(true)

		stubRequest(400);

		setTimeout(()=>{
				
			expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/contract-reject/1')

			expect(wrapper.vm.afterAction).toHaveBeenCalled()

			done();
		},50)
	})

	it("calls `updateData and onClose` methods when `afterAction` method called",()=>{

		updateWrapper();

		wrapper.vm.afterAction();

		expect(wrapper.vm.updateData).toHaveBeenCalled();

		expect(wrapper.vm.onClose).toHaveBeenCalled();

		expect(wrapper.vm.loading).toEqual(false);

		expect(wrapper.vm.isDisabled).toEqual(true);
	})

	function stubRequest(status = 200,url = '/service-desk/api/contract-reject/1'){
	  
	  moxios.uninstall();
	  
	  moxios.install();
	  
	  moxios.stubRequest(url,{
	    
	    status: status,
	    
	    response : {}
	  })
	}
})