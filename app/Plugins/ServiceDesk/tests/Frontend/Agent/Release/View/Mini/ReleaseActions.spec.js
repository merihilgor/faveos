
import { shallow, createLocalVue,  mount } from '@vue/test-utils'

import sinon from 'sinon'

import Vue from 'vue'

import Vuex from 'vuex'

import ReleaseActions from '../../../../../../views/js/components/Agent/Release/View/Mini/ReleaseActions.vue';

import moxios from 'moxios';

import * as extraLogics from "helpers/extraLogics";

let localVue = createLocalVue()

window.eventHub = new Vue();

localVue.use(Vuex)

describe('ReleaseActions',() => {

	let wrapper;

	let actions
  
  let store

  actions = { unsetValidationError: jest.fn() }

 	store = new Vuex.Store({ actions })

	const updateWrapper = () =>{

		wrapper = mount(ReleaseActions,{

			stubs: ['release-planning-modal','release-assets','release-change','release-status-modal','delete-modal','loader'],
			
			mocks:{ trans:(string)=>string },

			methods : { basePath : jest.fn() },

			propsData : { release : { id : 1},  },

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

	it('makes an API call', (done) => {
			
		updateWrapper();
			
		wrapper.vm.getActions();
		
		stubRequest();

		setTimeout(()=>{
			
			expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/release-action/1')
			
			expect(wrapper.vm.actions).not.toEqual('')

			done();
		},50)
	})

	it("makes `actions` as empty if api returns error response",(done)=>{

		updateWrapper();

		wrapper.vm.getActions();

		stubRequest(400);

		setTimeout(()=>{

			expect(wrapper.vm.actions).toEqual('')

			done();
		},50)
	})

	it('returns only 15 characters if value length is more than 15 and returns exact value if option length not more than 15',() => {		
		
		expect(wrapper.vm.subString('name name name name name ')).toEqual('name name name ...');

		expect(wrapper.vm.subString('name')).toEqual('name');
	});

	it('updates `showPlanningModal, identifier` values when `planningMethod` method called',()=>{

		wrapper.vm.planningMethod('reason');

		expect(wrapper.vm.showPlanningModal).toEqual(true)

		expect(wrapper.vm.identifier).toEqual('reason')
	})

	it("`showModal` should be `false` when `onClose` method called",()=>{

		updateWrapper();

		wrapper.vm.onClose();
		
		expect(wrapper.vm.showPlanningModal).toBe(false);

		expect(actions.unsetValidationError).toHaveBeenCalled();
	})

	function stubRequest(status = 200,url = '/service-desk/api/release-action/1'){
	  
	  moxios.uninstall();
	  
	  moxios.install();
	  
	  moxios.stubRequest(url,{
	    
	    status: status,
	    
	    response : {
	    	data : {
	    		actions : {}
	    	}
	    }
	  })
	}
})