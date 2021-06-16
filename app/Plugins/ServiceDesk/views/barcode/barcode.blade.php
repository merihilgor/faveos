<html>
    <head>
    <title>Print-Labels </title>
    
    <style>


    @media print {
        .label {
            border: none !important;
        }
    }
    
    .qr-image {
        margin: auto;
        width: 25%;
        height: 50%;
        /* margin-right: 5%; */
    }

    .asset-data {
        width: 45%;
        height: 100%;
        max-width: 45%;
        display: flex;
        flex-direction: column;
        /* flex:1; */
    }

    .asset_text {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        width: 100%;
        max-width: 100%;
        margin-top: 10%;
    }


    body {
        font-family: arial, helvetica, sans-serif;
        width: 8.5in;
        height: 11in;
        margin: 0.5in 0.21975in 0.5in 0.21975in;
        font-size: 8pt;
    }

    .qr-image > img.qr {
        
        width: 100%;
        height: 100%;
        display: block; /* remove extra space below image */
        
    }

    .label {
        width: {{ $template['width']  }}in;
        height: {{ $template['height'] }}in;
        padding: 0;
        display: flex;
        overflow: hidden;
        border: 1px dotted black;
    }    

    .container {
        display: grid;
        grid-template-columns: repeat({{$template['labels_per_row']}}, {{ $template['width']  }}in);
        grid-column-gap: {{ $template['space_between_labels'] / 25.4 }}in;
        grid-row-gap: {{ $template['space_between_labels'] / 25.4 }}in;
        
    }

    .asset-data > img.logo {
        width: 100%;
        height: 35%;
        object-fit: contain;
        display: block;
        max-width: 100%;
        max-height: 35%;
    }

    

    </style>
    </head>
    <body>
        <div class="container">
                @foreach ($payload as $data)
                    <div class="label">
                        <div class="qr-image">
                            <img class="qr" src="data:image/png;base64, {{$data['barcode']}}" alt="barcode">
                        </div> 

                        <div class="asset-data">
                            <?php 
                                if($template['display_logo_confirmed'] == 1 && $template['logo_image'])    
                                    echo '<img class="logo" src="data:image/png;base64,'. $template['logo_image'].'" alt="logo">';
                            ?>
                            <div class="asset_text">
                                {{ $data['identifier'] }} <br> {{ $data['asset_name'] }}
                            </div>
                            
                        </div>
                        
                    </div>
                    

                @endforeach
        </div>
    </body>
</html>