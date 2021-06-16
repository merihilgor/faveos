import BarcodeSettings from '../../../../views/js/components/Admin/Barcode/BarcodeSettings';
import { mount, shallowMount } from '@vue/test-utils';
import moxios from 'moxios'
import Vue from 'vue';
import sinon from 'sinon';


const fakeRequestData = {
    'success':true,
    'data':{}
}

describe('BarcodeSettings', () => {

    let wrapper;
  
    beforeEach(() => {
      moxios.install()

     afterEach(() => {
       moxios.uninstall()
     })
  
      wrapper = shallowMount(BarcodeSettings,{
        mocks:{
            stubs:['text-field'],
            lang:(string)=>string,
            toggleModal: jest.fn()
        },
      })
  
    })

    it('textfield and modal should exists when page created', () => {
    
        expect(wrapper.find('text-field-stub').exists()).toBe(true)

    });

    it("toggalModal toggles the modal", (done) => {
      wrapper.setData({showModal:false});

      wrapper.vm.toggleModal();
      
        setTimeout(()=>{
      
          expect(wrapper.vm.showModal).toBe(true)
      
          done();
        },1)
    })

    it('checks for existing templates',done => {
      moxios.stubRequest('service-desk/barcode-template',{
        status:200,
        response:fakeRequestData
     })
     
     wrapper.vm.getExistingCustomTemplateDetails();

      Vue.nextTick(()=> {
        expect(moxios.requests.mostRecent().config.url).toBe('service-desk/barcode-template');
        done();
      }) 


    })


  })