import { mount, createLocalVue } from '@vue/test-utils';

import AttachChangeModal from '../../../../views/js/components/Ticket/Changes/AttachChangeModal.vue';

import Vue from 'vue';

import moxios from 'moxios';

const localVue = createLocalVue();

window.eventHub = new Vue();

describe('AttachChangeModal',()=>{

	let wrapper;

	beforeEach(()=>{
		
		moxios.install();
	});

	afterEach(()=>{
		
		moxios.uninstall();
	})

	const updateWrapper = ()=> {

		wrapper = mount(AttachChangeModal,{

			propsData : {

				showModal : true,

				onClose : jest.fn(),

				title : 'link_to_an_existing_change',

				type : 'initiating',

				data: JSON.stringify({ id : 1})
			},

			stubs : ['modal','alert','custom-loader','change-create','dynamic-select'],

			mocks : { lang:(string)=> string }
		}) 
	};

	beforeEach(()=>{

		updateWrapper()
	});

	it('Is vue instance',()=>{

		expect(wrapper.isVueInstance()).toBeTruthy();
	});

	it('updates `ticketId` value', done => { 
			
		expect(wrapper.vm.ticketId).toEqual(1)

		wrapper.setProps({
			
			data: JSON.stringify({ id : 2})
		})
			
		expect(wrapper.vm.ticketId).toEqual(2)
			
		done()
	})

	it("makes an API call when `onSubmit` method called",(done)=>{

		wrapper.setProps({ data: JSON.stringify({ id : 2})})

		wrapper.setData({ change : { id :1, name : 'name'}});

		wrapper.vm.onCompleted = jest.fn();

		wrapper.vm.onSubmit();

		expect(wrapper.vm.loading).toEqual(true);

		mockSubmitRequest(200);

		setTimeout(()=>{

			expect(moxios.requests.mostRecent().url).toBe("/service-desk/api/attach-change/ticket")

			expect(wrapper.vm.onCompleted).toHaveBeenCalled()

			done();
		},1)
	})

	it("makes `loading` as false when `onSubmit` method returns error",(done)=>{

		wrapper.setProps({ data: JSON.stringify({ id : 2})})

		wrapper.setData({ change : { id :1, name : 'name'}});

		wrapper.vm.onCompleted = jest.fn();

		wrapper.vm.onSubmit();

		expect(wrapper.vm.loading).toEqual(true);

		mockSubmitRequest(400);

		setTimeout(()=>{

			expect(moxios.requests.mostRecent().url).toBe("/service-desk/api/attach-change/ticket")

			expect(wrapper.vm.onCompleted).not.toHaveBeenCalled()

			expect(wrapper.vm.loading).toEqual(false);

			done();
		},1)
	})

	it("calls child component `onSubmit` method when `title` is `create_a_change_to_this_ticket`",()=>{

		wrapper.setProps({ data: JSON.stringify({ id : 2}), title : 'create_a_change_to_this_ticket'})

		wrapper.vm.$refs.attachChange.onSubmit = jest.fn()
		
		wrapper.vm.onSubmit();

		expect(wrapper.vm.$refs.attachChange.onSubmit).toHaveBeenCalled()
	})

	it('updates `loading` as false when `onCompleted` method called',()=>{

		wrapper.vm.onCompleted();

		expect(wrapper.vm.loading).toBe(false);
	});

	it('updates `change` value when onChange method is called',()=>{
    	
    wrapper.vm.onChange({ id: 1, name : 'name'},'change');
    	
    expect(wrapper.vm.change).toEqual({ id: 1, name : 'name'});

    expect(wrapper.vm.isDisabled).toEqual(false)

     wrapper.vm.onChange('','change');
    	
    expect(wrapper.vm.change).toEqual('');

    expect(wrapper.vm.isDisabled).toEqual(true)
  	
  });
	
	function mockSubmitRequest(status = 200, url = '/service-desk/api/attach-change/ticket'){

		moxios.stubRequest(url,{

			status : status,

			response : {

				success : true,

				message : 'successfully saved'
			}
		})
	}
});