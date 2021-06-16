import {  mount, createLocalVue } from '@vue/test-utils'

import Vue from 'vue'

import Vuex from 'vuex'

import ContractUpdateModal from '../../../../../../../views/js/components/Agent/Contract/View/Mini/Child/ContractUpdateModal.vue';

import moxios from 'moxios';

import * as extraLogics from "helpers/extraLogics";

jest.mock('helpers/responseHandler');

import * as validation from "../../../../../../../views/js/validator/extendContractValidation";

let localVue = createLocalVue()

localVue.use(Vuex)

window.eventHub = new Vue();

describe('ContractUpdateModal',() => {

	let wrapper;

	let getters = {

		formattedTime: () => () => {return ''}
	}
  
  	let store

  	store = new Vuex.Store({ getters })

	const updateWrapper = () =>{

		wrapper = mount(ContractUpdateModal,{

			stubs: ['loader','modal','number-field','dynamic-select','date-time-field'],
			
			mocks:{ trans:(string)=>string },

			propsData : { contract :{ id: 1 }, type : 'extend', onClose : jest.fn(), updateData : jest.fn() },

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

	it('updates `approver` value when onChange method is called',()=>{

		wrapper.vm.onChange({id:1, name:'approver'}, 'approver');

		expect(wrapper.vm.approver).toEqual({id:1, name:'approver'});
	});

	it('isValid - should return false ', done => {
				
		validation.validateExtendContractSettings = () =>{return {errors : [], isValid : false}}
			
		expect(wrapper.vm.isValid()).toBe(false)
			
		done()
	})

	it('isValid - should return true ', done => {
			 
		validation.validateExtendContractSettings = () =>{return {errors : [], isValid : true}}
			
		expect(wrapper.vm.isValid()).toBe(true)
			
		done()
	})

	it('makes an AJAX call when onSubmit method is called',(done)=>{

		updateWrapper();

		wrapper.setData({ 
			approver : {id:1,name:'name'},
			cost : 10,
		})

		wrapper.vm.isValid = () =>{return true}

		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)

		expect(wrapper.vm.isDisabled).toBe(true)

		mockSubmitRequest();

		setTimeout(()=>{

			expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/contract-extend/1')

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
			approver : {id:1,name:'name'},
			cost : 10,
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

	function mockSubmitRequest(status = 200,url = '/service-desk/api/contract-extend/1'){

		moxios.uninstall();

		moxios.install();

		moxios.stubRequest(url,{

			status: status,

			response: {'success':true,'message':'successfully saved'}
		})
	}
})