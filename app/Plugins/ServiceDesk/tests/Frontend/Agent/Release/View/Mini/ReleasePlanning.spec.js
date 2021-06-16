import { shallow, createLocalVue,  mount } from '@vue/test-utils'

import sinon from 'sinon'

import Vue from 'vue'

import Vuex from 'vuex'

import ReleasePlanning from '../../../../../../views/js/components/Agent/Release/View/Mini/ReleasePlanning.vue';

import moxios from 'moxios';

window.eventHub = new Vue();

let localVue = createLocalVue()

localVue.use(Vuex)

describe('ReleasePlanning',() => {

	let wrapper;

	let actions
  
  let store

  let getters;

	getters = {

		formattedTime: () => () => {return ''},
		
		formattedDate:()=> () => {return ''},
	}

  actions = { unsetValidationError: jest.fn() }

 	store = new Vuex.Store({ actions, getters })

	const updateWrapper = () =>{

		wrapper = mount(ReleasePlanning,{

			stubs: ['release-update-modal','release-planning-delete','loader'],
			
			mocks:{ trans:(string)=>string },

			methods : {basePath : jest.fn() },

			propsData : { releaseId : 1 },

			store,

			attachToDocument: true,
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
			
		wrapper.vm.getDetails();
		
		expect(wrapper.vm.$data.loading).toBe(true)

		mockPlanningRequest(200);

		setTimeout(()=>{
			
			expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/release-planning/1')
			
			expect(wrapper.vm.$data.loading).toBe(false)

			done();
		},50)
	})

	it("makes `loading` as false if api returns error response",(done)=>{

		updateWrapper();

		wrapper.vm.getDetails();

		mockPlanningRequest(400);

		setTimeout(()=>{

			expect(wrapper.vm.$data.loading).toBe(false)

			done();
		},50)
	});

	it('updates `showChangeUpdate, identifier` values when `updateMethod` method called',()=>{

		wrapper.vm.updateMethod('test-plan');

		expect(wrapper.vm.showReleaseUpdate).toEqual(true)

		expect(wrapper.vm.identifier).toEqual('test-plan')
	})

	it('updates `showUpdateDelete, identifier` values when `deleteMethod` method called',()=>{

		wrapper.vm.deleteMethod('build-plan');

		expect(wrapper.vm.showUpdateDelete).toEqual(true)

		expect(wrapper.vm.identifier).toEqual('build-plan')
	})

	it("`showModal` should be `false` when `onClose` method called",()=>{

		updateWrapper();

		wrapper.vm.onClose();

		expect(wrapper.vm.showUpdateDelete).toBe(false);
		
		expect(wrapper.vm.showReleaseUpdate).toBe(false);

		expect(actions.unsetValidationError).toHaveBeenCalled();
	})
	
	function mockPlanningRequest(status = 200 , url = '/service-desk/api/release-planning/1'){

		moxios.uninstall();
	   
	  moxios.install();
	   
	  moxios.stubRequest(url,{
	   
	    status: status,
	   
	    response : {
	    	data : {
	    		planning_popups : [{
	    			attachment: null,
						created_at: "2019-11-23 09:58:29",
						description: "<p>test</p>↵",
						id: 7,
						key: "root-cause",
						owner: "sd_releases:2",
						updated_at: "2019-11-23 09:58:29",
	    		}]
	    	}
	    }
	  })
	}
})