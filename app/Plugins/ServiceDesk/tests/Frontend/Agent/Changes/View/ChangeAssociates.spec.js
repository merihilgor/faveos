import { shallow, createLocalVue,  mount } from '@vue/test-utils'

import Vue from 'vue'

import ChangeAssociates from '../../../../../views/js/components/Agent/Changes/View/ChangeAssociates.vue';

window.eventHub = new Vue();

describe('ChangeAssociates',() => {

	let wrapper;

	const updateWrapper = () =>{
		
		wrapper = mount(ChangeAssociates,{

			stubs: ['change-associates-table','change-activity','change-release','alert','change-details-tab','change-tickets'],
			
			mocks:{ lang:(string)=>string },
		})  
	}
	
	beforeEach(() => {
		
		updateWrapper();
	})

	it('is vue instance',() => {		
		
		expect(wrapper.isVueInstance()).toBeTruthy()
	});

	it("updtaes `category` when `associates` method called",()=>{

		updateWrapper();

		wrapper.vm.associates('assets');
		
		expect(wrapper.vm.category).toEqual('assets')
	})
})