import {  createLocalVue,  mount } from '@vue/test-utils'

import Vue from 'vue'

import Vuex from 'vuex'

import moxios from 'moxios'

import ContractActions from '../../../../../../views/js/components/Agent/Contract/View/Mini/ContractActions.vue';

let localVue = createLocalVue()

window.eventHub = new Vue();

localVue.use(Vuex)

describe('ContractActions',() => {

	let wrapper;

	let actions
  
  let store

  actions = { unsetValidationError: jest.fn() }

 	store = new Vuex.Store({ actions })

	const updateWrapper = () =>{

		wrapper = mount(ContractActions,{

			stubs: ['delete-modal','contract-update-modal','contract-assets','contract-action-modal','contract-reminder-modal'],
			
			mocks:{ trans:(string)=>string },

			methods : { basePath : jest.fn() },

			propsData : { 

				auth : { user_data : { id : 1 }}, 
			
				updateContractDetails : jest.fn(),
				
				contract : { id : 1, approver : { id : 1, name : 'approver'}, status : { id : 1, name : 'status'} }
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
	});

	it('makes an API call', (done) => {
			
		updateWrapper();
			
		wrapper.vm.getActions(1);
		
		stubRequest();

		setTimeout(()=>{
			
			expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/contract-action/1')
			
			expect(wrapper.vm.actions).not.toEqual('')

			done();
		},50)
	})

	it("makes `actions` as empty if api returns error response",(done)=>{

		updateWrapper();

		wrapper.vm.getActions(1);

		stubRequest(400);

		setTimeout(()=>{

			expect(wrapper.vm.actions).toEqual('')

			done();
		},50)
	})

	it('updates `actionType, showUpdateModal` values when `extendAndRenew` method called',()=>{

		wrapper.vm.extendAndRenew('renew');

		expect(wrapper.vm.showUpdateModal).toEqual(true)

		expect(wrapper.vm.actionType).toEqual('renew')
	})

	it("`showModal` should be `false` when `onClose` method called",()=>{

		updateWrapper();

		wrapper.vm.onClose();

		expect(wrapper.vm.showUpdateModal).toBe(false);

		expect(wrapper.vm.showAssetModal).toBe(false);

		expect(wrapper.vm.showCommonModal).toBe(false);

		expect(wrapper.vm.showReminder).toBe(false);
		
		expect(wrapper.vm.showDeleteModal).toBe(false);

		expect(actions.unsetValidationError).toHaveBeenCalled();
	})

	it('updates `actionType, showCommonModal` values when `commonMethod` method called',()=>{

		wrapper.vm.commonMethod('approve');

		expect(wrapper.vm.showCommonModal).toEqual(true)

		expect(wrapper.vm.actionType).toEqual('approve')
	})

	function stubRequest(status = 200,url = '/service-desk/api/contract-action/1'){
	  
	  moxios.uninstall();
	  
	  moxios.install();
	  
	  moxios.stubRequest(url,{
	    
	    status: status,
	    
	    response : {
	    	data : {
	    		actions : {
	    			associated_asset: true,
					contract_approve: true,
					contract_asset_attach: true,
					contract_asset_detach: true,
					contract_delete: true,
					contract_edit: true,
					contract_extend: true,
					contract_renew: false,
					contract_terminate: true,
					contract_view: true
	    		}
	    	}
	    }
	  })
	}
})