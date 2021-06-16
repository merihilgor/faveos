import { mount, createLocalVue } from '@vue/test-utils';

import ProductDescription from '../../../../../../views/js/components/Admin/Products/View/MiniComponents/ProductDescription.vue';

import Vue from 'vue';

describe('ProductDescription',()=>{

	let wrapper;

	const updateWrapper = ()=>{

		wrapper = mount(ProductDescription,{

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