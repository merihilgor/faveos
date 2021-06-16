import { shallow, createLocalVue,  mount } from '@vue/test-utils'

import sinon from 'sinon'

import Vue from 'vue'

import Vuex from 'vuex'

import ProductActions from '../../../../../../views/js/components/Admin/Products/View/MiniComponents/ProductActions.vue';

import * as extraLogics from "helpers/extraLogics";

let localVue = createLocalVue()

window.eventHub = new Vue();

localVue.use(Vuex)

describe('ProductActions',() => {

	let wrapper;

	let actions
  
  let store

  actions = { unsetValidationError: jest.fn() }

 	store = new Vuex.Store({ actions })

	const updateWrapper = () =>{

		wrapper = mount(ProductActions,{

			stubs: ['delete-modal','vendor-add-modal','vendor-create-modal','product-assets'],
			
			mocks:{
			
				lang:(string)=>string
			},

			methods : {

				basePath : jest.fn()
			},

			propsData : {

				product : { id : 1}
			},

			localVue, store
		})  
	}
	
	beforeEach(() => {
		
		updateWrapper();
		
	})

	it('is vue instance',() => {		
		
		expect(wrapper.isVueInstance()).toBeTruthy()
	});

	it('returns only 15 characters if value length is more than 15 and returns exact value if option length not more than 15',() => {		
		
		expect(wrapper.vm.subString('name name name name name ')).toEqual('name name name ...');

		expect(wrapper.vm.subString('name')).toEqual('name');
	});

	it("`showModal` should be `false` when `onClose` method called",()=>{

		updateWrapper();

		wrapper.vm.onClose();

		expect(wrapper.vm.showDeleteModal).toBe(false);

		expect(wrapper.vm.showVendorCreate).toBe(false);

		expect(wrapper.vm.showVendorExists).toBe(false);

		expect(wrapper.vm.showAttach).toBe(false);
		
		expect(actions.unsetValidationError).toHaveBeenCalled();
	})
})