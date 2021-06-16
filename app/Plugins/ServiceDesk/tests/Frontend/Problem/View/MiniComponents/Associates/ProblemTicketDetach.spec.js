import { mount ,createLocalVue, shallowMount} from '@vue/test-utils'

import ProblemTicketDetach from '../../../../../../views/js/components/Problem/View/MiniComponents/Associates/ProblemTicketDetach'

import Vue from 'vue'

import moxios from 'moxios'

jest.mock('helpers/responseHandler')

window.eventHub = new Vue();

let wrapper;

describe('ProblemTicketDetach',()=>{

	beforeEach(()=>{

		moxios.install()
	});

	afterEach(()=>{
		
		moxios.uninstall()
	});

	const populateWrapper = ()=>{
			
		wrapper = mount(ProblemTicketDetach,{
			
			mocks : { lang:(string)=> string },
			
			propsData : { 
				
				ticketId : 1,

				problemId : 1,

				showModal : true,

				onClose : jest.fn()
			},
		
			stubs:['modal','alert','loader'],
		
		});
	}
	
	it('Is a vue instance',()=>{
		
		populateWrapper()

		expect(wrapper.isVueInstance()).toBe(true)	
	});

	it('Makes an API call when `onSubmit` method called', (done) => {
			
		populateWrapper();
			
		wrapper.setData({ loading : false })
		
		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toEqual(true)

		expect(wrapper.vm.isDisabled).toEqual(true)
			
		mockSubmitRequest(200)

		setTimeout(()=>{
				
			expect(moxios.requests.__items.length).toBe(1)

			expect(moxios.requests.mostRecent().url).toBe("/service-desk/api/problem-detach-ticket/1/1")
			
			expect(wrapper.vm.onClose).toHaveBeenCalled()

			expect(wrapper.vm.loading).toEqual(false)

			expect(wrapper.vm.isDisabled).toEqual(true)

			done();
		},1)
	});
	
	it('Makes an `loading, isDisabled` as false when `onSubmit` returns error', (done) => {
			
		populateWrapper();
			
		wrapper.setData({ loading : false })
		
		wrapper.vm.onSubmit()
			
		mockSubmitRequest(400)

		setTimeout(()=>{
				
			expect(moxios.requests.mostRecent().url).toBe("/service-desk/api/problem-detach-ticket/1/1")
			
			expect(wrapper.vm.onClose).not.toHaveBeenCalled()

			expect(wrapper.vm.loading).toEqual(false)

			expect(wrapper.vm.isDisabled).toEqual(false)

			done();
		},1)
	});

	function mockSubmitRequest(status = 200, url = '/service-desk/api/problem-detach-ticket/1/1'){
		
		moxios.uninstall();
		
		moxios.install();
		
		moxios.stubRequest(url,{
			
			status: status,
			
			response : {}
		})
	}
});