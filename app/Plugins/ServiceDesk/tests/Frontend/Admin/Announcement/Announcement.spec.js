import { mount } from '@vue/test-utils';

import Announcement from '../../../../views/js/components/Admin/Announcement/Announcement';

import moxios from 'moxios';

import Vue from 'vue'

import * as validation from "../../../../views/js/validator/announcementValidationSettings";

jest.mock('helpers/responseHandler')


describe('Announcement',() => {

	let wrapper;

	const updateWrapper = () =>{

			wrapper = mount(Announcement,{

			stubs: ['text-field','dynamic-select','custom-loader','alert','radio-button','ck-editor'],

			mocks:{	lang:(string)=>string },
		})  
	}
	
	beforeEach(() => {
		 
		updateWrapper();

		moxios.install();
	})

	afterEach(() => {
		
		moxios.uninstall()
	})

	it('is vue instance',() => {		
		
		expect(wrapper.isVueInstance()).toBeTruthy()
	});

	it('updates `subject` of the announcement when onChange method is called',()=>{

		wrapper.vm.onChange('announcement', 'subject');

		expect(wrapper.vm.subject).toBe('announcement');
	});

	it('updates `department, organization and showField` values of the announcement when onChange method is called with name `to`',()=>{

		wrapper.vm.onChange('department', 'to');

		expect(wrapper.vm.department).toEqual('');

		expect(wrapper.vm.organization).toEqual('');

		expect(wrapper.vm.showField).toEqual(false);

		setTimeout(()=>{
			
			expect(wrapper.vm.showField).toEqual(true);

		},2)
	})

	it('call `isValid` method when onSubmit method is called',(done)=>{

		updateWrapper()

		wrapper.setData({ loading : false })

		wrapper.vm.getDescription = () =>{return 'description'}

		wrapper.vm.isValid =jest.fn()   

		wrapper.vm.onSubmit()

		expect(wrapper.vm.isValid).toHaveBeenCalled()
	 
		done();
	});

	it('isValid - should return false ', done => {
				
		validation.validateAnnouncementSettings = () =>{return {errors : [], isValid : false}}
			
		expect(wrapper.vm.isValid()).toBe(false)
			
		done()
	})

	it('isValid - should return true ', done => {
			 
		validation.validateAnnouncementSettings = () =>{return {errors : [], isValid : true}}
			
		expect(wrapper.vm.isValid()).toBe(true)
			
		done()
	})

	it('makes an AJAX call when onSubmit method is called',(done)=>{

		updateWrapper()

		wrapper.setData({ loading : false, organization : { id :1, name : 'org'}, department : { id : 1, name : 'dept'} })

		wrapper.vm.isValid = () =>{return true}

		wrapper.vm.getDescription = () =>{return 'description'}

		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)

		mockSubmitRequest();

		setTimeout(()=>{

			expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/announcement')

			expect(wrapper.vm.loading).toBe(false)

			expect(wrapper.vm.department).toEqual('');

			expect(wrapper.vm.organization).toEqual('');
			
			expect(wrapper.vm.to).toEqual('department');

			done();
		},1);
	});

	it('makes an loading as false when onSubmit method return error',(done)=>{

		updateWrapper()

		wrapper.setData({ loading : false })

		wrapper.vm.isValid = () =>{return true}

		wrapper.vm.getDescription = () =>{return 'description'}

		wrapper.vm.onSubmit()

		mockSubmitRequest(400);

		setTimeout(()=>{

			expect(wrapper.vm.loading).toBe(false)

			done();
		},1);
	});

	function mockSubmitRequest(status = 200,url = '/service-desk/api/announcement'){

		moxios.uninstall();

		moxios.install();

		moxios.stubRequest(url,{

			status: status,

			response: {'success':true,'message':'successfully saved'}
		})
	}
})