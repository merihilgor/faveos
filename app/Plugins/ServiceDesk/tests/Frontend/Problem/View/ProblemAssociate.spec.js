import { shallow, createLocalVue,  mount } from '@vue/test-utils'

import Vue from 'vue'

import ProblemAssociates from '../../../../views/js/components/Problem/View/ProblemAssociate.vue';

window.eventHub = new Vue();

describe('ProblemAssociates',() => {

	let wrapper;

	const updateWrapper = () =>{
		
		wrapper = mount(ProblemAssociates,{

			stubs: ['problem-associated-assets','problem-planning','problem-activity','alert','problem-change-details','problem-associated-tickets'],
			
			mocks:{ lang:(string)=>string },
		})  
	}
	
	beforeEach(() => {
		
		updateWrapper();
	})

	it('calls `associates` when `associateMethod` method called',(done) => {		
		
		updateWrapper();

		wrapper.vm.associates = jest.fn();

		wrapper.vm.associateMethod('activity');

		setTimeout(()=>{

			expect(wrapper.vm.associates).toHaveBeenCalled();

			done();

		},3001)


	});

	it("updtaes `category` when `associates` method called",()=>{

		updateWrapper();

		wrapper.vm.associates('assets');
		
		expect(wrapper.vm.category).toEqual('assets')
	});

	it("updates `tabs` value when `getActionsList` method called",()=>{

		updateWrapper();

		expect(wrapper.vm.tabs).toEqual('');

		let actions = { check_planning : true, associated_asset : true, associated_change : true, associated_ticket : true};

		wrapper.vm.getActionsList(actions);

		expect(wrapper.vm.tabs).not.toEqual('')
	});
})