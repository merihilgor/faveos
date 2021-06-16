import { shallow, createLocalVue,  mount } from '@vue/test-utils'

import Vue from 'vue'

import Vuex from 'vuex'

import ChangeAssociatesActions from '../../../../../../views/js/components/Agent/Changes/View/MiniComponents/ChangeAssociatesActions.vue';

let localVue = createLocalVue()

localVue.use(Vuex)

describe('ChangeAssociatesActions',() => {

	let wrapper;

	let actions
  
	let store

	const updateWrapper = () =>{

	  actions = { unsetValidationError: jest.fn() }

	 	store = new Vuex.Store({ actions })

		wrapper = mount(ChangeAssociatesActions,{

			stubs: ['change-asset-detach'],
			
			mocks:{ lang:(string)=>string },

			propsData : { data : { detach : true, id : 1, change_id : 1, compName :'change'}},

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