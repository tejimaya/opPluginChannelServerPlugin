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
*/

/**
 * The class represents a form for editing deps with OpenPNE.
 *
 * @package    opPluginChannelServerPlugin
 * @subpackage form
 * @author     Kousuke Ebihara <ebihara@php.net>
 */
class opPluginReleaseEditOpenPNEDepsForm extends BaseForm
{
  public function configure()
  {
    $this->setWidgets(array(
      'ge' => new sfWidgetFormInputText(array(), array('size' => 10)),
      'le' => new sfWidgetFormInputText(array(), array('size' => 10)),
    ));

    $this->setValidators(array(
      'ge' => new opValidatorString(array('required' => false)),
      'le' => new opValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('release_dep[%s]');
  }

  public function save()
  {
    if (!($this->getOption('release') instanceof PluginRelease))
    {
      throw new LogicException();
    }

    $this->getOption('release')
      ->setOpenPNEDeps($this->getValue('ge'), $this->getValue('le'))
      ->save();
  }
}
