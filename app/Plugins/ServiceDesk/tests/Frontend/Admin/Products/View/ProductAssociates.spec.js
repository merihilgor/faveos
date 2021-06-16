import { shallow, createLocalVue,  mount } from '@vue/test-utils'

import Vue from 'vue'

import ProductAssociates from '../../../../../views/js/components/Admin/Products/View/ProductAssociates.vue';

window.eventHub = new Vue();

describe('ProductAssociates',() => {

	let wrapper;

	const updateWrapper = () =>{
		
		wrapper = mount(ProductAssociates,{

			stubs: ['product-associates-table','alert','loader'],
			
			mocks:{ lang:(string)=>string },
		})  
	}
	
	beforeEach(() => {
		
		updateWrapper();
	})

	it('is vue instance',() => {		
		
		expect(wrapper.isVueInstance()).toBeTruthy()
	});

	it("updtaes `category` when `associates` method called",(done)=>{

		updateWrapper();

		wrapper.vm.associates('assets');
		
		expect(wrapper.vm.category).toEqual('assets');

		expect(wrapper.vm.tabLoading).toEqual(true);

		setTimeout(()=>{
			
			expect(wrapper.vm.tabLoading).toEqual(false);

			done();
		},1)
	})
})