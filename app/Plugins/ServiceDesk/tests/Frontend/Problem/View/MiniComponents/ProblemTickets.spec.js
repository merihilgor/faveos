import { mount ,createLocalVue, shallowMount} from '@vue/test-utils'

import ProblemTickets from '../../../../../views/js/components/Problem/View/MiniComponents/ProblemTickets.vue';

import Vue from 'vue'

import moxios from 'moxios'

jest.mock('helpers/responseHandler')

window.eventHub = new Vue();

let wrapper;

describe('ProblemTickets',()=>{

  beforeEach(()=>{

    moxios.install()
  });

  afterEach(()=>{
    
    moxios.uninstall()
  });

  const populateWrapper = ()=>{
      
    wrapper = mount(ProblemTickets,{
      
      mocks : { lang:(string)=> string },
      
      propsData : { onClose : jest.fn(), problemId : 1, showModal : true },
    
      stubs:['modal','loader','dynamic-select','alert'],
    
    });
  }

  it('updates `ticket_ids` of the cab when onChange method is called with suitable parameters',()=>{
    
    populateWrapper();
    
    wrapper.vm.onChange([{ id : 1, name :'ticket'}], 'ticket_ids');
  
    expect(wrapper.vm.ticket_ids).toEqual([{ id : 1, name :'ticket'}]);
  })

  it('makes an api call as soon as `onSubmit` is called',(done)=>{
    
    populateWrapper()

    wrapper.setData({ ticket_ids : [{ id : 1, name : 'ticket'}] });

    stubRequest()
    
    wrapper.vm.onSubmit()

    expect(wrapper.vm.loading).toBe(true)
     
    setTimeout(()=>{
      
      expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/problem-attach-ticket/1')

      expect(wrapper.vm.loading).toBe(false)

      expect(wrapper.vm.ticket_ids).toEqual([])

      expect(wrapper.vm.onClose).toHaveBeenCalled();
    
      done();
    },1)
  })

  it('updates `loading` value and not calls `onClose` method if api returns error response',(done)=>{
    
    populateWrapper()

    wrapper.setData({ ticket_ids : [{ id : 1, name : 'ticket'}] });

    stubRequest(400)
    
    wrapper.vm.onSubmit()

    expect(wrapper.vm.loading).toBe(true)
     
    setTimeout(()=>{
      
      expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/problem-attach-ticket/1')

      expect(wrapper.vm.loading).toBe(false)

      expect(wrapper.vm.onClose).not.toHaveBeenCalled();
    
      done();
    },1)
  })

  function stubRequest(status = 200,url = '/service-desk/api/problem-attach-ticket/1'){
     
    moxios.uninstall();
     
    moxios.install();
     
    moxios.stubRequest(url,{
     
      status: status,
     
      response : {}
    })
  }
});