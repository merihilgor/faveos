import { shallow, createLocalVue,  mount,shallowMount } from '@vue/test-utils'

import Vue from 'vue'

import ChangesCreateEdit from '../../../../views/js/components/Agent/Changes/ChangesCreateEdit.vue';

import moxios from 'moxios';

window.eventHub = new Vue();

import * as extraLogics from "helpers/extraLogics";

import * as validation from "../../../../views/js/validator/changeValidation";

jest.mock('helpers/responseHandler')

describe('ChangesCreateEdit',() => {

	let wrapper;

	const updateWrapper = () =>{

		extraLogics.getIdFromUrl = () =>{return 1}
		
		wrapper = mount(ChangesCreateEdit,{
			
			stubs: ['text-field','dynamic-select','ck-editor','custom-loader','alert','file-upload'],
			
			methods : {

				basePath : jest.fn(),

				redirect : jest.fn()
			},

			mocks:{ lang:(string)=>string },
			
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

	it("updates `title` value when page is in edit and calls `getInitialValues` method",()=>{
		
		updateWrapper()
		
		wrapper.vm.getInitialValues = jest.fn();

		wrapper.vm.getValues('/10/edit');
	
		expect(wrapper.vm.title).toEqual('edit_change')

		expect(wrapper.vm.hasDataPopulated).toEqual(false)

		expect(wrapper.vm.btnName).toEqual('update')

		expect(wrapper.vm.iconClass).toEqual('fa fa-refresh')

		expect(wrapper.vm.getInitialValues).toHaveBeenCalledWith(1)
	});

	it('updates `loading,hasDataPopulated` value on create page', () => { 
		
		updateWrapper()

		wrapper.vm.getValues('/create');

		expect(wrapper.vm.loading).toEqual(false)

		expect(wrapper.vm.hasDataPopulated).toEqual(true)
	});

	it('makes an API call', (done) => {

		updateWrapper();
			
		wrapper.vm.updateStatesWithData = jest.fn();

		wrapper.vm.getInitialValues(1);

		expect(wrapper.vm.loading).toBe(true)

		stubRequest();
			
		setTimeout(()=>{
				
			expect(wrapper.vm.hasDataPopulated).toBe(true)
				
			expect(wrapper.vm.loading).toBe(false)

			expect(wrapper.vm.updateStatesWithData).toHaveBeenCalled()

			expect(wrapper.vm.change_id).toEqual(1)

			expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/change/1')
				
			done();
		},50)
	})

	it("makes `loading` as false if api returns error response",(done)=>{

		updateWrapper();

		updateWrapper();

		wrapper.vm.getInitialValues(1);

		stubRequest(400);

		setTimeout(()=>{

			expect(wrapper.vm.loading).toBe(false)

			done();
		},50)
	})

	it('updates state data correctly when `updateStatesWithData` is called',() => {
		
		var data = { id : 1,subject :'test' }
		
		wrapper.vm.updateStatesWithData(data);
		
		expect(wrapper.vm.subject).toBe('test');
	});

	it('isValid - should return false ', done => {
				
		validation.validateChangeSettings = () =>{return {errors : [], isValid : false}}
			
		expect(wrapper.vm.isValid()).toBe(false)
			
		done()
	})

	it('isValid - should return true ', done => {
			 
		validation.validateChangeSettings = () =>{return {errors : [], isValid : true}}
			
		expect(wrapper.vm.isValid()).toBe(true)
			
		done()
	})

	it('updates `subject` of the change when onChange method is called with suitable parameters for change name',()=>{
	
		wrapper.vm.onChange('change 1', 'subject');
	
		expect(wrapper.vm.subject).toBe('change 1');
	})

	it('makes an AJAX call when onSubmit method is called',(done)=>{

		updateWrapper()

		wrapper.setData({ loading : false, hasDataPopulated : true, subject : 'change', assets : [ { id : 1, name : 'ww'}]})

		wrapper.vm.redirect = jest.fn()

		wrapper.vm.isValid = () =>{return true}

		wrapper.vm.getDescription = () =>{return 'description'}

		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)

		mockSubmitRequest();

		setTimeout(()=>{

			expect(wrapper.vm.loading).toBe(false)

			expect(wrapper.vm.redirect).toHaveBeenCalled()

			expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/change')
			
				done();
		},1);
	});

	it('makes loading as false when api returns error response',(done)=>{

		updateWrapper()

		wrapper.setData({ loading : false, hasDataPopulated : true, change_id : 1 })

		wrapper.vm.isValid = () =>{return true}

		wrapper.vm.getDescription = () =>{return 'description'}

		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)

		mockSubmitRequest(400);

		setTimeout(()=>{

			expect(wrapper.vm.loading).toBe(false)

			done();
		},1);
	});

	function stubRequest(status = 200,url = '/service-desk/api/change/1'){

		moxios.uninstall();

		moxios.install();

		moxios.stubRequest(url,{

			status: status,

			response : {
					
				success:true,
					
				data : {
					
					change : { id : 1, subject : 'test' }
				}	
			}
		})
	}

	function mockSubmitRequest(status = 200,url = '/service-desk/api/change'){

		moxios.uninstall();
		
		moxios.install();
		
		moxios.stubRequest(url,{
			
			status: status,
			
			response: {'success':true,'message':'successfully saved'}
		})
	}
})