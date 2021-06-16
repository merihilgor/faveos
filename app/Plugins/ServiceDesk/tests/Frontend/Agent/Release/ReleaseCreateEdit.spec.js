import { shallow, createLocalVue,  mount,shallowMount } from '@vue/test-utils'

import Vue from 'vue'

import ReleaseCreateEdit from '../../../../views/js/components/Agent/Release/ReleaseCreateEdit.vue';

import moxios from 'moxios';

import * as extraLogics from "helpers/extraLogics";

import * as validation from "../../../../views/js/validator/releaseValidation";

jest.mock('helpers/responseHandler')

describe('ReleaseCreateEdit',() => {

	let wrapper;

	const updateWrapper = () =>{

		extraLogics.getIdFromUrl = () =>{return 1}

			wrapper = mount(ReleaseCreateEdit,{

			stubs: ['text-field','dynamic-select','ck-editor','custom-loader','alert','file-upload','date-time-field'],

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

		moxios.stubRequest('/service-desk/api/release/1',{
			
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
		
				expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/release/1')
			
				expect(wrapper.vm.planned_date).toEqual('');

				expect(wrapper.vm.release_id).toEqual(1);
				
				expect(wrapper.vm.releaseType).toEqual({id:1, name : 'type1'});
				
				done();
		
			},50)
	 })

	it('updates state data correctly(according to the key) when `updateStatesWithData` is called',() => {
		
		var data = { subject :'test' }
		
		wrapper.vm.updateStatesWithData(data);
		
		expect(wrapper.vm.subject).toBe('test');
	});

	it('updates `subject` of the problem when onChange method is called with suitable parameters for problem name',()=>{

		wrapper.vm.onChange('release 1', 'subject');

		expect(wrapper.vm.subject).toBe('release 1');
	})

	it('updates `planned_start_date,planned_end_date` value when onChange method is called with `planned_date`',()=>{
		
		wrapper.vm.onChange(['2019-01-19', '2019-01-20'], 'planned_date');
		
		expect(wrapper.vm.planned_date).toEqual(["2019-01-19", "2019-01-20"]);

		expect(wrapper.vm.planned_start_date).toBe('2019-01-19+00:00:00');

		expect(wrapper.vm.planned_end_date).toBe('2019-01-20+00:00:00');
	})

	it('makes an AJAX call when onSubmit method is called',(done)=>{

		updateWrapper()

		wrapper.setData({ loading : false, hasDataPopulated : true,release_id : 1,asset_ids : [{id:1,name:'asset'}]})

		wrapper.vm.isValid = () =>{return true}

		wrapper.vm.getDescription = () =>{return 'description'}

		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)

		mockSubmitRequest();

		setTimeout(()=>{

			expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/release/1')

			expect(wrapper.vm.loading).toBe(true)

			done();
		},1);
	});

	it('makes an loading as false when onSubmit method return error',(done)=>{

		updateWrapper()

		wrapper.setData({ loading : false, hasDataPopulated : true})

		wrapper.vm.isValid = () =>{return true}

		wrapper.vm.getDescription = () =>{return 'description'}

		wrapper.vm.onSubmit()

		mockSubmitRequest(400);

		setTimeout(()=>{

			expect(wrapper.vm.loading).toBe(false)

			done();
		},1);
	});

	it('updates `show_attach_assets` value when getActions method is called',(done)=>{

		updateWrapper()

		wrapper.vm.getActions()

		mockActionRequest();

		setTimeout(()=>{

			expect(moxios.requests.mostRecent().url).toBe('/service-desk/get/permission/api')

			expect(wrapper.vm.show_attach_assets).toBe(false)

			done();
		},1);
	});

	it('updates `show_attach_assets` value as `false` when getActions Api returns error',(done)=>{

		updateWrapper()

		wrapper.vm.getActions()

		mockActionRequest(400);

		setTimeout(()=>{

			expect(wrapper.vm.show_attach_assets).toBe(true)

			done();
		},1);
	});

	it("updates `title value when page is in edit`",()=>{
		
		updateWrapper()

		wrapper.vm.getValues('/10/edit');

		expect(wrapper.vm.$data.title).toEqual('edit_release')
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
				
		validation.validateReleaseSettings = () =>{return {errors : [], isValid : false}}
			
		expect(wrapper.vm.isValid()).toBe(false)
			
		done()
	})

	it('isValid - should return true ', done => {
			 
		validation.validateReleaseSettings = () =>{return {errors : [], isValid : true}}
			
		expect(wrapper.vm.isValid()).toBe(true)
			
		done()
	})

	function mockSubmitRequest(status = 200,url = '/service-desk/api/release'){

		moxios.uninstall();

		moxios.install();

		moxios.stubRequest('/service-desk/api/release',{

			status: status,

			response: {'success':true,'message':'successfully saved'}
		})
	}

	function stubRequest(status = 200,url = '/service-desk/api/release/1'){
		
		moxios.uninstall();
		
		moxios.install();
		
		moxios.stubRequest(url,{
			
			status: status,
			
			response : {
				data : {
					release : {
						id : 1,
						release_type :  { id : 1, name : 'type1'},
						planned_start_date : '2019-01-19',
						planned_end_date : '2019-01-20',
					}
				}
			}
		})
	}

	function mockActionRequest(status = 200,url = '/service-desk/get/permission/api'){
		
		moxios.uninstall();
		
		moxios.install();
		
		moxios.stubRequest(url,{
			
			status: status,
			
			response : {
				data : {
					actions : {
						attach_asset : false
					}
				}
			}
		})
	}

	let fakeResponse = {
		success:true,
		data : {
			data : {
				release : {
					subject : 'test'
				}
			}
		}
	}
})