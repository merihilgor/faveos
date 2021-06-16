import { shallow, createLocalVue,  mount } from '@vue/test-utils'

import Vue from 'vue'

import ReleaseAssociates from '../../../../../views/js/components/Agent/Release/View/ReleaseAssociates.vue';

window.eventHub = new Vue();

describe('ReleaseAssociates',() => {

	let wrapper;

	const updateWrapper = () =>{
		
		wrapper = mount(ReleaseAssociates,{

			stubs: ['release-associated-assets','release-planning','release-activity','alert','release-associated-changes','loader'],
			
			mocks:{ trans:(string)=>string },

			propsData : { release : { attach_assets : [], attach_changes : []} }
		})  
	}
	
	beforeEach(() => {
		
		updateWrapper();
	})

	it("updtaes `category` when `associates` method called",()=>{

		updateWrapper();

		wrapper.vm.associates('assets');
		
		expect(wrapper.vm.category).toEqual('assets')
	});

	it("updates `tabs` value when `getActionsList` method called",()=>{

		updateWrapper();

		wrapper.vm.getTabs(true);

		expect(wrapper.vm.tabs).not.toEqual('')
	});
})