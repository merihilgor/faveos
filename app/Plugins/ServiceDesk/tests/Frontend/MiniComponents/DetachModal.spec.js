import { mount ,createLocalVue, shallowMount} from '@vue/test-utils'

import DetachModal from '../../../views/js/components/MiniComponents/DetachModal'

import Vue from 'vue'

import Vuex from 'vuex'

import sinon from 'sinon'

import moxios from 'moxios'

jest.mock('helpers/responseHandler')

jest.mock('helpers/extraLogics')

window.eventHub = new Vue();

let wrapper;

const localVue = createLocalVue()

localVue.use(Vuex)

describe('DetachModal',()=>{

	let getters

	let actions

	let store

	beforeEach(()=>{

		moxios.install()

		getters = {
	    
	      getStoredTicketId : ()=>{ return 1 }
	    },
	    
	    actions ={
	    	updateTicketActions : jest.fn()
	    },

	   	store = new Vuex.Store({
	    
	      getters,
	      actions
	    })
	});

	afterEach(()=>{
		
		moxios.uninstall()
	});

	const populateWrapper = ()=>{
	   	
	   	wrapper = mount(DetachModal,{
	   	
	   	mocks : { lang:(string)=> string },
	   	
	   	propsData : { from : 'modal_1', onClose : Function},
		
		stubs:['modal','alert','loader'],
		
			store,
	    
	        localVue
		});
	}
	
	it('Is a vue instance',()=>{
		
		populateWrapper()

		expect(wrapper.isVueInstance()).toBe(true)	
	});

	it('Does not show modal popup if showModal is false',() => {

		expect(wrapper.find('modal-stub').exists()).toBe(false)
	});

	it('Show modal popup if showModal is true',() => {
		
		populateWrapper()

		wrapper.setProps({ showModal : true })

		expect(wrapper.find('modal-stub').exists()).toBe(true)
	});

	it('Does not show modal popup `title` if loading is true',() => {

		wrapper.setData({ loading : true})

		expect(wrapper.find('#H5').exists()).toBe(false)
	});

	it('Show modal popup `title` if loading is false',() => {
		
		populateWrapper()

		wrapper.setData({ loading : false})

		wrapper.setProps({ showModal : true })

		expect(wrapper.find('#H5').exists()).toBe(true)
	});

	it('renders correct title',()=>{
	
		expect(wrapper.find("h4").text()).toBe("detach")
	});

	it('`alert-stub` should exists when page created', () => {

		wrapper.setProps({ showModal : true });

        expect(wrapper.find('alert-stub').exists()).toBe(true)
    });

	it('updates classname when language changed to `ar`',()=>{

		populateWrapper()

		wrapper.setData({ lang_locale : 'ar'})

		wrapper.setData({ loading : false})

		wrapper.setProps({ showModal : true })
		
		expect(wrapper.find('#H5').attributes().class).toEqual('margin')
	})


	it('Initialy loading value should be false', () => {
	
		populateWrapper()
	
		expect(wrapper.vm.loading).toBe(false);
	});

	it('Does not show loader if `loading` is false',()=>{
		
		populateWrapper()
		
		wrapper.setProps({ showModal : true });

		wrapper.setData({ loading : false });

		expect(wrapper.find('loader-stub').exists()).toBe(false);
	});

	it('Calls `onSubmit` method when clicks on `Detach` button',()=>{
		
		populateWrapper()
		
		wrapper.vm.onSubmit = jest.fn()
		
		wrapper.setData({ loading : false })
		
		wrapper.setProps({ showModal : true })
		
		wrapper.find('#submit_btn').trigger('click')
		
		expect(wrapper.vm.onSubmit).toHaveBeenCalled()
	});
	
	it('makes a delete api call as soon as `onSubmit` is called',(done)=>{
		
		populateWrapper()

		wrapper.vm.$options.props.onClose = jest.fn()

		stubRequest(400,'/service-desk/api/detach-asset/'+wrapper.vm.ticketId+'/'+wrapper.vm.assetId)
		
		wrapper.vm.onSubmit()

    	setTimeout(()=>{
    		expect(wrapper.vm.$options.props.onClose).not.toHaveBeenCalled()
      		done();
    	},1)
	})

		 it("updates `apiUrl` value and calls `actionMetohd` when api return success response", (done)=>{

    	populateWrapper()

    	wrapper.vm.actionMethod = jest.fn();

    	wrapper.setProps({ problemId : 1, from : 'modal'})

    	wrapper.setData({ ticketId : 1})

    	stubRequest(200,'/service-desk/api/problem-detach-ticket/'+wrapper.vm.problemId+'/'+wrapper.vm.ticketId)

	    wrapper.vm.isValid = () =>{return true}

	    wrapper.vm.onSubmit()
      	
    	setTimeout(()=>{
      		
      		expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/problem-detach-ticket/1/1')
			
      		done();
    	},1)
    });


	it('Makes an API call when `onSubmit` method called', (done) => {
    	
    	populateWrapper();
    	
    	wrapper.setData({ loading : false, ticketId : 1 })
		
		wrapper.setProps({ showModal : true, assetId : 1, from : 'from' })

		stubRequest(200,'/service-desk/api/detach-asset/'+wrapper.vm.ticketId+'/'+wrapper.vm.assetId)
		
		wrapper.vm.onSubmit()
    	
    	setTimeout(()=>{
      	
		  	expect(moxios.requests.__items.length).toBe(1)

		  	expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/detach-asset/1/1')
		 	done();
		},1)
	});

	it('makes a delete api call as soon as `onSubmit` is called',(done)=>{
		
		populateWrapper()

		wrapper.vm.$options.props.onClose = jest.fn()

		stubRequest(200,'/service-desk/api/detach-asset/'+wrapper.vm.ticketId+'/'+wrapper.vm.assetId)
		
		wrapper.vm.onSubmit()

    	setTimeout(()=>{
      		
      		expect(moxios.requests.mostRecent().config.method).toBe('delete');
			
      		done();
    	},50)
	})

	it('updates `loading`  value correctly when `onSubmit` method is called',(done) => {

	    populateWrapper()

	    expect(wrapper.vm.loading).toBe(false)

	    wrapper.setProps({ showModal : true , assetId : 1})

	    stubRequest(200,'/service-desk/api/detach-asset/'+wrapper.vm.ticketId+'/'+wrapper.vm.assetId)

	    wrapper.vm.isValid = () =>{return true}

	    wrapper.vm.onSubmit()

	     expect(wrapper.vm.loading).toBe(true)

	    setTimeout(()=>{
	    	
	    	expect(moxios.requests.__items.length).toBe(1)
      
      		expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/detach-asset/1/1')
	      
	      	setTimeout(()=>{

	      		expect(wrapper.vm.loading).toBe(false)
	      	},1)
	      done();
	    },50);
	});


	 it("calls `updateTicketActions` store actions and `onCLose` method when `actionMethod` called",()=>{
	 	
	 	populateWrapper()

    	 wrapper.setProps({onClose: jest.fn(()=>{})});

	 	jest.useFakeTimers();
	 	
	 	wrapper.vm.actionMethod()
	 	
	 	jest.runAllTimers()

	    expect(wrapper.vm.onClose).toHaveBeenCalled()

	 })

	it("ticketId value should be equal to getStoredTicketId value",()=>{
	   
	    expect(wrapper.vm.$data.ticketId).toBe(getters.getStoredTicketId())

	})

	it("updates ticketId value when store value changed",()=>{

		 store.hotUpdate({
            getters: {
              ...getters,
              getStoredTicketId: () => {return 2}
            }
        })

		const wrapper = shallowMount(DetachModal, { store, localVue })

	    expect(wrapper.vm.$data.ticketId).toBe(wrapper.vm.getStoredTicketId)

	})

	 function stubRequest(status = 200,url = '/service-desk/api/detach-asset/'+wrapper.vm.assetId+'/'+wrapper.vm.ticketId){
	    moxios.uninstall();
	    moxios.install();
	    moxios.stubRequest(url,{
	      status: status,
	      response : {}
	    })
	  }
});