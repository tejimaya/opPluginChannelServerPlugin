<?php

/**
 * Copyright 2010 Kousuke Ebihara
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @copyright 2010 Kousuke Ebihara
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 */

/**
 * The opPluginChannelServerPlugin class.
 *
 * @package    opPluginChannelServerPlugin
 * @subpackage config
 * @author     Kousuke Ebihara <ebihara@tejimaya.com>
 */
class opPluginChannelServerPluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
    sfToolkit::addIncludePath(array(sfConfig::get('sf_plugins_dir').'/opPluginChannelServerPlugin/lib/vendor/PEAR/'));
  }
}
