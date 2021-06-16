import { shallow, createLocalVue,  mount } from '@vue/test-utils'

import Vue from 'vue'

import Vuex from 'vuex'

import ProductAssociatesActions from '../../../../../../views/js/components/Admin/Products/View/MiniComponents/ProductAssociatesActions.vue';

let localVue = createLocalVue()

localVue.use(Vuex)

describe('ProductAssociatesActions',() => {

	let wrapper;

	let actions
  
	let store

	const updateWrapper = () =>{

	  actions = { unsetValidationError: jest.fn() }

	 	store = new Vuex.Store({ actions })

		wrapper = mount(ProductAssociatesActions,{

			stubs: ['product-detach'],
			
			mocks:{ lang:(string)=>string },

			propsData : { data : { detach : true, id : 1, product_id : 1, compName :'product'}},

			localVue, store
		})  
	}
	
	beforeEach(() => {
		
		updateWrapper();
	})

	it('is vue instance',() => {		
		
		expect(wrapper.isVueInstance()).toBeTruthy()
	});

	it("updtaes `showModal` when `onClose` method called",()=>{

		updateWrapper();

		wrapper.vm.onClose();
		
		expect(wrapper.vm.showModal).toEqual(false)

		expect(actions.unsetValidationError).toHaveBeenCalled();
	})
})