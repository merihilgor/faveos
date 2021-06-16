import { shallow, createLocalVue,  mount,shallowMount } from '@vue/test-utils'

import Vue from 'vue'

import AssetStatusCreateEdit from '../../../../views/js/components/Admin/AssetStatus/AssetStatusCreateEdit.vue';

import moxios from 'moxios';

import * as extraLogics from "helpers/extraLogics";

import * as validation from "../../../../views/js/validator/assetStatusSettings";

jest.mock('helpers/responseHandler')

describe('AssetStatusCreateEdit',() => {

	let wrapper;

	const updateWrapper = () =>{

		extraLogics.getIdFromUrl = () =>{return 1}

			wrapper = mount(AssetStatusCreateEdit,{

			stubs: ['text-field','ckeditor','custom-loader','alert'],

			mocks:{	trans:(string)=>string },

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

	it('makes an API call', (done) => {
		
		updateWrapper();
		
		wrapper.vm.getInitialValues(1);
		
		stubRequest();
		
		setTimeout(()=>{
		
			expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/asset-status/1')
				
			expect(wrapper.vm.name).toEqual('test');
				
			done();
		
		},50)
	})

	it('updates `name` of the AssetStatus when onChange method is called with suitable parameters',()=>{

		wrapper.vm.onChange('test', 'name');

		expect(wrapper.vm.name).toBe('test');
	})

	it('makes an AJAX call when onSubmit method is called',(done)=>{

		updateWrapper()

		wrapper.setData({ loading : false, hasDataPopulated : true})

		wrapper.vm.isValid = () =>{return true}

		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)

		mockSubmitRequest();

		setTimeout(()=>{

			expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/asset-status')

			expect(wrapper.vm.loading).toBe(false)

			done();
		},1);
	});

	it('makes an loading as false when onSubmit method return error',(done)=>{

		updateWrapper()

		wrapper.setData({ loading : false, hasDataPopulated : true,asset_status_id : 1})

		wrapper.vm.isValid = () =>{return true}

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

		expect(wrapper.vm.$data.title).toEqual('edit_asset_status')
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

		wrapper.vm.isValid =jest.fn()   

		wrapper.vm.onSubmit()

		expect(wrapper.vm.isValid).toHaveBeenCalled()
	 
		done();
	});

	it('isValid - should return false ', done => {
				
		validation.validateAssetStatusSettings = () =>{return {errors : [], isValid : false}}
			
		expect(wrapper.vm.isValid()).toBe(false)
			
		done()
	})

	it('isValid - should return true ', done => {
			 
		validation.validateAssetStatusSettings = () =>{return {errors : [], isValid : true}}
			
		expect(wrapper.vm.isValid()).toBe(true)
			
		done()
	})

	function mockSubmitRequest(status = 200,url = '/service-desk/api/asset-status'){

		moxios.uninstall();

		moxios.install();

		moxios.stubRequest(url,{

			status: status,

			response: {'success':true,'message':'successfully saved'}
		})
	}

	function stubRequest(status = 200,url = '/service-desk/api/asset-status/1'){
		
		moxios.uninstall();
		
		moxios.install();
		
		moxios.stubRequest(url,{
			
			status: status,
			
			response : {
				data : {
					asset_status : {
						id : 1,
						name : 'test'
					}
				}
			}
		})
	}
})