@extends('themes.default1.admin.layout.admin')
@section('content')

<style>
   .app-version-wrapper {
      display: -webkit-box;   
      display: -moz-box; 
      display: -ms-flexbox;  
      display: -webkit-flex;
      display: flex;  
      
      -webkit-justify-content: center;
      -moz-justify-content: center;
      justify-content: center;
      -webkit-align-items: center;
      -moz-align-items: center;
      align-items: center;
      -webkit-flex-wrap: wrap;
      -moz-flex-wrap: wrap;
      flex-wrap: wrap;
   }

   .app-version-wrapper > div {
      color: #FFFFFF;
      width: 260px;
      height: 175px;
      margin: 15px 0px 15px 0px; 
      box-shadow: 0 2px 1px -1px rgba(0,0,0,.2), 0 1px 1px 0 rgba(0,0,0,.14), 0 1px 3px 0 rgba(0,0,0,.12);
   }

   .app-current-version-box {
      background-color: #868686;
      border-radius: 7px 0px 0px 7px;
      text-align: end;
   }
   .app-latest-version-box {
      background-color: #4e93bc;
      border-radius: 0px 7px 7px 0px;
   }
   .app-current-version-box .au-box-header {
      background-color: #6c6c6c;
      border-radius: 7px 0px 0px 0px;
   }
   .app-latest-version-box .au-box-header {
      background-color: #357ea8;
      border-radius: 0px 7px 0px 0px;
   }
   .au-box-header {
      border-bottom: 1px solid #dedede;
      padding: 10px;
   }
   .au-box-body {
      padding: 10px;
   }

</style>

  @if (isset($error))
    <div class="alert alert-danger alert-dismissable">
          <li>{{ $error }}</li>
    </div>
  @endif
       <?php
        $model = new App\Backup_path();
        $path  = $model->pluck('backup_path')->first();

        $cont = new \App\Http\Controllers\Admin\helpdesk\BackupController();
        $sumOfDbAndFilesystem = intval($cont->getDatabaseAndFileSystemSize());//Getting only integer part of the sum
        ?>
  @if(isset($update_avaiable))
    @if(\Event::dispatch('helpdesk.apply.whitelabel'))
      <div class="alert alert-danger">
        Note: Auto Updates cannot be proceed if white label plugin is enabled
      </div>
    @else


    <div style="text-align: center">
       <div>
         <h3 style="color: olive">
            <i class="fa fa-info-circle"></i>
               An update is available.
         </h3>
       </div>
      <div>
         <button class="btn bg-olive btn-flat margin" id="app-update">UPDATE NOW</button>
      </div>
   </div>
    <div class="app-version-wrapper">
      <div class="app-current-version-box">
         <div class="au-box-header">
            <h4> Currently installed version</h4>
         </div>
         <div class="au-box-body">
            <h1>{{Config::get('app.tags')}}</h1>
         </div>
      </div>
      <div class="app-latest-version-box">
         <div class="au-box-header">
            <h4>Latest version</h4>
         </div>
         <div class="au-box-body">
            <h1>{{Storage::get('self-updater-new-version')}}</h1>
         </div>
      </div>
   </div>

    @endif
   @else
      @if(isset($no_update))
         <div class="alert alert-info" role="alert">
            <i class="fa fa-check-circle"></i>
            You are running the <b>latest (<span style="font-size: 18px">{{Config::get('app.tags')}}</span>)</b> version. 
            <a href="{{url('dashboard')}}" class="alert-link pull-right">Go to dashboard</a>
         </div>
      @else
         <div class="alert alert-info" role="alert">
            <i class="fa fa-info-circle"></i>
            You do not have access to <b>upgrade</b> the application.
            @if(\Auth::user()->role == 'admin')
               <br><br>
               Please contact faveo team.<b> Or </b>
               <a href="{{url('update-order-details')}}" class="alert-link">Update your order details</a>
            @endif
         </div>
      @endif
   @endif
   <!-- Update modal pop-up -->

   <div class="modal fade" id="update-modal">
      <div class="modal-dialog">
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
               <h4 class="modal-title">Auto Update</h4>
            </div>
            <div class="modal-body">
               <h4>You are about to perform an update!</h4>
               {{-- <h1 style="text-align: center;">{{Storage::get('self-updater-new-version')}}</h1> --}}
               <h5 style="color: #fa2525">Note:- {{trans('lang.backup_about_to_start',["filesystem_space"=> getSize($sumOfDbAndFilesystem * 1024 *1024)])}}</h5>
               <br>

                <input type="hidden" value="{{$path}}" id="storage_path">
                <input type="checkbox" id="confirm-backup" name="backup" checked> <span data-toggle="tooltip" data-placement="top" title="Backup path: {{$path}}">  &nbsp;Take System Backup before update(<b>recommended</b>).</span>

            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-default pull-left" data-dismiss="modal" id="dismis2">Close</button>

               <button class="btn btn-success" id="confirm-update">Continue</button>
            </div>
         </div>
      </div>
   </div>
   <!-- maintenance mode pop-up -->
   <div class="modal fade" id="maintenence-mode" data-backdrop="static" data-keyboard="false">
      <div class="modal-dialog">
         <div class="modal-content">
            <div class="modal-header">
               <h4 class="modal-title text-center">Faveo Upgrading</h4>
            </div>
            <div class="modal-body">
               <div class="text-center">
                  <img src="{{assetLink('image','logo')}}" width="100px" class="center-block" style="background-color: crimson"><br><br>
                  <span id="body-contents">Maintenance Mode Enabled... Faveo Upgrading.. Do not close the window.</span><br><br>
                  <img class="center-block loader" src="{{assetLink('image','gifloader')}}"><br>
               </div>
            </div>
            <div class="modal-footer">
               Copyright © <?php echo date('Y') ?> . All rights reserved. Powered by Faveo.
            </div>
         </div>
      </div>
   </div>
   <!-- Application backup modal pop-up -->
   <div class="modal fade" id="backup-modal">
      <div class="modal-dialog">
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
               <h3 class="modal-title"> Back up and Update</h3>
            </div>
            <div class="modal-body">
               <div class="alert alert-danger" id="backup-and-update"></div>
               <div class="back-exists"></div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-default pull-left" data-dismiss="modal" id="dismis2">Close</button>
            </div>
         </div>
      </div>
   </div>


   
