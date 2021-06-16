import { shallow, createLocalVue,  mount } from '@vue/test-utils'

import Vue from 'vue'

import ContractAssociates from '../../../../../views/js/components/Agent/Contract/View/ContractAssociates.vue';

window.eventHub = new Vue();

describe('ContractAssociates',() => {

	let wrapper;

	const updateWrapper = () =>{
		
		wrapper = mount(ContractAssociates,{

			stubs: ['contract-activity','contract-history','contract-associated-assets','alert','loader'],
			
			mocks:{ trans:(string)=>string },

			propsData : {

				contract : { id : 1, attach_assets : [{id:1,name:'asset'}]}
			}
		})  
	}
	
	beforeEach(() => {
		
		updateWrapper();
	})

	it("updates `tabs` value whenn `getTabs` method called",()=>{

		wrapper.vm.getTabs();

		expect(wrapper.vm.tabs).not.toEqual('');
	});

	it("updtaes `category` when `associates` method called",()=>{

		updateWrapper();

		wrapper.vm.associates('assets');
		
		expect(wrapper.vm.category).toEqual('assets')
	})
})