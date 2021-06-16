import { shallow, createLocalVue,  mount,shallowMount } from '@vue/test-utils'
import sinon from 'sinon'
import Vue from 'vue'
import Vuex from 'vuex'
import Problem from '../../../views/js/components/Problem/Problem.vue';
import moxios from 'moxios';

 window.location.replace = jest.fn()

Vue.use(Vuex)

window.scrollTo = () => { };

window.eventHub = new Vue();

import * as extraLogics from "helpers/extraLogics";

import * as validation from "../../../views/js/validator/problemValidation";

jest.mock('helpers/responseHandler')

describe('Problem',() => {

	let wrapper;

	let store
	
	let getters

	let actions

	const updateWrapper = () =>{

		getters = {
			getUserData: () => () => {return {system : {url : '' },user_data : {user_language:''}}},

			getStoredTicketId : ()=>{ return 1 }
		},
		
		actions ={
	    	updateTicketActions : jest.fn()
	    },

		store = new Vuex.Store({
			getters,
			actions
		})
		extraLogics.getIdFromUrl = () =>{return 1}
			wrapper = mount(Problem,{
			stubs: ['text-field','dynamic-select','ck-editor','custom-loader','alert','file-upload'],
			mocks:{
				lang:(string)=>string
			},
			store
		})  
	}
	
	beforeEach(() => {
		 
		updateWrapper();

		moxios.install();

		moxios.stubRequest('/service-desk/api/problem/1',{
			
			status: 200,
			
			response: fakeResponse
		})
	})

	afterEach(() => {
		moxios.uninstall()
	})

	it('is vue instance',() => {		
		expect(wrapper.isVueInstance()).toBeTruthy()
	});

	it('makes an API call', (done) => {
			updateWrapper();
			wrapper.vm.getInitialValues(1);
			stubRequest();
			setTimeout(()=>{
				expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/problem/1')
				done();
			},50)
	 })

	it('updates state data correctly(according to the key) when `updateStatesWithData` is called',() => {
		var data = { subject :'test' }
		wrapper.vm.updateStatesWithData(data);
		expect(wrapper.vm.subject).toBe('test');
	});

	it('shows loader if data is not populated and loading is true', () => {
		wrapper.vm.$data.hasDataPopulated = false;
		wrapper.vm.$data.loading = true;
		expect(wrapper.find('custom-loader-stub').exists()).toBe(true)
	})

	it('shows loader if data is populated but loading is true', () => {
		wrapper.vm.$data.hasDataPopulated = true;
		wrapper.vm.$data.loading = true;
		expect(wrapper.find('custom-loader-stub').exists()).toBe(true)
	})

	it('does not show loader if data is populated and loading is false', () => {
		wrapper.vm.$data.hasDataPopulated = true;
		wrapper.vm.$data.loading = false;
		expect(wrapper.find('custom-loader-stub').exists()).toBe(false)
	})

	it('shows loader in edit mode initially when API call is made',(done)=>{
		wrapper.vm.getInitialValues(49);
		expect(wrapper.find('custom-loader-stub').exists()).toBe(true);
		done();
	})

	it('does not show body if `hasDataPopulated` is false',(done)=>{
		wrapper.setData({hasDataPopulated: false});
		wrapper.vm.getInitialValues(1);
		expect(wrapper.find('.box-primary').exists()).toBe(false);
		done();
	})

	it('populates component data as soon as API response is a success in edit mode',(done) => {
		wrapper.vm.getInitialValues(1);
		setTimeout(()=>{
			wrapper.vm.updateStatesWithData(fakeResponse.data.data);
			expect(wrapper.vm.subject).toEqual(fakeResponse.data.data.subject)
			done();
		},1)
	})

	it('updates `subject` of the problem when onChange method is called with suitable parameters for problem name',()=>{
		wrapper.vm.onChange('problem 1', 'subject');
		expect(wrapper.vm.subject).toBe('problem 1');
	})

	it('updates `department_id`value as `""` when onChange method is called with null parameter',()=>{
		wrapper.vm.onChange(null, 'department_id');
		expect(wrapper.vm.department_id).toBe('');
	})

	it('calls `onSubmit` when clicks on Save button',()=>{
		updateWrapper()

		wrapper.vm.onSubmit =jest.fn()

		wrapper.setData({ loading : false, hasDataPopulated : true})

		wrapper.vm.isValid = () =>{return true}

		wrapper.find('#submit_btn').trigger('click')

		expect(wrapper.vm.onSubmit).toHaveBeenCalled()

	});

	it('makes an AJAX call when onSubmit method is called',(done)=>{

		updateWrapper()

		wrapper.setData({ loading : false, hasDataPopulated : true})

		wrapper.vm.isValid = () =>{return true}

		wrapper.vm.onSubmit()

		mockSubmitRequest();

		setTimeout(()=>{
				expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/problem/')
				done();
		},1);
	});

	it('Updates `loading` value correctly when onSubmit method is called',(done)=>{

		updateWrapper()
			
		expect(wrapper.vm.loading).toBe(false)

		wrapper.setData({ loading : false, hasDataPopulated : true})

		wrapper.vm.isValid = () =>{return true}

		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)

		mockSubmitRequest();

		setTimeout(()=>{

			expect(wrapper.vm.loading).toBe(false)
				
			done();
			
		},1);

	});

	it("updates base value when store value changed",()=>{

		 store.hotUpdate({
			getters: {
			  ...getters,
			  getUserData: () => {return { system : { url :'new'}}}
			}
		})

		const wrapper = shallowMount(Problem, {
			stubs: ['text-field','dynamic-select','ck-editor','custom-loader','alert','file-upload'],
			mocks:{
				lang:(string)=>string
			}, store 
		})

		expect(wrapper.vm.$data.base).toBe(wrapper.vm.getUserData.system.url)

	})

	it("updates `title value when page is in edit`",()=>{
		
		updateWrapper()
		wrapper.vm.getValues('/10/edit');
		expect(wrapper.vm.$data.title).toEqual('edit_problem')
	});

	it('updates `subject,loading,hasDataPopulated` value', done => { 
	  
		updateWrapper()

		wrapper.vm.getValues('/');

		expect(wrapper.vm.$data.subject).toEqual('')

		expect(wrapper.vm.$data.loading).toEqual(false)

		expect(wrapper.vm.$data.hasDataPopulated).toEqual(true)

		const wrapper1 = shallowMount(Problem, { 
			propsData : { box_class : 'box', title_val : 'new_title'},
			mocks:{ lang:(string)=>string },
			store 
		})

	   	expect(wrapper1.vm.$data.subject).toEqual('new_title')
	  
	  	done()
	});

	it("makes `loading` as false if api returns error response",(done)=>{

		updateWrapper();

		updateWrapper();

		wrapper.vm.getInitialValues(1);

		stubRequest(400);

		setTimeout(()=>{

			expect(wrapper.vm.$data.loading).toBe(false)

			done();
		},50)
	})

	it('call `isValid` method when onSubmit method is called',(done)=>{

	    updateWrapper()

		wrapper.setData({ loading : false, hasDataPopulated : true})

	    wrapper.vm.isValid =jest.fn()   

	    wrapper.vm.onSubmit()

	    expect(wrapper.vm.isValid).toHaveBeenCalled()

	    setTimeout(()=>{
	     
	      done();
	    },1);

  	});

	it('isValid - should return false ', done => {
       	
       	validation.validateProblemSettings = () =>{return {errors : [], isValid : false}}
      
      	expect(wrapper.vm.isValid()).toBe(false)
      
      	done()
    })

    it('isValid - should return true ', done => {
       
       	validation.validateProblemSettings = () =>{return {errors : [], isValid : true}}
      
      	expect(wrapper.vm.isValid()).toBe(true)
      
      	done()
    })

    it('updates `apiUrl,classname,show_err,alert` value',  done=> { 
	  
		updateWrapper()

		wrapper.setData({ loading : false, hasDataPopulated : true})

		wrapper.vm.isValid = () =>{return true}

		wrapper.vm.onSubmit();

		expect(wrapper.vm.$data.apiUrl).toEqual('/service-desk/api/problem/')

		store.hotUpdate({
            getters: {
              ...getters,
              getStoredTicketId: () => {return 2},
              getUserData: () => { return { system : { url :'s'}}}
            }
        })

		const wrapper2 = mount(Problem, { 
			propsData : { box_class : 'box'},
			mocks:{ lang:(string)=>string },
			store 
		})

		wrapper2.setData({ loading : false, hasDataPopulated : true})

		wrapper2.vm.isValid = () =>{return true}
		
		wrapper2.vm.onSubmit();
		
		expect(wrapper2.vm.$data.apiUrl).toEqual('/service-desk/api/problem?ticket_id=2')

		done()
	});

	 it("calls `actionMetohd` when api return success response", (done)=>{

    	updateWrapper()

    	const wrapper = shallowMount(Problem, { 
			propsData : { box_class : 'box'},
			mocks:{ lang:(string)=>string },
			store 
		})

    	mockSubmitRequest()

    	wrapper.setData({ loading : false, hasDataPopulated : true})

    	wrapper.vm.isValid = () =>{return true}

    	wrapper.vm.actionMethod = jest.fn();

	    wrapper.vm.onSubmit()

	    mockSubmitRequest1()
      	
    	setTimeout(()=>{
			
			expect(wrapper.vm.actionMethod).toHaveBeenCalled()
			
      		done();
    	},1)
    });

	 //For REFERENCE
	// it("calls `updateTicketActions` store actions when `actionMethod` called",()=>{

	// 	updateWrapper()

	//  	jest.useFakeTimers();
	 	
	//  	wrapper.vm.actionMethod()
	 	
	//  	jest.runAllTimers()
	 	
	//  	expect(actions.updateTicketActions).toHaveBeenCalled()
	// });

	function mockSubmitRequest(){
		moxios.uninstall();
		moxios.install();
		moxios.stubRequest('/service-desk/api/problem/',{
			status: 200,
			response: {'success':true,'message':'successfully saved'}
		})
	}

	function mockSubmitRequest1(){
		moxios.uninstall();
		moxios.install();
		moxios.stubRequest('/service-desk/api/problem?ticket_id=1',{
			status: 200,
			response: {'success':true,'message':'successfully saved'}
		})
	}

	function stubRequest(status = 200,url = '/service-desk/api/problem/1'){
	    moxios.uninstall();
	    moxios.install();
	    moxios.stubRequest(url,{
	      status: status,
	      response : {}
	    })
	  }


	let fakeResponse = {
		success:true,
		data : {
			data : {
				subject : 'test'
			}
		}
	}
})