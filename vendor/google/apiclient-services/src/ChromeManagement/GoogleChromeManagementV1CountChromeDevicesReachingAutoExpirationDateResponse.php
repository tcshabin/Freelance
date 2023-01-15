<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementV1CountChromeDevicesReachingAutoExpirationDateResponse extends \Google\Collection
{
  protected $collection_key = 'deviceAueCountReports';
  protected $deviceAueCountReportsType = GoogleChromeManagementV1DeviceAueCountReport::class;
  protected $deviceAueCountReportsDataType = 'array';
  public $deviceAueCountReports;

  /**
   * @param GoogleChromeManagementV1DeviceAueCountReport[]
   */
  public function setDeviceAueCountReports($deviceAueCountReports)
  {
    $this->deviceAueCountReports = $deviceAueCountReports;
  }
  /**
   * @return GoogleChromeManagementV1DeviceAueCountReport[]
   */
  public function getDeviceAueCountReports()
  {
    return $this->deviceAueCountReports;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1CountChromeDevicesReachingAutoExpirationDateResponse::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1CountChromeDevicesReachingAutoExpirationDateResponse');
