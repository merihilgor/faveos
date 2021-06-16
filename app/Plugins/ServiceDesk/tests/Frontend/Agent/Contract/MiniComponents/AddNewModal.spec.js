import { shallow, createLocalVue,  mount,shallowMount } from '@vue/test-utils'

import Vue from 'vue'

import AddNewModal from '../../../../../views/js/components/Agent/Contract/MiniComponents/AddNewModal.vue';

import moxios from 'moxios';

import * as extraLogics from "helpers/extraLogics";

jest.mock('helpers/responseHandler')

describe('AddNewModal',() => {

	let wrapper;

	const updateWrapper = () =>{

		wrapper = mount(AddNewModal,{

			stubs: ['modal','loader','vendor','contract-type'],

			mocks:{	lang:(string)=>string },

			propsData : {

				showModal : true,
				
				onClose : jest.fn(),

				createdValue : jest.fn()
			}
		})  
	}
	
	beforeEach(() => {
		 
		updateWrapper();

		moxios.install();
	})

	afterEach(() => {
		
		moxios.uninstall()
	})

	it('is vue instance',() => {		
		
		expect(wrapper.isVueInstance()).toBeTruthy()
	});

	it("calls vendor component `onSubmit` method when `name` is `vendor`",()=>{

		wrapper.setData({ loading : false})

		wrapper.setProps({ name : 'vendor'})

		wrapper.vm.$refs.addVendor.onSubmit = jest.fn()
		
		wrapper.vm.onSubmit();

		expect(wrapper.vm.$refs.addVendor.onSubmit).toHaveBeenCalled()
	})

	it("calls contractType component `onSubmit` method when `name` is `contract-type`",()=>{

		wrapper.setProps({ name : 'contract-type'})

		wrapper.vm.$refs.addContractType.onSubmit = jest.fn()
		
		wrapper.vm.onSubmit();

		expect(wrapper.vm.$refs.addContractType.onSubmit).toHaveBeenCalled()
	})

	it("Calls `getNewValue and onClose` method when `onCompleted` method called",()=>{

		wrapper.vm.getNewValue = jest.fn();

		wrapper.vm.onCompleted('value','name');

		expect(wrapper.vm.getNewValue).toHaveBeenCalled();

		expect(wrapper.vm.onClose).toHaveBeenCalled();
	})

	it("makes an API Call when `getNewValue` method called",(done)=>{

		wrapper.vm.getNewValue('value','name');

		stubRequest();

		setTimeout(()=>{

			expect(wrapper.vm.createdValue).toHaveBeenCalled()
			done()
		},1)
	})

	function stubRequest(status = 200,url = '/service-desk/api/dependency/name?search-query=value'){
		
		moxios.uninstall();
		
		moxios.install();
		
		moxios.stubRequest(url,{
			
			status: status,
			
			response : {

				data : {

					name : 'value'
				}
			}
		})
	}
})