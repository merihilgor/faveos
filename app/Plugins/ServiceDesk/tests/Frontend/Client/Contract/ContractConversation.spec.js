import { shallow, createLocalVue,  mount } from '@vue/test-utils'

import Vue from 'vue'

import Vuex from 'vuex'

import VueRouter from 'vue-router'

import VueProgressBar from 'vue-progressbar'

Vue.use(VueProgressBar, {});

import ContractConversation from '../../../../views/js/components/Client/Contract/ContractConversation.vue';

import * as validation from "../../../../views/js/validator/contractApprovalSettings";

import moxios from 'moxios';

jest.mock('helpers/responseHandler');

import * as extraLogics from "helpers/extraLogics";

let localVue = createLocalVue()

localVue.use(Vuex)

describe('ContractConversation',() => {

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

		wrapper = mount(ContractConversation,{

			stubs: ['contract-approval-modal','alert','loader','meta-component'],
			
			mocks:{ trans:(string)=>string },

			propsData : { 

				layout : { language : 'en', portal : { client_header_color : 'red'} }
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

	it('returns only 15 characters if value length is more than 15 and returns exact value if option length not more than 15',() => {		
		
		expect(wrapper.vm.subString('name name name name name ')).toEqual('name name name ...');

		expect(wrapper.vm.subString('name')).toEqual('name');
	});

	it("returns object when `getHashFromUrl` method called",()=>{

		updateWrapper();

		expect(wrapper.vm.getHashFromUrl('/edit/1/3')).toEqual({"hash": "1", "id": "3"})
	});

	it('updates `comment` value when onChange method is called',()=>{

		wrapper.vm.onChange('comment', 'comment');

		expect(wrapper.vm.comment).toEqual('comment');
	});

	it('isValid - should return false ', done => {
				
		validation.validateContractApprovalSettings = () =>{return {errors : [], isValid : false}}
			
		expect(wrapper.vm.isValid()).toBe(false)
			
		done()
	})

	it('isValid - should return true ', done => {
			 
		validation.validateContractApprovalSettings = () =>{return {errors : [], isValid : true}}
			
		expect(wrapper.vm.isValid()).toBe(true)
			
		done()
	});

	it("updates `actionType` value when `onClick` method called",()=>{

		updateWrapper();

		wrapper.vm.onClick('reject');

		expect(wrapper.vm.actionType).toEqual('reject');
	});
})