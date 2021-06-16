import {  mount } from '@vue/test-utils'

import Vue from 'vue'

import AssetAssociates from '../../../../views/js/components/Asset/View/AssetAssociates.vue';

import moxios from 'moxios';

window.eventHub = new Vue();

import * as extraLogics from "helpers/extraLogics";

describe('AssetAssociates',() => {

	let wrapper;

	const updateWrapper = () =>{

		wrapper = mount(AssetAssociates,{

			stubs: ['alert','asset-activity','asset-associate-table','loader'],
			
			mocks:{ trans:(string)=>string },

			propsData : { assetId : 1 }
		})  
	}
	
	beforeEach(() => {
		
		updateWrapper();
		
		moxios.install();
	})

	afterEach(() => {
		
		moxios.uninstall()
	})

	it("updates `columns` value when `category` changed",()=>{

		wrapper.setData({ category : 'problem'});

		expect(wrapper.vm.columns).toEqual(['identifier', 'subject', 'requester','assignedTo','priority', 'department', 'status_type_id','action'])

		wrapper.setData({ category : 'change'});

		expect(wrapper.vm.columns).toEqual(['identifier','subject','requester','status','priority','department','created_at','action'])

		wrapper.setData({ category : 'release'});

		expect(wrapper.vm.columns).toEqual(['identifier','subject','release_type','priority', 'status','planned_start_date','planned_end_date','action'])

		wrapper.setData({ category : 'contract'});

		expect(wrapper.vm.columns).toEqual(['identifier','name','cost','expiry','contract_status','contract_renewal_status','vendor','contract_type','action'])

		wrapper.setData({ category : 'ticket'});

		expect(wrapper.vm.columns).toEqual(['ticket_number', 'title', 'assigned','action'])
	});

	it("updates `sortable` value when `category` changed",()=>{

		wrapper.setData({ category : 'problem'});

		expect(wrapper.vm.sortable).toEqual(['subject', 'identifier'])

		wrapper.setData({ category : 'change'});

		expect(wrapper.vm.sortable).toEqual(['identifier','subject','created_at'])

		wrapper.setData({ category : 'release'});

		expect(wrapper.vm.sortable).toEqual(['subject', 'identifier', 'planned_start_date', 'planned_end_date'])

		wrapper.setData({ category : 'contract'});

		expect(wrapper.vm.sortable).toEqual(['identifier','name', 'contract_number', 'expiry','cost'])

		wrapper.setData({ category : 'ticket'});

		expect(wrapper.vm.sortable).toEqual(['ticket_number'])
	});

	it("updates `filterable` value when `category` changed",()=>{

		wrapper.setData({ category : 'problem'});

		expect(wrapper.vm.filterable).toEqual(['subject', 'identifier'])

		wrapper.setData({ category : 'change'});

		expect(wrapper.vm.filterable).toEqual(['identifier','subject','requester','status','priority','department'])

		wrapper.setData({ category : 'release'});

		expect(wrapper.vm.filterable).toEqual(['identifier','subject','release_type','priority', 'status','planned_start_date','planned_end_date'])

		wrapper.setData({ category : 'contract'});

		expect(wrapper.vm.filterable).toEqual(['identifier','name','cost','expiry','contract_status','contract_renewal_status','vendor','contract_type'])

		wrapper.setData({ category : 'ticket'});

		expect(wrapper.vm.filterable).toEqual(['ticket_number'])
	});

	it("calls `getActionsList & associates` method when `refreshTabs` method called",()=>{

		wrapper.vm.getActionsList = jest.fn();

		wrapper.vm.associates = jest.fn();

		wrapper.vm.refreshTabs('activity');

		expect(wrapper.vm.getActionsList).toHaveBeenCalled();

		expect(wrapper.vm.associates).toHaveBeenCalledWith('activity');
	});

	it("makes an API Call when `getActionsList` method called",(done)=>{

		wrapper.vm.getActionsList();

		actionsRequest();

		setTimeout(()=>{

			expect(wrapper.vm.tabs).not.toEqual('');

			done();
		})
	});

	it("makes `loading` value as false when api returns error response",(done)=>{

		wrapper.vm.getActionsList();

		actionsRequest(400);

		setTimeout(()=>{

			expect(wrapper.vm.tabs).toEqual([]);

			done();
		})
	});

	it("calls `getActionsList & associates` method when `updateAssociates` method called",()=>{

		wrapper.vm.getActionsList = jest.fn();

		wrapper.vm.associates = jest.fn();

		wrapper.vm.updateAssociates('problems');

		expect(wrapper.vm.getActionsList).toHaveBeenCalled();

		expect(wrapper.vm.associates).toHaveBeenCalledWith('problem');
	});

	it("updates `category & loading` value when `associates` method called",(done)=>{

		wrapper.vm.associates('contract');

		expect(wrapper.vm.loading).toEqual(true);

		setTimeout(()=>{

			expect(wrapper.vm.loading).toEqual(false);

			done();
		},2);

		expect(wrapper.vm.category).toEqual('contract');
	})
	
	function actionsRequest(status = 200, url = '/service-desk/api/asset-action/1') {

		moxios.uninstall();
	  
	  	moxios.install();

		moxios.stubRequest(url,{

			status: status,
	    
	    	response : { data : { data : { asset_actions : {}}} }
		})
	}
})