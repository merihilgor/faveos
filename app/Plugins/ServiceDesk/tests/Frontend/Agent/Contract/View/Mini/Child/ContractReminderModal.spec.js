import {  mount } from '@vue/test-utils'

import Vue from 'vue'

import ContractReminderModal from '../../../../../../../views/js/components/Agent/Contract/View/Mini/Child/ContractReminderModal.vue';

import moxios from 'moxios';

import * as extraLogics from "helpers/extraLogics";

jest.mock('helpers/responseHandler');

import * as validation from "../../../../../../../views/js/validator/contractReminderValidation";

window.eventHub = new Vue();

describe('ContractReminderModal',() => {

	let wrapper;

	const updateWrapper = () =>{

		wrapper = mount(ContractReminderModal,{

			stubs: ['loader','modal','number-field','dynamic-select'],
			
			mocks:{ trans:(string)=>string },

			propsData : { contract :{ id: 1 }, type : 'reject', onClose : jest.fn(), updateData : jest.fn() },
		})  
	}
	
	beforeEach(() => {
		
		updateWrapper();
		
		moxios.install();
	})

	afterEach(() => {
		
		moxios.uninstall()
	})

	it('updates `notifyBefore` value when onChange method is called',()=>{

		wrapper.vm.onChange(10, 'notifyBefore');

		expect(wrapper.vm.notifyBefore).toEqual(10);
	});

	it('isValid - should return false ', done => {
				
		validation.validateContractReminderSettings = () =>{return {errors : [], isValid : false}}
			
		expect(wrapper.vm.isValid()).toBe(false)
			
		done()
	})

	it('isValid - should return true ', done => {
			 
		validation.validateContractReminderSettings = () =>{return {errors : [], isValid : true}}
			
		expect(wrapper.vm.isValid()).toBe(true)
			
		done()
	})

	it('makes an AJAX call when onSubmit method is called',(done)=>{

		updateWrapper();

		wrapper.setData({ 
			notify_to : [{id:1,name:'name'},{name:'email'}],
			notifyBefore : 10,
		})

		wrapper.vm.isValid = () =>{return true}

		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)

		expect(wrapper.vm.isDisabled).toBe(true)

		mockSubmitRequest();

		setTimeout(()=>{

			expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/contract-expiry-reminder/1')

			expect(wrapper.vm.loading).toBe(false);

			expect(wrapper.vm.isDisabled).toBe(false)

			expect(wrapper.vm.onClose).toHaveBeenCalled()

			expect(wrapper.vm.updateData).toHaveBeenCalled()

			done();
		},1);
	});

	it('makes an loading as false when onSubmit method return error',(done)=>{

		updateWrapper()

		wrapper.setData({ 
			notify_to : [{id:1,name:'name'},{name:'email'}],
			notifyBefore : 10,
		})

		wrapper.vm.isValid = () =>{return true}

		wrapper.vm.onSubmit()

		mockSubmitRequest(400);

		setTimeout(()=>{

			expect(wrapper.vm.loading).toBe(false)

			expect(wrapper.vm.isDisabled).toBe(false)

			done();
		},1);
	});

	function mockSubmitRequest(status = 200,url = '/service-desk/api/contract-expiry-reminder/1'){

		moxios.uninstall();

		moxios.install();

		moxios.stubRequest(url,{

			status: status,

			response: {'success':true,'message':'successfully saved'}
		})
	}
})