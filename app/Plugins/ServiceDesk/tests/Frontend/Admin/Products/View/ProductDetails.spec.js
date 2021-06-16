import { shallow, createLocalVue,  mount } from '@vue/test-utils'

import sinon from 'sinon'

import Vue from 'vue'

import Vuex from 'vuex'

import ProductDetails from '../../../../../views/js/components/Admin/Products/View/ProductDetails.vue';

import moxios from 'moxios';

import * as extraLogics from "helpers/extraLogics";

let localVue = createLocalVue()

localVue.use(Vuex)

describe('ProductDetails',() => {

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

		wrapper = mount(ProductDetails,{

			stubs: ['product-actions','product-description','product-assets'],
			
			mocks:{
			
				lang:(string)=>string
			},

			methods : {

				basePath : jest.fn()
			},

			propsData : { product : { 
					id :1, 
					name : 'subject', 
					description : 'description', 
					created_at : '2019-07-31', 
					status : { id : 1, name : 'status'},
					manufacturer : 'manufacturer', 
					product_status : { id : 1, name : 'status'},
					department : { id : 1, name : 'dept'},
					procurement : { id : 1, name : 'pro'},
				}
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
		
		expect(wrapper.isVueInstance()).toBeTruthy();
	});

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