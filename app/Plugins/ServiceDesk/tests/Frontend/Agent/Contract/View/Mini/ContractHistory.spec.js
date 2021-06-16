import { shallow, createLocalVue,  mount } from '@vue/test-utils'

import sinon from 'sinon'

import Vue from 'vue'

import Vuex from 'vuex'

import ContractHistory from '../../../../../../views/js/components/Agent/Contract/View/Mini/ContractHistory.vue';

import moxios from 'moxios';

import * as extraLogics from "helpers/extraLogics";

let localVue = createLocalVue()

localVue.use(Vuex)

window.eventHub = new Vue();

describe('ContractHistory',() => {

	let wrapper;

	let store;

	let getters;

	getters = {

		formattedTime: () => () => {return ''},
		
		formattedDate:()=> () => {return ''},
	}

	store = new Vuex.Store({ getters })

	const updateWrapper = () =>{

		wrapper = mount(ContractHistory,{

			stubs: ['loader'],
			
			mocks:{ trans:(string)=>string },

			methods : { basePath : jest.fn() },

			propsData : { contractId : 1 },

			attachToDocument: true,

			localVue, store
		})  
	}
	
	beforeEach(() => {
		
		updateWrapper();
		
		moxios.install();
	})

	afterEach(() => {
		
		moxios.uninstall()
	})

	it('returns only 15 characters if value length is more than 15 and returns exact value if option length not more than 15',() => {		
		
		expect(wrapper.vm.subString('name name name name name ')).toEqual('name name name ...');

		expect(wrapper.vm.subString('name')).toEqual('name');
	});

	it('makes an API call', (done) => {
			
		updateWrapper();
			
		wrapper.vm.getValues();

		stubRequest();

		setTimeout(()=>{
			
			expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/contract-threads/1')
			
			expect(wrapper.vm.loading).toBe(false)

			expect(wrapper.vm.history).not.toEqual('')

			done();
		},50)
	})

	it("updates `data` values if api returns error response",(done)=>{

		updateWrapper();

		wrapper.vm.getValues();

		stubRequest(400);

		setTimeout(()=>{

			expect(wrapper.vm.loading).toBe(false)

			done();
		},50)
	})

	it('`checkDate` method returns true if it called with zero',()=>{

		expect(wrapper.vm.checkDate(0)).toEqual(true)
	})

	it('`checkDate` method returns undefined(formattedDate method returns empty value so i am assuming it retuns undefined) if it called with one',()=>{

		wrapper.setData( { history : [{ description : 'description', created_at : '20-10-2019',causer_name : { id : 1, user_name :'name'}},{ description : 'description', created_at : '20-10-2019',causer_name : { id : 1, user_name :'name'}}]})
		
		expect(wrapper.vm.checkDate(1)).toEqual(undefined)
	})

	it('`showThreadEnd` method returns true if index is equal to activity_log length',()=>{

		wrapper.setData({ history : [ { created_at : '2019-10-10', description : 'test', causer_name : { id :1, user_name:'user'}}]})
		
		expect(wrapper.vm.showThreadEnd(0)).toEqual(true);
	})


	function stubRequest(status = 200,url = '/service-desk/api/contract-threads/1'){
	  
	  moxios.uninstall();
	  
	  moxios.install();
	  
	  moxios.stubRequest(url,{
	    
	    status: status,
	    
	    response : {
	    	
	    	data : {
	    		
	    		contract_threads : [{ description : 'description', created_at : '20-10-2019',creator : { id : 1, user_name :'name', profile_pic : 'pic'}}],

	    	}
	    }
	  })
	}
})