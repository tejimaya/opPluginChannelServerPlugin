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
    error_reporting(error_reporting() & ~(E_STRICT | E_DEPRECATED));

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

  public function executeAddRelease(sfWebRequest $request)
  {
    $this->form = new opPluginPackageReleaseForm();
    $this->form->setPluginPackage($this->package);
    if ($request->isMethod(sfWebRequest::POST))
    {
      $this->form->bind($request['plugin_release'], $request->getFiles('plugin_release'));
      if ($this->form->isValid())
      {
        $this->form->uploadPackage();

        $this->getUser()->setFlash('notice', 'Released plugin package');
        $this->redirect('package_home', $this->package);
      }
    }
  }

  public function executeJoin(sfWebRequest $request)
  {
    $this->form = new opPluginPackageJoinForm();
    $this->form->setPluginPackage($this->package);

    if (opPlugin::getInstance('opMessagePlugin')->getIsActive())
    {
      $this->form->injectMessageField();
    }

    if ($request->isMethod(sfWebRequest::POST))
    {
      $this->form->bind($request['plugin_join']);
      if ($this->form->isValid())
      {
        $this->form->send();

        $this->getUser()->setFlash('notice', 'Sent join request');
        $this->redirect('package_home', $this->package);
      }
    }
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
