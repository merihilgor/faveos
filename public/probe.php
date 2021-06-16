<?php

error_reporting(-1);

ini_set('display_errors', 1);

require __DIR__.'/../bootstrap/autoload.php';
require_once("script/apl_core_configuration.php");
require_once("script/apl_core_functions.php");
use App\Http\Controllers\Admin\helpdesk\PHPController;

//store application's config data such as version, name etc. available in config/app.php
$config = require_once("../config/app.php");
$extensions = [
    'required' => [
        'curl', 'ctype', 'imap', 'mbstring', 'openssl', 'tokenizer', 'pdo_mysql', 'zip', 'pdo',
        'mysqli', 'bcmath', 'iconv', 'XML', 'json', 'fileinfo', 'gd'
    ],
    //define the extra extenstion which are only required for enhancing performance and functionality but not mandatory
    'optional' => ['ldap', 'redis', 'ionCube Loader', 'soap']
    //'ioncube_loader_dar_5.6',
];

$curlError = [];
checkCurl($curlError);

function checkCurl(&$curlError)
{
    if(function_exists('curl_init') === true){
        $ch = curl_init("https://billing.faveohelpdesk.com");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_exec($ch);
        if(curl_error($ch)) {
            array_push($curlError, curl_error($ch));
        }
    } else {
        array_push($curlError, 'cURL is not executable');
    }

}

$env = '../.env';
$envFound = is_file($env);
if ($envFound) {
    $dotenv = Dotenv\Dotenv::create(__DIR__ . '/..');
    $dotenv->load();
}
$passwordMatched = false;
$showError=false;
if(isset($_POST['submit'])) {
    $probePhrase = env('PROBE_PASS_PHRASE', '599fe9896c015afebff1789ea0078f61');
    //Unique password incase support team requires access to probe.php
    $password = "599fe9896c015afebff1789ea0078f61";

    $input = $_POST['passPhrase'];
    if(!in_array($input, [$probePhrase, $password])) {
        $showError=true;
    } else {
        $passwordMatched = true;
    }
}
?>
<html>
<head>
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <?php
    $appName = $config['name'];
    $logo = 'themes/default/common/images/installer/faveo.png';
    $ico = 'themes/default/common/images/favicon.ico';
    if(file_exists('../app/Whitelabel/WhitelabelServiceProvider.php')) {
        $appName = str_replace("Faveo ", "", $appName);
        $logo = 'themes/default/common/images/whitelabel.png';
        $ico = 'themes/default/common/images/whitefavicon.png';
    }
    ?>
    <title><?=$appName?></title>
    <img src="<?=$logo?>" alt="faveo" width="200px" height="130px">
    <!-- links-->
    <link href="<?=$ico?>"  rel="shortcut icon" />
    <link href='themes/default/common/css/bootstrap.min.css' rel="stylesheet" type="text/css"/>
    <link href="themes/default/common/css/load-styles.css" rel="stylesheet" type="text/css" />
    <link href="themes/default/common/css/css.css" rel="stylesheet" type="text/css" />
    <link href="themes/default/common/css/setup.css" rel="stylesheet" type="text/css" />
    <link href="themes/default/common/css/probe-custom.css" rel="stylesheet" type="text/css" />
    <!-- links-->
