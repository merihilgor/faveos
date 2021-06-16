<style type="text/css">
 .test-class{
    margin-bottom: 5%;
   }   
</style>


<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"> {{Lang::get('ServiceDesk::lang.service_desk')}}</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">

                <div class="col-md-2 col-sm-6 test-class" style="margin-left: 0%;">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('service-desk/products') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-industry"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" > {{Lang::get('ServiceDesk::lang.products')}}</p>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('service-desk/procurement') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-phone"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{{Lang::get('ServiceDesk::lang.procurement_types')}}</p>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6 test-class">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('service-desk/contract-types') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-paper-plane"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{{Lang::get('ServiceDesk::lang.contract_types')}}</p>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6 test-class">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('service-desk/license-types') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-paste"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{{Lang::get('ServiceDesk::lang.license_type')}}</p>
                    </div>
                </div>

                
                <div class="col-md-2 col-sm-6 test-class">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('service-desk/vendor') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-barcode"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{{Lang::get('ServiceDesk::lang.vendors')}}</p>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6 test-class">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('service-desk/assetstypes') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-briefcase"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{{Lang::get('ServiceDesk::lang.asset_types')}}</p>
                    </div>
                </div>
                 <div class="col-md-2 col-sm-6 test-class">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('service-desk/asset-statuses') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-briefcase"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{{Lang::get('ServiceDesk::lang.asset_statuses')}}</p>
                    </div>
                </div>
                
                <div class="col-md-2 col-sm-6 test-class">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('service-desk/cabs') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-users"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{{Lang::get('ServiceDesk::lang.change_advisory_board')}}</p>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6 hide test-class">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('service-desk/location-types') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-map"></i>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6 test-class">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('service-desk/announcement') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-bullhorn"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{{Lang::get('ServiceDesk::lang.announcement-page-title')}}</p>
                    </div>
                </div>
                <!--/.col-md-2-->

                <div class="col-md-2 col-sm-6 test-class">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="{{ url('service-desk/barcode/settings') }}" onclick="sidebaropen(this)">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-qrcode"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >{{Lang::get('ServiceDesk::lang.barcode')}}</p>
                    </div>
                </div>
                <!--/.col-md-2-->
            </div>
        </div>
        <!-- /.row -->
    </div>
    <!-- ./box-body -->
</div>