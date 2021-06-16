import { shallow, createLocalVue,  mount,shallowMount } from '@vue/test-utils'

import Vue from 'vue'

import ProductCreateEdit from '../../../../views/js/components/Admin/Products/ProductCreateEdit.vue';

import moxios from 'moxios';

import * as extraLogics from "helpers/extraLogics";

import * as validation from "../../../../views/js/validator/productValidation.js";

jest.mock('helpers/responseHandler')

describe('ProductCreateEdit',() => {

	let wrapper;

	const updateWrapper = () =>{

		extraLogics.getIdFromUrl = () =>{return 1}

			wrapper = mount(ProductCreateEdit,{

			stubs: ['text-field','dynamic-select','radio-button','ck-editor','custom-loader','alert'],

			mocks:{	lang:(string)=>string },

			methods : { 
				
				redirect : jest.fn()
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

	it("updates `title value when page is in edit`",()=>{
		
		updateWrapper()

		wrapper.vm.getInitialValues = jest.fn();

		wrapper.vm.getValues('/10/edit');

		expect(wrapper.vm.$data.title).toEqual('edit_product');

		expect(wrapper.vm.hasDataPopulated).toEqual(false);

		expect(wrapper.vm.getInitialValues).toHaveBeenCalled();
	});

	it("updates `hasDataPopulated` value as true when page is in create`",()=>{
		
		updateWrapper()

		wrapper.vm.getValues('/create');

		expect(wrapper.vm.$data.title).toEqual('create_new_product');

		expect(wrapper.vm.hasDataPopulated).toEqual(true);

		expect(wrapper.vm.loading).toEqual(false);
	});

	it('makes an API call when `getInitialValues` method called', (done) => {
		
		updateWrapper();

		wrapper.vm.updateStatesWithData = jest.fn();
		
		wrapper.vm.getInitialValues(1);
		
		stubRequest();
		
		setTimeout(()=>{
		
			expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/product/1')
				
			expect(wrapper.vm.hasDataPopulated).toEqual(true);

			expect(wrapper.vm.loading).toEqual(false);

			expect(wrapper.vm.product_id).toEqual(1);

			expect(wrapper.vm.updateStatesWithData).toHaveBeenCalled()
				
			done();
		
		},1)
	})

	it('makes `loading` as `false` when API call in `getInitialValues` method returns error', (done) => {
		
		updateWrapper();
		
		wrapper.vm.getInitialValues(1);
		
		stubRequest(400);
		
		setTimeout(()=>{
		
			expect(wrapper.vm.loading).toEqual(false);
		
			done();
		
		},1)
	})

	it('updates state data correctly when `updateStatesWithData` is called',() => {
		
		var data = { id : 1,name :'test',procurement:{id:1,name:'proc'},asset_type:{id:1,name:'asset'}}
		
		wrapper.vm.updateStatesWithData(data);
		
		expect(wrapper.vm.name).toEqual('test');
		
		expect(wrapper.vm.product_proc_mode).toEqual({id:1,name:'proc'});

		expect(wrapper.vm.assetType).toEqual({id:1,name:'asset'});
	});

	it('isValid - should return false ', done => {
				
		validation.validateProductSettings = () =>{return {errors : [], isValid : false}}
			
		expect(wrapper.vm.isValid()).toBe(false)
			
		done()
	})

	it('isValid - should return true ', done => {
			 
		validation.validateProductSettings = () =>{return {errors : [], isValid : true}}
			
		expect(wrapper.vm.isValid()).toBe(true)
			
		done()
	})

	it('updates `name` of the Product when onChange method is called with suitable parameters',()=>{

		wrapper.vm.onChange('test', 'name');

		expect(wrapper.vm.name).toBe('test');
	})

	it('updates `value` as empty when value comes as `null`',()=>{

		wrapper.vm.onChange(null, 'assetType');

		expect(wrapper.vm.assetType).toBe('');
	})

	it('makes an AJAX call when onSubmit method is called',(done)=>{

		updateWrapper()

		wrapper.vm.redirect = jest.fn();

		wrapper.setData({ loading : false, hasDataPopulated : true})

		wrapper.vm.isValid = () =>{return true}

		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)

		mockSubmitRequest();

		setTimeout(()=>{

			expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/product')

			expect(wrapper.vm.loading).toBe(false)

			expect(wrapper.vm.redirect).toHaveBeenCalled()

			done();
		},1);
	});

	it('calls `getInitialValues` method when updating Product in onSubmit method',(done)=>{

		updateWrapper()

		wrapper.vm.getInitialValues = jest.fn();

		wrapper.setData({ loading : false, hasDataPopulated : true,product_id:1})

		wrapper.vm.isValid = () =>{return true}

		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)

		mockSubmitRequest();

		setTimeout(()=>{

			expect(wrapper.vm.getInitialValues).toHaveBeenCalled()

			done();
		},1);
	});

	it('makes an loading as false when onSubmit method return error',(done)=>{

		updateWrapper()

		wrapper.setData({ loading : false, hasDataPopulated : true})

		wrapper.vm.isValid = () =>{return true}

		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)

		mockSubmitRequest(400);

		setTimeout(()=>{

			expect(wrapper.vm.loading).toBe(false)

			done();
		},1);
	});

	function mockSubmitRequest(status = 200,url = '/service-desk/api/product'){

		moxios.uninstall();

		moxios.install();

		moxios.stubRequest(url,{

			status: status,

			response: {'success':true,'message':'successfully saved'}
		})
	}

	function stubRequest(status = 200,url = '/service-desk/api/product/1'){
		
		moxios.uninstall();
		
		moxios.install();
		
		moxios.stubRequest(url,{
			
			status: status,
			
			response : {
				data : {
					product : {
						id : 1,
						name : 'test'
					}
				}
			}
		})
	}
})