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
 * package actions.
 *
 * @package    opPluginChannelServerPlugin
 * @subpackage package
 * @author     Kousuke Ebihara <ebihara@tejimaya.com>
 */
class packageActions extends sfActions
{
  public function preExecute()
  {
    if ($this->getRoute() instanceof sfDoctrineRoute)
    {
      $this->package = $this->getRoute()->getObject();
    }
  }

  public function executeHome(sfWebRequest $request)
  {
    if ($this->getUser()->getMemberId())
    {
      $this->security['home'] = array('is_secure' => true);
    }
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new PluginPackageForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->form = new PluginPackageForm();
    $this->redirectIf($this->form->bindAndSave($request['plugin_package'], $request->getFiles('plugin_package')),
      'package_home', $this->form->getObject());

    $this->setTemplate('new');
  }

  public function executeToggleUsing(sfWebRequest $request)
  {
    $this->forward404Unless($this->getRequest()->isXmlHttpRequest());
    $this->getResponse()->setContentType('application/json');

    try {
      $request->checkCSRFProtection();
    } catch (sfValidatorErrorSchema $e) {
      $this->forward404();
    }

    $memberId = $this->getUser()->getMemberId();
    $isUse = $this->package->isUser($memberId);
    $this->package->toggleUsing($memberId);

    return $this->renderText(json_encode(array($this->package->countUsers(), !$isUse)));
  }
}
