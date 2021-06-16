import { shallow, createLocalVue,  mount } from '@vue/test-utils';

import ChangeCabDetails from '../../../../../../views/js/components/Agent/Changes/View/MiniComponents/ChangeCabDetails';

import Vue from 'vue';

import moxios from 'moxios';

import * as extraLogics from "helpers/extraLogics";

import * as validation from "../../../../../../views/js/validator/cabActionsRules";

jest.mock('helpers/responseHandler')

window.eventHub  = new Vue();

describe('ChangeCabDetails', () => {
  
  let wrapper;

  const updateWrapper = () =>{

    wrapper = mount(ChangeCabDetails, {
    
      propsData: { changeId: 1 },

      mocks: { lang: (string) => string },

      stubs:['loader','alert','text-field'],
    })
  }


  beforeEach(() => {

    updateWrapper();

    moxios.install();
  })

  afterEach(() => {

    moxios.uninstall();
  })

  it('is vue instance',()=>{

    expect(wrapper.isVueInstance()).toBeTruthy()
  });

  it('makes an API call when `getActions` method called', (done) => {
      
    updateWrapper();
      
    wrapper.vm.getActions(1);
    
    stubRequest();

    setTimeout(()=>{
      
      expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/change-action/1')
      
      expect(wrapper.vm.actions).not.toEqual('')

      done();
    },50)
  })

  it("makes `actions` as empty if actions api returns error response",(done)=>{

    updateWrapper();

    wrapper.vm.getActions();

    stubRequest(400);

    setTimeout(()=>{

      expect(wrapper.vm.actions).toEqual('')

      done();
    },50)
  })

  it('makes an API call and calls `getActions` method when `getDataFromServer` method called', (done) => {
      
    updateWrapper();
    
    wrapper.vm.getActions = jest.fn();

    wrapper.vm.getDataFromServer();
    
    expect(wrapper.vm.loading).toBe(true);

    expect(wrapper.vm.cabData).toEqual([]);

    expect(wrapper.vm.getActions).toHaveBeenCalled();

    dataRequest();

    setTimeout(()=>{
      
      expect(wrapper.vm.loading).toBe(false);

      expect(wrapper.vm.cabData).not.toEqual([]);

      expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/change-approval-status/1')

      done();
    },50)
  })

  it('makes `loading` as false and `cabData` as empty array when `getDataFromServer` method returns error', (done) => {
      
    updateWrapper();
    
    wrapper.vm.getDataFromServer();
  
    expect(wrapper.vm.cabData).toEqual([]);

    dataRequest(400);

    setTimeout(()=>{
      
      expect(wrapper.vm.loading).toBe(false);

      expect(wrapper.vm.cabData).toEqual([]);

      done();
    },50)
  })

  it('isValid - should return false ', done => {
        
    validation.validateCabActionsSettings = () =>{return {errors : [], isValid : false}}
      
    expect(wrapper.vm.isValid()).toBe(false)
      
    done()
  })

  it('isValid - should return true ', done => {
       
    validation.validateCabActionsSettings = () =>{return {errors : [], isValid : true}}
      
    expect(wrapper.vm.isValid()).toBe(true)
      
    done()
  })

  it('updates `comment` of the change when onChange method is called with suitable parameters',()=>{
  
    wrapper.vm.onChange('comment', 'comment');
  
    expect(wrapper.vm.comment).toEqual('comment');
  })

  it('updates `action` value and `showModal` as true when `onCabAction` method is called',()=>{
  
    expect(wrapper.vm.showModal).toBe(false);

    wrapper.vm.onCabAction('remove');
  
    expect(wrapper.vm.action).toEqual('remove');
    
    expect(wrapper.vm.showModal).toBe(true);
  })

  it('gives `icon` as `glyphicon glyphicon-time` when `getIconStyle` is called with parameters `PENDING` and `icon`', ()=>{
  
    expect(wrapper.vm.getIconStyle('PENDING','icon')).toBe('glyphicon glyphicon-time text-warning');
  })

  it('gives `icon` as `glyphicon glyphicon-ok` when `getIconStyle` is called with parameters `APPROVED` and `icon`', ()=>{
   
    expect(wrapper.vm.getIconStyle('APPROVED','icon')).toBe('glyphicon glyphicon-check text-success');
  })

  it('gives `icon` as `glyphicon glyphicon-remove` when `getIconStyle` is called with parameters `DENIED` and `icon`', ()=>{
   
    expect(wrapper.vm.getIconStyle('DENIED', 'icon')).toBe('glyphicon glyphicon-exclamation-sign text-danger');
  })

  it('gives info color when `getIconStyle` is called with parameters `PENDING` and `color`', ()=>{
   
    expect(wrapper.vm.getIconStyle('PENDING','color')).toBe('#eee');
  })

  it('gives success color when `getIconStyle` is called with parameters  `APPROVED` and `color`', ()=>{
   
    expect(wrapper.vm.getIconStyle('APPROVED','color')).toBe('#cbe0d3');
  })

  it('gives danger color when `getIconStyle` is called with parameters  `DENIED` and `color`', ()=>{
   
    expect(wrapper.vm.getIconStyle('DENIED','color')).toBe('#f6ddd8');
  })

  it('returns `1` when `getOpacity` is called with `1`', ()=>{
   
    expect(wrapper.vm.getOpacity(1)).toBe(1);
  })

  it('returns `0.5` when `getOpacity` is not called with `1`', ()=>{
   
    expect(wrapper.vm.getOpacity(2)).toBe(0.5);
  })

  it('makes an `post` request when `action` is not equal to `remove`',(done)=>{

    updateWrapper();

    wrapper.setData( { action : 'approve', comment : 'comment'} );

    wrapper.vm.initialActions = jest.fn();

    wrapper.vm.onCabActionConfirm();

    expect(wrapper.vm.loading).toBe(true);

    actionRequest();

    setTimeout(()=>{

      expect(wrapper.vm.loading).toBe(false);

      expect(moxios.requests.mostRecent().config.method).toBe('post')

      expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/approval-action-without-hash/1')

      setTimeout(()=>{

        expect(wrapper.vm.initialActions).toHaveBeenCalled();
        
        done()
      },2100);
    },1)
  })

  it('makes an loading as `false` when `post` api returns error',(done)=>{

    updateWrapper();

    wrapper.setData( { action : 'deny', comment : 'comment'} );

    wrapper.vm.onCabActionConfirm();

    actionRequest(400);

    setTimeout(()=>{

      expect(wrapper.vm.loading).toBe(false);
      
      done()
    },1)
  })

  it('makes an removeRequest when `action` is `remove`',(done)=>{

    updateWrapper();
    
    wrapper.setData( { action : 'remove'} );

    wrapper.vm.initialActions = jest.fn();

    wrapper.vm.onCabActionConfirm();

    expect(wrapper.vm.loading).toBe(true);

    removeRequest();

    setTimeout(()=>{

      expect(wrapper.vm.loading).toBe(false);

      expect(moxios.requests.mostRecent().config.method).toBe('delete')

      expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/remove-cab-approval/1')

      setTimeout(()=>{

        expect(wrapper.vm.initialActions).toHaveBeenCalled();

        done()
      },2100);
    },1)
  })

  it('makes an loading as `false` when `delete` api returns error',(done)=>{

    updateWrapper();

    wrapper.setData( { action : 'remove'} );

    wrapper.vm.onCabActionConfirm();

    removeRequest(400);

    setTimeout(()=>{

      expect(wrapper.vm.loading).toBe(false);
      
      done()
    },1)
  })

  it('updates `action,comment` value, calls `getDataFromServer` method and `showModal` as false when `initialActions` method is called',()=>{
    
    wrapper.vm.getDataFromServer = jest.fn();

    wrapper.vm.initialActions();

    expect(wrapper.vm.showModal).toBe(false);
  
    expect(wrapper.vm.action).toEqual('');

    expect(wrapper.vm.comment).toEqual('');
    
    expect(wrapper.vm.getDataFromServer).toHaveBeenCalled();
  })

  it('updates `modalTitle` value based on `action`',()=>{

    wrapper.setData( { action : 'remove'} );

    expect(wrapper.vm.modalTitle).toEqual("remove_cab")

    wrapper.setData( { action : 'approve'} );

    expect(wrapper.vm.modalTitle).toEqual("approve_cab")
  })

  it('updates `modalQuestion` value based on `action`',()=>{

    extraLogics.lang = () => { return 'Are you sure you want to' }

    wrapper.setData( { action : 'remove'} );

    expect(wrapper.vm.modalQuestion).toEqual("Are you sure you want to remove?")
  })

  function stubRequest(status = 200,url = '/service-desk/api/change-action/1'){
    
    moxios.uninstall();
    
    moxios.install();
    
    moxios.stubRequest(url,{
      
      status: status,
      
      response : {
        data : {
          actions : {
            allowed_cab_action: true,
            allowed_enforce_cab: false,
            change_close: true,
            change_create: true,
            change_delete: true,
            change_edit: true,
            change_release_attach: true,
            change_release_detach: false,
            change_view: true,
            remove_cab: true,
            view_cab_progress: true,
          }
        }
      }
    })
  }

  function dataRequest(status = 200,url = '/service-desk/api/change-approval-status/1'){
    
    moxios.uninstall();
    
    moxios.install();
    
    moxios.stubRequest(url,{
      
      status: status,
      
      response : {
        data : [{
          id : 1, 
          approval_workflow_id : 1,
          name : "test_cab",
          change_id : 1,
          created_at : "2018-10-11 10:01:55",
          updated_at : "2018-10-11 10:01:55",
          approval_levels : [{
            id : 1, 
            status : "PENDING",
            is_active : 1,
            name : "test_level_1",
            approve_users : [{ id : 1, status : "PENDING", name : "test_user"}],
            approve_user_types : [{ id : 4, name : "department_manager", status : "PENDING" }]
          }]
        }]
      }
    })
  }

  function removeRequest(status = 200,url = '/service-desk/api/remove-cab-approval/1'){
    
    moxios.uninstall();
    
    moxios.install();
    
    moxios.stubRequest(url,{
      
      status: status,
      
      response : {}
    })
  }

  function actionRequest(status = 200,url = '/service-desk/api/approval-action-without-hash/1'){
    
    moxios.uninstall();
    
    moxios.install();
    
    moxios.stubRequest(url,{
      
      status: status,
      
      response : {}
    })
  }
})