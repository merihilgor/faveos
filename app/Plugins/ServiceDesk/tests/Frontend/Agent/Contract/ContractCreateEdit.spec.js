import { shallow, createLocalVue,  mount,shallowMount } from '@vue/test-utils'

import Vue from 'vue'

import Vuex from 'vuex'

import ContractCreateEdit from '../../../../views/js/components/Agent/Contract/ContractCreateEdit.vue';

import moxios from 'moxios';

import * as extraLogics from "helpers/extraLogics";

import * as validation from "../../../../views/js/validator/contractValidation";

let localVue = createLocalVue()

localVue.use(Vuex)

jest.mock('helpers/responseHandler')

describe('ContractCreateEdit',() => {

	let wrapper;

	let actions

	let getters = {

		formattedTime: () => () => {return ''},
		
		formattedDate:()=> () => {return ''},
	}
  
  let store

  actions = { unsetValidationError: jest.fn() }

 	store = new Vuex.Store({ actions, getters })

	const updateWrapper = () =>{

		extraLogics.getIdFromUrl = () =>{return 1}

			wrapper = mount(ContractCreateEdit,{

			stubs: ['text-field','dynamic-select','ck-editor','custom-loader','alert','file-upload','date-time-field','number-field','add-new-modal'],

			mocks:{	lang:(string)=>string },

			methods : { 
				
				redirect : jest.fn(), 
				
				basePath : jest.fn() 
			},

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

	it('is vue instance',() => {		
		
		expect(wrapper.isVueInstance()).toBeTruthy()
	});

	it("updates `showModal` value as `true` when `getField` method called",()=>{

		wrapper.vm.getField('modal');

		expect(wrapper.vm.modalName).toEqual('modal');

		expect(wrapper.vm.showModal).toEqual(true);
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

		wrapper.vm.getInitialValues = jest.fn();

		wrapper.vm.getValues('/10/edit');

		expect(wrapper.vm.$data.title).toEqual('edit_contract');

		expect(wrapper.vm.getInitialValues).toHaveBeenCalled();

		expect(wrapper.vm.hasDataPopulated).toEqual(false)

		wrapper.vm.getValues('/create');

		expect(wrapper.vm.loading).toEqual(false)

		expect(wrapper.vm.hasDataPopulated).toEqual(true)
	});
	
	it('makes an API call', (done) => {
		
			updateWrapper();

			wrapper.vm.updateStatesWithData = jest.fn();
		
			wrapper.vm.getInitialValues(1);
			
			expect(wrapper.vm.attachment_delete).toEqual(false);
			
			expect(wrapper.vm.loading).toEqual(true);
			
			stubRequest();
		
			setTimeout(()=>{
		
				expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/contract/1')

				expect(wrapper.vm.updateStatesWithData).toHaveBeenCalled();

				expect(wrapper.vm.hasDataPopulated).toEqual(true);

				expect(wrapper.vm.loading).toEqual(false);

				done();
		
			},50)
	 })

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

	it('updates state data correctly(according to the key) when `updateStatesWithData` is called',() => {
		
		var data = { cost :10 , name : 'name' }
		
		wrapper.vm.updateStatesWithData(data);
		
		expect(wrapper.vm.cost).toEqual(10);

		expect(wrapper.vm.name).toEqual('name');
	});

	it('isValid - should return false ', done => {
				
		validation.validateContractSettings = () =>{return {errors : [], isValid : false}}
			
		expect(wrapper.vm.isValid()).toBe(false)
			
		done()
	})

	it('isValid - should return true ', done => {
			 
		validation.validateContractSettings = () =>{return {errors : [], isValid : true}}
			
		expect(wrapper.vm.isValid()).toBe(true)
			
		done()
	})

	it("updates `vendor` value when `createdValue` method called",()=>{

		wrapper.vm.createdValue({id:1,name:'vendor1'},'vendors');

		expect(wrapper.vm.vendor).toEqual({id:1,name:'vendor1'})
	})

	it("updates `attachment_delete` value when `onFileSelected` method called",()=>{

		expect(wrapper.vm.attachment_delete).toEqual(false)

		wrapper.vm.onFileSelected('value','name',true);

		expect(wrapper.vm.attachment_delete).toEqual(true)
	})

	it('updates `statusDisabled` value when onChange method is called with name `approver`',()=>{

		wrapper.vm.onChange({id:1,name:'dddd'}, 'approver');

		expect(wrapper.vm.status).toEqual({ id : 1, name : 'Draft' });

		expect(wrapper.vm.statusDisabled).toEqual(true);
	})

	it('updates `statusDisabled` value as `false` when onChange method is called with empty `approver`',()=>{

		wrapper.vm.onChange('', 'approver');

		expect(wrapper.vm.statusDisabled).toEqual(false);
	})

	it('updates `contract_start_date,contract_end_date` value when onChange method is called with `contractDate`',()=>{
		
		wrapper.vm.onChange(['2019-01-19', '2019-01-20'], 'contractDate');
		
		expect(wrapper.vm.contractDate).toEqual(["2019-01-19 00:00:00", "2019-01-20 00:00:00"]);

		expect(wrapper.vm.contract_start_date).toBe('2019-01-19 00:00:00');

		expect(wrapper.vm.contract_end_date).toBe('2019-01-20 00:00:00');
	})

	it('makes an AJAX call when onSubmit method is called',(done)=>{

		updateWrapper()

		wrapper.vm.getInitialValues = jest.fn();

		wrapper.setData({ 
			loading : false, 
			hasDataPopulated : true,
			contract_id : 1,
			licenseType : [{id:1,name:'license'}],
			licenseCount : 10,
			approver : {id:1,name:'name'},
			agents : [{id:1,name:'name'},{name:'email'}],
			asset_ids : [{id:1,name:'name'}],
			selectedFile : 'file',
		})

		wrapper.vm.isValid = () =>{return true}

		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)

		mockSubmitRequest();

		setTimeout(()=>{

			expect(wrapper.vm.loading).toBe(false)

			expect(wrapper.vm.getInitialValues).toHaveBeenCalled();

			done();
		},1);
	});

	it('calls `redirect` method when page is in edit',(done)=>{

		updateWrapper()

		wrapper.setData({ 
			loading : false, 
			hasDataPopulated : true,
			contract_id : '',
			licenseType : [{id:1,name:'license'}],
			licenseCount : 10,
			approver : {id:1,name:'name'},
			agents : [{id:1,name:'name'},{name:'email'}],
			asset_ids : [{id:1,name:'name'}],
			attachment_delete : true,
		})

		wrapper.vm.isValid = () =>{return true}

		wrapper.vm.redirect = jest.fn()

		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)

		mockSubmitRequest();

		setTimeout(()=>{

			expect(wrapper.vm.loading).toBe(false)

			expect(wrapper.vm.redirect).toHaveBeenCalled()

			expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/contract')
			
			done();
		},1);
	});

	it('makes an loading as false when onSubmit method return error',(done)=>{

		updateWrapper()

		wrapper.setData({ loading : false, hasDataPopulated : true})

		wrapper.vm.isValid = () =>{return true}

		wrapper.vm.onSubmit()

		mockSubmitRequest(400);

		setTimeout(()=>{

			expect(wrapper.vm.loading).toBe(false)

			done();
		},1);
	});

	it("updates `showModal` value as `false` when `onClose` method called",()=>{

		wrapper.vm.onClose();

		expect(wrapper.vm.showModal).toEqual(false)

		expect(actions.unsetValidationError).toHaveBeenCalled();
	})
	

	function mockSubmitRequest(status = 200,url = '/service-desk/api/contract'){

		moxios.uninstall();

		moxios.install();

		moxios.stubRequest(url,{

			status: status,

			response: {'success':true,'message':'successfully saved'}
		})
	}

	function stubRequest(status = 200,url = '/service-desk/api/contract/1'){
		
		moxios.uninstall();
		
		moxios.install();
		
		moxios.stubRequest(url,{
			
			status: status,
			
			response : {
				data : {
					contract : {
						id : 1,
						licence :  { id : 1, name : 'type1'},
						contract_start_date : '2019-01-19',
						contract_end_date : '2019-01-20',
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
})