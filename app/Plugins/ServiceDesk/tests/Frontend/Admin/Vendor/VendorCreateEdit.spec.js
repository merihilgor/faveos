import { shallow, createLocalVue,  mount,shallowMount } from '@vue/test-utils'

import Vue from 'vue'

import VendorCreateEdit from '../../../../views/js/components/Admin/Vendor/VendorCreateEdit.vue';

import moxios from 'moxios';

import * as extraLogics from "helpers/extraLogics";

import * as validation from "../../../../views/js/validator/validationVendorSettings";

jest.mock('helpers/responseHandler')

describe('VendorCreateEdit',() => {

	let wrapper;

	const updateWrapper = () =>{

		extraLogics.getIdFromUrl = () =>{return 1}

			wrapper = mount(VendorCreateEdit,{

			stubs: ['text-field','radio-button','ck-editor','custom-loader','alert','number-field'],

			mocks:{	lang:(string)=>string },

			methods : { 
				
				redirect : jest.fn(), 
				
				basePath : jest.fn() 
			}
		})  
	}
	
	beforeEach(() => {
		 
		updateWrapper();

		moxios.install();

		moxios.stubRequest('/service-desk/api/vendor/1',{
			
			status: 200,
			
			response: fakeResponse
		})
	})

	afterEach(() => {
		
		moxios.uninstall()
	})

	it('is vue instance',() => {		
		
		expect(wrapper.isVueInstance()).toBeTruthy()
	});

	it('makes an API call', (done) => {
		
			updateWrapper();
		
			wrapper.vm.getInitialValues(1);
		
			stubRequest();
		
			setTimeout(()=>{
		
				expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/vendor/1')
				
				expect(wrapper.vm.name).toEqual('vendor');

				expect(wrapper.vm.status).toEqual(1);
				
				done();
		
			},50)
	 })

	it('updates state data correctly(according to the key) when `updateStatesWithData` is called',() => {
		
		var data = { name :'test' }
		
		wrapper.vm.updateStatesWithData(data);
		
		expect(wrapper.vm.name).toBe('test');
	});

	it('updates `name` of the vendor when onChange method is called with suitable parameters for vendor name',()=>{

		wrapper.vm.onChange('vendor 1', 'name');

		expect(wrapper.vm.name).toBe('vendor 1');
	})

	it('makes an AJAX call when onSubmit method is called',(done)=>{

		updateWrapper()

		wrapper.setData({ loading : false, hasDataPopulated : true})

		wrapper.vm.isValid = () =>{return true}

		wrapper.vm.getDescription = () =>{return 'description'}

		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)

		mockSubmitRequest();

		setTimeout(()=>{

			expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/vendor')

			expect(wrapper.vm.loading).toBe(false)

			done();
		},1);
	});

	it('makes an loading as false when onSubmit method return error',(done)=>{

		updateWrapper()

		wrapper.setData({ loading : false, hasDataPopulated : true,vendor_id : 1})

		wrapper.vm.isValid = () =>{return true}

		wrapper.vm.getDescription = () =>{return 'description'}

		wrapper.vm.onSubmit()

		mockSubmitRequest(400);

		setTimeout(()=>{

			expect(wrapper.vm.loading).toBe(false)

			done();
		},1);
	});


	it("updates `title value when page is in edit`",()=>{
		
		updateWrapper()

		wrapper.vm.getValues('/10/edit');

		expect(wrapper.vm.$data.title).toEqual('edit-vendor')
	});

	it("makes `loading` as false if api returns error response",(done)=>{

		updateWrapper();

		updateWrapper();

		wrapper.vm.getInitialValues(1);

		stubRequest(400);

		setTimeout(()=>{

			expect(wrapper.vm.$data.loading).toBe(false)

			done();
		},50)
	})

	it('call `isValid` method when onSubmit method is called',(done)=>{

		updateWrapper()

		wrapper.setData({ loading : false, hasDataPopulated : true})

		wrapper.vm.getDescription = () =>{return 'description'}

		wrapper.vm.isValid =jest.fn()   

		wrapper.vm.onSubmit()

		expect(wrapper.vm.isValid).toHaveBeenCalled()
	 
		done();
	});

	it('isValid - should return false ', done => {
				
		validation.validateVendorSettings = () =>{return {errors : [], isValid : false}}
			
		expect(wrapper.vm.isValid()).toBe(false)
			
		done()
	})

	it('isValid - should return true ', done => {
			 
		validation.validateVendorSettings = () =>{return {errors : [], isValid : true}}
			
		expect(wrapper.vm.isValid()).toBe(true)
			
		done()
	})

	function mockSubmitRequest(status = 200,url = '/service-desk/api/vendor'){

		moxios.uninstall();

		moxios.install();

		moxios.stubRequest('/service-desk/api/vendor',{

			status: status,

			response: {'success':true,'message':'successfully saved'}
		})
	}

	function stubRequest(status = 200,url = '/service-desk/api/vendor/1'){
		
		moxios.uninstall();
		
		moxios.install();
		
		moxios.stubRequest(url,{
			
			status: status,
			
			response : {
				data : {
					vendor : {
						id : 1,
						name : 'vendor',
						status : {id:1,name:'status'}
					}
				}
			}
		})
	}


	let fakeResponse = {
		success:true,
		data : {
			data : {
				vendor : {
					name : 'test'
				}
			}
		}
	}
})