import { mount, createLocalVue, shallowMount } from '@vue/test-utils';

import ReportNestedFilter from '../../../views/js/components/Report/ReportNestedFilter.vue';

import sinon from 'sinon'

import Vue from 'vue'

import Vuex from 'vuex'

import moxios from 'moxios'

import * as extraLogics from "helpers/extraLogics";

import * as validation from "../../../views/js/validator/assetReportValidation";

jest.mock('helpers/responseHandler')

describe('ReportNestedFilter',() => {

	let wrapper;

	const updateWrapper = () =>{

		extraLogics.getIdFromUrl = () =>{return 1}
		
		wrapper = mount(ReportNestedFilter,{
			
			stubs: ['asset-filter','text-field','dynamic-select','static-select','custom-loader','alert','loader','chart','agent-asset-index'],
			
			methods : {

				basePath : jest.fn(),

				redirect : jest.fn()
			},

			mocks:{ lang:(string)=>string },
			
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

	it("updates `title` value when page is in edit and calls `getInitialValues` method",()=>{
		
		updateWrapper()
		
		wrapper.vm.getInitialValues = jest.fn();

		wrapper.vm.getValues('/10/edit');
	
		expect(wrapper.vm.title).toEqual('edit_asset_report')

		expect(wrapper.vm.btnName).toEqual('update')

		expect(wrapper.vm.iconClass).toEqual('fa fa-refresh')

		expect(wrapper.vm.getInitialValues).toHaveBeenCalledWith(1)
	});

	it('updates `loading,apiUrl` value and calls `getChartData,filterData` methods on create page', () => { 
		
		updateWrapper()

		wrapper.vm.getChartData = jest.fn();

		wrapper.vm.filterData = jest.fn();

		wrapper.vm.getValues('/create');

		expect(wrapper.vm.loading).toEqual(false)

		expect(wrapper.vm.reset).toEqual(false)

		expect(wrapper.vm.apiUrl).toEqual('/service-desk/api/asset-list?config=true&')

		expect(wrapper.vm.chartApiUrl).toEqual('/service-desk/api/asset-list?count=true&')

		expect(wrapper.vm.getChartData).toHaveBeenCalled()

		expect(wrapper.vm.filterData).toHaveBeenCalled()
	});

	it('makes an API call', (done) => {

		updateWrapper();
			
		wrapper.vm.filterData = jest.fn();

		wrapper.vm.applyFilter = jest.fn();

		wrapper.vm.getInitialValues(1);

		expect(wrapper.vm.loading).toBe(true)

		expect(wrapper.vm.reset).toBe(true)

		detailsRequest();
			
		setTimeout(()=>{
				
			expect(wrapper.vm.loading).toBe(false)

			expect(wrapper.vm.reset).toBe(false)

			expect(wrapper.vm.filterData).toHaveBeenCalledWith([{"key": "asset_ids", "value_meta": [{"id": 1, "name": "name1"}, {"id": 2, "name": "name2"}]}])

			expect(wrapper.vm.applyFilter).toHaveBeenCalledWith({"asset_ids": [{"id": 1, "name": "name1"}, {"id": 2, "name": "name2"}]})

			expect(wrapper.vm.filter_id).toEqual(1)

			expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/reports/1')
				
			done();
		},50)
	})

	it("makes `loading` as false if api returns error response",(done)=>{

		updateWrapper();

		wrapper.vm.getInitialValues(1);

		detailsRequest(400);

		setTimeout(()=>{

			expect(wrapper.vm.loading).toBe(false)

			expect(wrapper.vm.reset).toBe(false)

			done();
		},50)
	})

	it('isValid - should return false ', done => {
				
		validation.validateAssetReportSettings = () =>{return {errors : [], isValid : false}}
			
		expect(wrapper.vm.isValid()).toBe(false)
			
		done()
	})

	it('isValid - should return true ', done => {
			 
		validation.validateAssetReportSettings = () =>{return {errors : [], isValid : true}}
			
		expect(wrapper.vm.isValid()).toBe(true)
			
		done()
	})

	it('updates `selectedFilters` of the change when onChange method is called',()=>{
	
		wrapper.vm.onChange([{id:1,name:'name'}], 'asset_ids');
	
		expect(wrapper.vm.selectedFilters).toEqual({"asset_ids": [{"id": 1, "name": "name"}]});
	})

	it('calls `getChartData` method when onFieldChange method is called with name `chart_type`',()=>{
		
		wrapper.vm.getChartData = jest.fn();

		wrapper.vm.onFieldChange({id:'pie',name:'Pie'}, 'chart_type');
	
		expect(wrapper.vm.getChartData).toHaveBeenCalled();
	})

	it("calls `storeFields` method when `filterAction` method called with `save` parameter",()=>{

		updateWrapper();

		wrapper.vm.storeFields = jest.fn();

		wrapper.vm.filterAction('save');

		wrapper.vm.isValid = () =>{return true}

		expect(wrapper.vm.storeFields).toHaveBeenCalled();
	})

	it("calls `getChartData` method when 	`applyFilter` method called",()=>{

		wrapper.vm.getChartData = jest.fn();

		wrapper.setData({ componentMetaData : [ { section : [ {  name: 'asset_ids',url: 'url',label: 'assets'}]}],});

		wrapper.setData({ selectedFilters : { 'asset_ids' : [{id:1,name:'name'}]}});

		wrapper.vm.applyFilter(wrapper.vm.selectedFilters);

		expect(wrapper.vm.loading).toBe(false);

		expect(wrapper.vm.apiUrl).toBe('/service-desk/api/asset-list?config=true&asset_ids[0]=1');

		expect(wrapper.vm.chartApiUrl).toBe('/service-desk/api/asset-list?count=true&asset_ids[0]=1');

		expect(wrapper.vm.getChartData).toHaveBeenCalled();
	})

	it("makes an api call when `storeFields` method called",(done)=>{

		wrapper.setData({ componentMetaData : [ { section : [ {  name: 'asset_ids',url: 'url',label: 'assets'}]}],});

		wrapper.setData({ selectedFilters : { 'asset_ids' : [{id:1,name:'name'}]}});

		wrapper.vm.redirect = jest.fn()

		wrapper.vm.storeFields(wrapper.vm.selectedFilters);

		expect(wrapper.vm.loading).toBe(true);

		submitRequest();

		setTimeout(()=>{

			expect(wrapper.vm.loading).toBe(false);

			expect(wrapper.vm.redirect).toHaveBeenCalled()

			expect(moxios.requests.mostRecent().url).toBe('/service-desk/api/reports/create')
			
			done();
		},1)
	})

	it("makes `loading` as false if store api returns error response",(done)=>{

		updateWrapper();

		wrapper.setData({ componentMetaData : [ { section : [ {  name: 'asset_ids',url: 'url',label: 'assets'}]}],});

		wrapper.setData({ selectedFilters : { 'asset_ids' : [{id:1,name:'name'}]}});

		wrapper.vm.storeFields(wrapper.vm.selectedFilters);

		submitRequest(400);

		setTimeout(()=>{

			expect(wrapper.vm.loading).toBe(false)

			done();
		},50)
	})

	it("updates `apiUrl` values and calls `getChartData` method when `onReset` method called",()=>{

		wrapper.setData({ componentMetaData : [{section:[{name:'asset_ids',url:'url',label:'assets',value : [{id:1,name:'nmae'}]}]}],});

		wrapper.vm.getChartData = jest.fn();

		wrapper.vm.onReset();

		expect(wrapper.vm.apiUrl).toBe('/service-desk/api/asset-list?config=true&');

		expect(wrapper.vm.chartApiUrl).toBe('/service-desk/api/asset-list?count=true&');

		expect(wrapper.vm.componentMetaData).toEqual([{"section": [{"className": "col-xs-4", "elements": [], "isMultiple": true, "isPrepopulate": false, "label": "assets", "name": "asset_ids", "url": "url", "value": ""}]}]);

		expect(wrapper.vm.getChartData).toHaveBeenCalled();
	});

	it("makes an Api call and updates `showChart` value as true when `getChartData` method called",(done)=>{

		wrapper.setData( { chartApiUrl : '/service-desk/api/asset-list?count=true&'});

		wrapper.vm.getChartData();

		expect(wrapper.vm.showChart).toBe(false);

		expect(wrapper.vm.chartData).toEqual('');

		chartRequest();

		setTimeout(()=>{

			expect(wrapper.vm.showChart).toBe(true);

			expect(wrapper.vm.chartData).not.toEqual('');

			done()
		},1)
	})

	it("updates `chartData` value as '' when `chart` api returns error response",(done)=>{

		wrapper.setData( { chartApiUrl : '/service-desk/api/asset-list?count=true&'});

		wrapper.vm.getChartData();

		expect(wrapper.vm.showChart).toBe(false);

		chartRequest(400);

		setTimeout(()=>{

			expect(wrapper.vm.showChart).toBe(true);

			expect(wrapper.vm.chartData).toEqual('');

			done()
		},1)
	})

	it("updates `componentMetaData` value when `filterData` method called with data",()=>{

		wrapper.setData({ componentMetaData : [{section:[{name:'asset_ids',url:'url',label:'assets',value : ''}]}],});

		wrapper.vm.filterData([{"key": "asset_ids", "value_meta": [{"id": 1, "name": "name1"}]}])

		expect(wrapper.vm.loading).toBe(false);

		expect(wrapper.vm.componentMetaData).toEqual([{"section": [{"className": "col-xs-4", "elements": [], "isMultiple": true, "isPrepopulate": false, "label": "assets", "name": "asset_ids", "url": "url", "value": [{"id": 1, "name": "name1"}]}]}]);

	})

	function detailsRequest(status = 200,url = '/service-desk/api/reports/1'){

		moxios.uninstall();

		moxios.install();

		moxios.stubRequest(url,{

			status: status,

			response : {
					
				success:true,
					
				data : {
					
					report_filter : { id : 1, name : 'test', description : 'desc', filter_meta : [{key:'asset_ids', value_meta:[{id:1,name:'name1'},{id:2,name:'name2'}]}] }
				}	
			}
		})
	}

	function submitRequest(status = 200,url = '/service-desk/api/reports/create'){

		moxios.uninstall();
		
		moxios.install();
		
		moxios.stubRequest(url,{
			
			status: status,
			
			response: {'success':true,'message':'successfully saved'}
		})
	}

	function chartRequest(status = 200,url = '/service-desk/api/asset-list?count=true&'){

		moxios.uninstall();
		
		moxios.install();
		
		moxios.stubRequest(url,{
			
			status: status,
			
			response: {}
		})
	}
})