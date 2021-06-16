import { mount, createLocalVue } from '@vue/test-utils';

import ProblemDescription from '../../../../../views/js/components/Problem/View/MiniComponents/ProblemDescriptionPage.vue';

import Vue from 'vue';

describe('ProblemDescription',()=>{

	let wrapper;

	const updateWrapper = ()=>{

		wrapper = mount(ProblemDescription,{

			 mocks : { lang:(string)=> string },

			 propsData : { description : 'description', onClose : jest.fn()},

			 stubs : ['modal']
		})
	};

	beforeEach(()=>{

		updateWrapper();
	
	});

	it('Is a vue instance',()=>{
		
		expect(wrapper.isVueInstance()).toBeTruthy();
	
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