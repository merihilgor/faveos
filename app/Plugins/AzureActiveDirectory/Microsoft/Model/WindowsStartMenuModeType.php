<?php
/**
* Copyright (c) Microsoft Corporation.  All Rights Reserved.  Licensed under the MIT License.  See License in the project root for license information.
* 
* WindowsStartMenuModeType File
* PHP version 7
*
* @category  Library
* @package   Microsoft.Graph
* @copyright © Microsoft Corporation. All rights reserved.
* @license   https://opensource.org/licenses/MIT MIT License
* @link      https://graph.microsoft.com
*/
namespace App\Plugins\AzureActiveDirectory\Microsoft\Model;

use App\Plugins\AzureActiveDirectory\Microsoft\Core\Enum;

/**
* WindowsStartMenuModeType class
*
* @category  Model
* @package   Microsoft.Graph
* @copyright © Microsoft Corporation. All rights reserved.
* @license   https://opensource.org/licenses/MIT MIT License
* @link      https://graph.microsoft.com
*/
class WindowsStartMenuModeType extends Enum
{
    /**
    * The Enum WindowsStartMenuModeType
    */
    const USER_DEFINED = "userDefined";
    const FULL_SCREEN = "fullScreen";
    const NON_FULL_SCREEN = "nonFullScreen";
}