</head>
<?php if($envFound && !$passwordMatched){ ?>
    <body>
    <div class="setup-content" style="padding: 10 0 10 0">
        <div style="height: auto; width: 500; margin: auto; border: 1px solid #F1F1F1; padding: 10 10 10 10">
            <h1 style="text-align: center; color: #71BEE3">Faveo Probe</h1>
            <?php if($showError){ ?>
                <h4><span style="color: red">The magic phrase you entered is not working.</span></h4>
            <?php } ?>
            <form method="POST" action="probe.php">
                <!-- table Mod Rewrite block-->
                <table class="t01">
                    <label style="float: left;">What's the magic phrase</label>
                    <input  style="float: right; width: 300px; height: 25px; outline: none;" type="password" name="passPhrase" autofocus id="passPhrase">
                    <tfoot>
                    <tr>
                        <td style="border: 1px solid #ffffff;">
                            <!-- Adding app version to make it easy to identify app version during troubleshooting client's system. As many of times if code is encoded
                            we are stuck to identify application version and support team needs to get login
                            details or ask client the app version.
                            -->
                            <p style="font-size: .8em">
                                <b>App Name:</b> <?= $appName; ?><br/>
                                <b>App Version:</b> <?= $config['tags']; ?>
                            </p>
                        </td>
                        <td style="border: 1px solid #ffffff;">
                            <form action="pre-license" method="post"  class="border-line">
                                <p class="setup-actions step">
                                    <button type="submit" name="submit" id="passSubmit" class="button button-large" style="float: right;" disabled>Continue</button>
                                </p>
                            </form>
                        </td>
                    </tr>
                    </tfoot>
                </table>
                <!-- .table -->
            </form>
        </div>
    </div>
    </body>
<?php } else{ ?>
    <body>
    <ol class="setup-steps">
        <li class="active">Server Requirements</li>
        <li class="@yield('license')">License Agreement</li>
        <!-- <li class="@yield('environment')">Environment Test</li> -->
        <li class="@yield('database')">Database Setup</li>
        <li class="@yield('locale')">Getting Started</li>
        <li class="@yield('license-code')">License Code</li>
        <li class="@yield('ready')">Final</li>
    </ol>
    <div class="setup-content">
        <div style="width: 700; margin: auto;">
            <h1 style="text-align: center; color: #71BEE3">Server Requirements</h1>
            <?php
            $errorCount = 0;
            $basePath = substr(__DIR__, 0, -6);
            $storagePermission = is_readable($basePath.DIRECTORY_SEPARATOR.'storage') && is_writeable($basePath.DIRECTORY_SEPARATOR.'storage');
            $bootstrapPermission = is_readable($basePath.DIRECTORY_SEPARATOR.'bootstrap') && is_writeable($basePath.DIRECTORY_SEPARATOR.'bootstrap');
            ?>
            <!-- table Directory Permission block-->
            <table class="t01">
                <tr>
                    <th style="width: 40%;">Directory</th>
                    <th>Permissions</th>
                </tr>
                <tr>
                    <td><?php echo $basePath.'storage'; ?></td>
                    <?php
                    $storagePermissionColor = 'green';
                    $storageMessage = "Read/Write";
                    if (!$storagePermission) {
                        $storagePermissionColor = 'red';
                        $errorCount += 1;
                        $storageMessage = "Directory should be readable and writable by your web server. Give preferred permissions as 755 for directory and 644 for files and owner as your web server user";
                    }
                    ?>
                    <td style='color:<?=$storagePermissionColor;?>'><?=$storageMessage;?></td>
                </tr>
                <tr>
                    <td><?php echo $basePath.'bootstrap'; ?></td>
                    <?php
                    $bootStrapPermissionColor = 'green';
                    $bootStrapMessage = "Read/Write";
                    if (!$bootstrapPermission) {
                        $bootStrapPermissionColor = 'red';
                        $errorCount += 1;
                        $bootStrapMessage = "This directory should be readable and writable by your web server. Give preferred permissions as 755 for directory and 644 for files and owner as your web server user";
                    }
                    ?>
                    <td style='color:<?=$bootStrapPermissionColor;?>'><?=$bootStrapMessage;?></td>
                </tr>
            </table>
            <!-- .table -->


            <!-- table Requirement Check block-->
            <table class="t01">
                <tr>
                    <th style="width: 40%;">Requisites</th>
                    <th>Status</th>
                </tr>
                <tr>
                    <td>Establish connection to Faveo License Manager</td>
                    <?php
                    if(function_exists('curl_init') === true){
                        $apl_connection_notifications=aplCheckConnection();
                        $connectionString = 'Connection Successful';
                        $connectionColor = 'green';
                        if (!empty($apl_connection_notifications)) {
                            $connectionString = "Connection Failed. {$apl_connection_notifications['notification_text']}Connection could not be established with Licensing server.";
                            $connectionColor = 'red';
                            $errorCount += 1;
                        }
                    }  else {
                        $connectionString = 'Connection Failed. cURL extension is not enabled on your server';
                        $errorCount += 1;
                    }
                    ?>
                    <td style='color:<?=$connectionColor;?>'><?=$connectionString;?></td>
                </tr>
                <tr>
                    <td>PHP Version</td>
                    <?php
                    $versionColor = 'green';
                    $versionString = phpversion();
                    if(version_compare(phpversion(), '7.3', '>=') != 1) {
                        $versionColor = 'red';
                        $errorCount += 1;
                        $versionString = phpversion().'Please upgrade PHP Version to 7.3 or greater version';
                    }
                    ?>
                    <td style='color:<?=$versionColor;?>'><?=$versionString;?></td>
                </tr>
                <tr>
                    <td>PHP exec function</td>
                    <?php
                    $execColor = 'green';
                    $execString = 'exec function is enabled';
                    if(!(new PHPController)->execEnabled()) {
                        $execColor = '#F89C0D';
                        $execString = 'exec function is not enabled. This is required for taking system backup. Please note system backup functionality will not work without it.';
                    }
                    ?>
                    <td style='color:<?=$execColor;?>'><?=$execString;?></td>
                </tr>
                <tr>
                    <td>.env file</td>
                    <?php
                    $envColor = 'green';
                    $envString = 'Not found';
                    if($envFound) {
                        $errorCount += 1;
                        $envColor = 'red';
                        $delteFilePath = str_replace("public", ".env", __DIR__);
                        $envString = "Yes Found. <p>Please delete <br/> '$delteFilePath'</p>";
                    }
                    ?>
                    <td style='color:<?=$envColor;?>'><?=$envString;?></td>
                </tr>
                <tr>
                    <td>Maximum execution time</td>
                    <?php
                    $executionColor = 'green';
                    $executionString = ini_get('max_execution_time')." (Maximum execution time is as per requirement)";
                    if ((int) ini_get('max_execution_time') < 120) {
                        $executionColor = '#F89C0D';
                        $executionString = ini_get('max_execution_time')." (Maximum execution time is too low. Recommended execution time is 120 seconds)";
                    }
                    ?>
                    <td style='color:<?=$executionColor;?>'><?=$executionString;?></td>
                </tr>
                <tr>
                    <td>Allow url fopen</td>
                    <?php
                    $color = 'green';
                    $messsage = "Directive is enabled";
                    if (!(int) ini_get('allow_url_fopen')) {
                        $color = '#F89C0D';
                        $messsage = "Directive is disabled (It is recommended to keep this ON as few features in the system are dependent on this)";
                    }
                    ?>
                    <td style='color:<?=$color;?>'><?=$messsage;?></td>
                </tr>
                <tr>
                    <td>cURL exceution</td>
                    <?php
                    $curlColor='green';
                    $curlString='Working fine';
                    if(count($curlError)>0) {
                        $errorCount += 1;
                        $curlColor = "red";
                        $curlString = $curlError[0];
                    }
                    ?>
                    <td style='color:<?=$curlColor;?>'><?=$curlString;?></td>
                </tr>
                <tr>
                    <td>App URL</td>
                    <?php
                    $color='green';
                    $infoString='Valid';
                    if(!filter_var("http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], FILTER_VALIDATE_URL)) {
                        $errorCount += 1;
                        $color = "red";
                        $infoString = "Invalid URL found <p>Make sure your domain/IP doesn't contain any special character other than dash( '-' ) and dot ( '.' )<p>";
                    }
                    ?>

                    <td style='color:<?=$color;?>'><?=$infoString;?></td>
                </tr>
            </table>
            <!-- .table -->


            <!-- table PHP Extension Check block-->
            <table class="t01">
                <tr>
                    <th style="width: 40%;">PHP Extensions</th>
                    <th>Status</th>
                </tr>
                <?php foreach ($extensions as $key => $extension) { foreach ($extension as $value) {?>
                    <tr>
                        <td><?=$value;?></td>
                        <?php
                        $extColor = 'green';
                        $extString = 'Enabled';
                        if(!extension_loaded($value)) {
                            $extString = "Not Enabled<p>To enable this, please install the extension on your server and  update '".php_ini_loaded_file()."' to enable ".$value."</p>"
                                .'<a href="https://support.faveohelpdesk.com/show/how-to-enable-required-php-extension-on-different-servers-for-faveo-installation" target="_blank">How to install PHP extensions on my server?</a>';
                            $extColor = "#F89C0D";
                            if($key == 'required') {
                                $errorCount += 1;
                                $extColor = 'red';
                            }
                        }
                        ?>
                        <td style='color:<?=$extColor;?>'><?=$extString;?></td>
                    </tr>
                <?php } }?>
            </table>
            <!-- .table -->


            <?php

                /**
                 * Gets license page URL
                 * @return string
                 */
                function getLicenseUrl()
                {
                    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')   
                         $url = "https://";   
                    else  
                         $url = "http://";   
                    // Append the host(domain name, ip) to the URL.   
                    $url.= $_SERVER['HTTP_HOST'];   
                    
                    // Append the requested resource location to the URL   
                    $url.= $_SERVER['REQUEST_URI'];    
                    
                    return str_replace('probe.php', 'pre-license', $url);
                }

                /**
                 * Checks if user friendly url is on.
                 * @internal it curls for pre-license page, if it gets a 404, it returns false. 
                 * If any exception happens or curl is not found, it returns null
                 * @return bool|null
                 */
                function checkUserFriendlyUrl()
                {
                    if(function_exists('curl_init') === true){
                        try {
                            $ch = curl_init(getLicenseUrl());
                            curl_setopt($ch, CURLOPT_HEADER, true);
                            curl_setopt($ch, CURLOPT_NOBODY, true);  
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
                            curl_setopt($ch, CURLOPT_TIMEOUT,10);
                            curl_exec($ch);
                            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                            curl_close($ch);
                            return $httpcode != 404; 
                        } catch(Exception $e){
                            return null;
                        }
                    }
                    return null;
                }
            ?>

            <!-- table Mod Rewrite block-->
            <table class="t01">
                <tr>
                    <th style="width: 40%;">Mod Rewrite</th>
                    <th>Status</th>
                </tr>
                <tr>
                    <td>Rewrite Engine</td>
                    <?php
                    $redirect = function_exists('apache_get_modules')? (int)in_array('mod_rewrite', apache_get_modules()) : 2;
                    $rewriteStatusColor = 'green';
                    $rewriteStatusString = "ON";
                    if($redirect == 2) {
                        $rewriteStatusColor = "#F89C0D";
                        $rewriteStatusString = "Unable to detect";
                    } elseif(!$redirect) {
                        $errorCount += 1;
                        $rewriteStatusColor = 'red';
                        $rewriteStatusString = "OFF";
                    }
                    ?>
                    <td style='color:<?=$rewriteStatusColor;?>'><?=$rewriteStatusString;?></td>
                </tr>
                <tr>
                    <td>User friendly URL</td>
                    <?php
                        $userFriendlyUrl = checkUserFriendlyUrl();
                        if($userFriendlyUrl === true) {
                            $userFriendlyUrlStatusColor = 'green';
                            $userFriendlyUrlStatusString = "ON";
                        } elseif($userFriendlyUrl === false) {
                            $errorCount += 1;
                            $userFriendlyUrlStatusColor = 'red';
                            $userFriendlyUrlStatusString = "OFF (If you are using apache, make sure <var><strong>AllowOverride</strong></var> is set to <var><strong>All</strong></var> in apache configuration)";
                        }else {
                            $userFriendlyUrlStatusColor = "#F89C0D";
                            $userFriendlyUrlStatusString = "Unable to detect";
                        }
                        ?>
                    <td style='color:<?=$userFriendlyUrlStatusColor;?>'><?=$userFriendlyUrlStatusString;?></td>
                </tr>

                <?php

                $display = ($errorCount == 0) ? ' <input type="submit" name="submit" id="submitme" class="button-primary button button-large button-next" value="Continue">' : '<button disabled="" class="button button-large" style="float: right;">Continue</button>';
                ?>
                <tfoot>
                <tr>
                    <td style="border: 1px solid #ffffff;">
                        <!-- Adding app version to make it easy to identify app version during troubleshooting client's system. As many of times if code is encoded
                        we are stuck to identify application version and support team needs to get login
                        details or ask client the app version.
                        -->
                        <p style="font-size: .8em">
                            <b>App Name:</b> <?= $appName; ?><br/>
                            <b>App Version:</b> <?= $config['tags']; ?>
                        </p>
                    </td>
                    <td style="border: 1px solid #ffffff;">
                        <form action="pre-license" method="post"  class="border-line">
                            <input type="hidden" name="count" value="<?php echo $errorCount ;?>" />
                            <p class="setup-actions step"><?= $display ?></p>
                        </form>
                    </td>
                </tr>
                </tfoot>
            </table>
            <!-- .table -->
        </div>
    </div>
    </body>
<?php } ?>
<?php
$footerString = "Copyright &copy; 2015 - ".date('Y').". Ladybird Web Solution Pvt Ltd. All rights reserved. Powered by <a target='_blank' href='https://www.faveohelpdesk.com/'>Faveo </a>";

if(file_exists('../app/Whitelabel/WhitelabelServiceProvider.php')) {
    $footerString = "Copyright &copy; 2015 - ".date('Y').". All rights reserved ";
}
?>
<span class="hide" style="text-align: center;"><?=$footerString;?></span>
<footer>
    <script src='themes/default/common/js/jquery-2.1.4.min.js' type="text/javascript"></script>
    <script type="text/javascript">
        $("#passPhrase").keyup(function(e) {
            $("#passSubmit").removeClass('button-primary');
            $("#passSubmit").attr('disabled', true);
            if($(this).val() != '') {
                $("#passSubmit").addClass('button-primary');
                $("#passSubmit").attr('disabled', false);
            }
        });
    </script>
</footer>
</html>