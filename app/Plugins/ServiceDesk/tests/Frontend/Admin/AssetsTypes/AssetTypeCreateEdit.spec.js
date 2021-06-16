import { shallow, createLocalVue,  mount,shallowMount } from '@vue/test-utils'

import Vue from 'vue'

import AssetTypeCreateEdit from '../../../../views/js/components/Admin/AssetsTypes/AssetTypeCreateEdit.vue';

import moxios from 'moxios';

import sinon from 'sinon'

import * as extraLogics from "helpers/extraLogics";

import * as validation from "../../../../views/js/validator/assetTypeValidation";

jest.mock('helpers/responseHandler')

describe('AssetTypeCreateEdit',() => {

	let wrapper;

	const updateWrapper = () =>{

		extraLogics.getIdFromUrl = () =>{return 1}

			wrapper = mount(AssetTypeCreateEdit,{

			stubs: ['text-field','dynamic-select','custom-loader','alert','tool-tip'],

			mocks:{	lang:(string)=>string },

			methods : { 
				
				redirect : jest.fn()
			}
		})  
	}
	
	beforeEach(() => {
		 
		updateWrapper();

		moxios.install();

		moxios.stubRequest('/service-desk/api/asset-type/1',{
			
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

	it('updates `name` of the assettype when onChange method is called with suitable parameters',()=>{

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

			expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/asset-type')

			expect(wrapper.vm.loading).toBe(false)

			done();
		},1);
	});

	it('makes an loading as false when onSubmit method return error',(done)=>{

		updateWrapper()

		wrapper.setData({ loading : false, hasDataPopulated : true,asset_type_id : 1})

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

		wrapper.vm.getInitialValues = jest.fn();

		wrapper.vm.getValues('/10/edit');

		expect(wrapper.vm.$data.title).toEqual('edit_assetstypes');

		expect(wrapper.vm.hasDataPopulated).toEqual(false);

		expect(wrapper.vm.getInitialValues).toHaveBeenCalled();
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
	});

	it('changeDefault function will be called if `make deafult` checkbox changed.',() => {
		const spy = sinon.spy(AssetTypeCreateEdit.methods, 'changeDefault')
		updateWrapper();  
		wrapper.find('.checkbox_align').trigger('change')
		expect(spy.called).toBe(true)
	});

	it('call `isValid` method when onSubmit method is called',(done)=>{

		updateWrapper()

		wrapper.setData({ loading : false, hasDataPopulated : true})

		wrapper.vm.isValid =jest.fn()   

		wrapper.vm.onSubmit()

		expect(wrapper.vm.isValid).toHaveBeenCalled()
	 
		done();
	});

	it('isValid - should return false ', done => {
				
		validation.validateAssetsSettings = () =>{return {errors : [], isValid : false}}
			
		expect(wrapper.vm.isValid()).toBe(false)
			
		done()
	})

	it('isValid - should return true ', done => {
			 
		validation.validateAssetsSettings = () =>{return {errors : [], isValid : true}}
			
		expect(wrapper.vm.isValid()).toBe(true)
			
		done()
	})

	function mockSubmitRequest(status = 200,url = '/service-desk/api/asset-type'){

		moxios.uninstall();

		moxios.install();

		moxios.stubRequest(url,{

			status: status,

			response: {'success':true,'message':'successfully saved'}
		})
	}

	function stubRequest(status = 200,url = '/service-desk/api/asset-type/1'){
		
		moxios.uninstall();
		
		moxios.install();
		
		moxios.stubRequest(url,{
			
			status: status,
			
			response : {
				data : {
					assets_types : {
						id : 1,
						name : 'test'
					}
				}
			}
		})
	}


	let fakeResponse = {
		success:true,
		data : {
			data : {
				asset_types : {
					name : 'test'
				}
			}
		}
	}
})