@stop
@section('FooterInclude')
  @if(App::isDownForMaintenance())
    <script src="{{asset("lb-faveo/js/jquery-2.1.4.min.js")}}" type="text/javascript"></script>
  @endif
   <script>
    $('body').on('click','#app-update', function(){
         $('#update-modal').modal('show')
      });



    $('body').on('click','#confirm-update', function(){
         $('#update-modal').modal('toggle')
         $('.updater').addClass('hidden')
         
          if($('#confirm-backup').prop('checked') == true) {
          $.ajax({
           type: "post", 
           url: "{{url('api/backup/take-system-backup')}}/",
           data: {path: $('#storage_path').val(),autoUpdate: true, '_token':"{{csrf_token()}}"},
           success: function(data){
             if(data.success){
              var pop_up = $('#backup-modal')
               pop_up.modal('show')
               pop_up.children().find('img').attr('src', "{{assetLink('image','logo')}}")
              $('#backup-and-update').append(data.message)
              setTimeout(function(){
                  location.reload(true);
                }, 2000); 
             }
             else{
              var pop_up = $('#backup-modal')
               pop_up.modal('show')
               pop_up.children().find('img').attr('src', "{{assetLink('image','logo')}}")
              $('#backup-and-update').append(data.message)
             }
           },
            error: function (data) {
              var pop_up = $('#backup-modal')
               pop_up.modal('show')
               pop_up.children().find('img').attr('src', "{{assetLink('image','logo')}}")
              $('#backup-and-update').append(data.responseJSON.message)
          }
          });
         } else {
          downloadUpdate()
         }
  

      });


      function downloadUpdate(){
        $('.updater').removeClass('hidden')
        $('.updater').empty()
        $('#maintenence-mode').modal('show')
        var pop_up = $('#maintenence-mode')
        pop_up.modal('show')
        pop_up.children().find('img').attr('src', "{{assetLink('image','logo')}}")
        pop_up.find('.loader').attr("src", "{{assetLink('image','gifloader')}}")
        pop_up.find('.modal-title').text("Faveo Updating")
        pop_up.find('#body-contents').text("Maintenance Mode Enabled... Faveo Updating.. Do not close the window.")

        $.ajax({
          type:"post",
          url:"{{url('/auto-update-application')}}",
          data: {update_version: "{{trim(Storage::get('self-updater-new-version'))}}"},
          success:function(data){
            if(data.success == true){
              updateDatabase()
              
            }
             else{
              var pop_up = $('#maintenence-mode')
              pop_up.modal('show')
              pop_up.children().find('.modal-title').text("FAVEO UPDATING")
              pop_up.children().find('#body-contents').empty()
              pop_up.children().find('#body-contents').append(data.message+"<br><br> <button class='btn btn-sm btn-primary' id='retry-backup'>Retry </button>")
              pop_up.children().find('img').attr('src', "{{assetLink('image','logo')}}")
              pop_up.children().find('.loader').attr('src', '')
              $('.updater').addClass('hidden')
            }
          }
        });
      }

      function updateDatabase(){
          $('.updater').addClass('hidden')
      $('#maintenence-mode').modal('show')
      var pop_up = $('#maintenence-mode')
      pop_up.modal('show')
      pop_up.children().find('img').attr('src', "{{assetLink('image','logo')}}")
      pop_up.find('.loader').attr("src", "{{assetLink('image','gifloader')}}")
      pop_up.find('.modal-title').text("Faveo Updating")
     pop_up.children().find('#body-contents').text("Faveo Updating Database.. Do not close the window.")

        $('.updater').addClass('hidden')
        $.ajax({
          type: "post",
          url: "{{url('/auto-update-database')}}",
          data: {'_token':"{{csrf_token()}}"},
          success: function(data){
            if(data.success == true){
             $('#maintenence-mode').modal('toggle')
              $('.update-info').empty()
               $('.update-info').append('<div class="alert alert-info text-center">'+data.message+ '..Your Faveo Installation has been updated successfully <div alert alert-warning>  </div></div>')
                setTimeout(function(){
                  location.reload(true);
                }, 2000); 
            } else {
               var pop_up = $('#maintenence-mode')
              pop_up.modal('show')
              pop_up.children().find('.modal-title').text("FAVEO UPDATING")
              pop_up.children().find('#body-contents').empty()
              pop_up.children().find('#body-contents').append('Files have been updated but database update failed. Please contact Faveo team or run the command <i>php artisan database:sync</i> from CLI'+"<br><br> <button class='btn btn-sm btn-primary' id='retry-backup'>Retry </button>")
              pop_up.children().find('img').attr('src', "{{assetLink('image','logo')}}")
              pop_up.children().find('.loader').attr('src', '')
              $('.updater').addClass('hidden')
              
          } 
            }
        })
      }

       $('body').on('click','#retry-backup', function(){
        $('#maintenence-mode').modal('toggle')
         $('#update-modal').modal('show')
      });

   </script>
@stop