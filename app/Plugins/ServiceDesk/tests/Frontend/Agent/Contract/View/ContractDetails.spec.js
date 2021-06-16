import { shallow, createLocalVue,  mount } from '@vue/test-utils'

import sinon from 'sinon'

import Vue from 'vue'

import Vuex from 'vuex'

import ContractDetails from '../../../../../views/js/components/Agent/Contract/View/ContractDetails.vue';

import moxios from 'moxios';

import * as extraLogics from "helpers/extraLogics";

let localVue = createLocalVue()

localVue.use(Vuex)

describe('ContractDetails',() => {

	let wrapper;

	let store;

	let getters;

	let actions;

	getters = {

		formattedTime: () => () => {return ''},
		
		formattedDate:()=> () => {return ''},
	}

	actions = { unsetValidationError: jest.fn() }

	store = new Vuex.Store({ getters,actions })

	const updateWrapper = () =>{

		wrapper = mount(ContractDetails,{

			stubs: ['contract-actions','contract-description'],
			
			mocks:{ trans:(string)=>string },

			methods : { basePath : jest.fn() },

			propsData : { contract : { 
					id :1, 
					name : 'name', 
					description : 'description', 
					cost : 100,
					licensce_count : 1,
					notify_expiry : '--',
					contract_start_date : '2019-07-31', 
					contract_end_date : '2019-07-31', 
					created_at : '2019-07-31', 
					updated_at : '2019-07-31', 
					notify_before : 1,
					identifier : 1,
					vendor : { id : 1, name : 'vendor'},
					licence : { id : 1, name : 'licence'}, 
					owner : { id : 1, name : 'owner'},
					contractType : { id : 1, name : 'contractType'},
					notify_to : [{ id : 1, name : 'impact_type',email:'abc@gmail.com'}],
					approver : { id : 1, name : 'approver'},
					status : { id : 1, name : 'status'},
					organization : [{ id : 1, name : 'organization'}],
					user : [{ id : 1, name : 'user',email:'abc@gmail.com'}],
					attachment : [{ type : 'jpeg', value :'fghjkl', size : 1024}]
				}
			},

			attachToDocument: true,

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

	it('returns only 15 characters if value length is more than 15 and returns exact value if option length not more than 15',() => {		
		
		expect(wrapper.vm.subString('name name name name name ')).toEqual('name name name ...');

		expect(wrapper.vm.subString('name')).toEqual('name');
	});

	it("`showModal` should be `false` when `onClose` method called",()=>{

		updateWrapper();

		wrapper.vm.onClose();

		expect(wrapper.vm.showDescription).toBe(false);

		expect(actions.unsetValidationError).toHaveBeenCalled();
	})
})