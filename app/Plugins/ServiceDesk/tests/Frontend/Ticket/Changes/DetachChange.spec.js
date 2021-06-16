import { mount ,createLocalVue, shallowMount} from '@vue/test-utils'

import DetachChange from '../../../../views/js/components/Ticket/Changes/DetachChange'

import Vue from 'vue'

import Vuex from 'vuex'

import moxios from 'moxios'

jest.mock('helpers/responseHandler')

window.eventHub = new Vue();

let wrapper;

const localVue = createLocalVue()

localVue.use(Vuex)

describe('DetachChange',()=>{

	let actions

	let store

	beforeEach(()=>{

		moxios.install()

		actions = {
			
			updateTicketActions: jest.fn()
		}
		
		store = new Vuex.Store({
			
			actions
		})
	});

	afterEach(()=>{
		
		moxios.uninstall()
	});

	const populateWrapper = ()=>{
			
		wrapper = mount(DetachChange,{
			
			mocks : { lang:(string)=> string },
			
			propsData : { 
				
				ticketId : 1,

				changeId : 1,

				type : 'initiated',

				showModal : true,

				onClose : jest.fn()
			},
		
			stubs:['modal','alert','loader'],
		
			store,
			
			localVue
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

			expect(moxios.requests.mostRecent().url).toBe("/service-desk/api/detach-change/ticket?ticket_id=1&change_id=1&type=initiated")

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
				
			expect(moxios.requests.mostRecent().url).toBe("/service-desk/api/detach-change/ticket?ticket_id=1&change_id=1&type=initiated")
			
			expect(wrapper.vm.onClose).not.toHaveBeenCalled()

			expect(wrapper.vm.loading).toEqual(false)

			expect(wrapper.vm.isDisabled).toEqual(false)

			done();
		},1)
	});

	function mockSubmitRequest(status = 200, url = '/service-desk/api/detach-change/ticket?ticket_id=1&change_id=1&type=initiated'){
		
		moxios.uninstall();
		
		moxios.install();
		
		moxios.stubRequest(url,{
			
			status: status,
			
			response : {}
		})
	}
});