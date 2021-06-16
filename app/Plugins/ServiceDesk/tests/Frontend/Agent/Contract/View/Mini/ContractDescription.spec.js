import { mount, createLocalVue } from '@vue/test-utils';

import ContractDescription from '../../../../../../views/js/components/Agent/Contract/View/Mini/ContractDescription.vue';

import Vue from 'vue';

describe('ContractDescription',()=>{

	let wrapper;

	const updateWrapper = ()=>{

		wrapper = mount(ContractDescription,{

			 mocks : { trans:(string)=> string },

			 propsData : { description : 'description', onClose : jest.fn()},

			 stubs : ['modal']
		})
	};

	beforeEach(()=>{

		updateWrapper();
	
	});

	it('Shows Modal popup if `showModal` is true',()=>{

		wrapper.setProps({ showModal : true });

		expect(wrapper.find('modal-stub').exists()).toBe(true);

	});

	it('Does not show Modal popup if `showModal` is false',()=>{

		wrapper.setProps({ showModal : false });

		expect(wrapper.find('modal-stub').exists()).toBe(false);
		
	});
})