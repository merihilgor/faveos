import { mount, createLocalVue } from '@vue/test-utils';

import AssetDescription from '.../../../../../views/js/components/Asset/View/Mini/AssetDescription.vue';

import Vue from 'vue';

describe('AssetDescription',()=>{

	let wrapper;

	const updateWrapper = ()=>{

		wrapper = mount(AssetDescription,{

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