<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * opPluginChannelServerPlugin actions.
 *
 * @package    OpenPNE
 * @subpackage opPluginChannelServerPlugin
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 9301 2008-05-27 01:08:46Z dwhittle $
 */
class opPluginChannelServerPluginActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfWebRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->form = new opPluginChannelServerPluginConfigForm(array(
      'channel_name' => Doctrine::getTable('SnsConfig')->get(opPluginChannelServerPluginConfiguration::CONFIG_KEY_PREFIX.'channel_name'),
      'summary' => Doctrine::getTable('SnsConfig')->get(opPluginChannelServerPluginConfiguration::CONFIG_KEY_PREFIX.'summary'),
      'suggestedalias' => Doctrine::getTable('SnsConfig')->get(opPluginChannelServerPluginConfiguration::CONFIG_KEY_PREFIX.'suggestedalias'),
    ));
    if ($request->isMethod(sfWebRequest::POST))
    {
      $this->form->bind($request['plugin_config']);
      if ($this->form->isValid())
      {
        $this->form->save();
        $this->redirect('opPluginChannelServerPlugin/index');
      }
    }
  }

  public function executeCategory(sfWebRequest $request)
  {
    $this->categories = Doctrine::getTable('PluginCategory')->findAll();
    $this->rootForm = new PluginCategoryForm();
    $this->deleteForm = new sfForm();
    $this->categoryForms = array();

    $params = $request->getParameter('plugin_category');
    if ($request->isMethod(sfRequest::POST))
    {
      $targetForm = $this->rootForm;
      if ($targetForm->bindAndSave($params))
      {
        $this->getUser()->setFlash('notice', 'Saved.');
        $this->redirect('opPluginChannelServerPlugin/category');
      }
    }
  }

  /**
   * Executes categoryList action
   *
   * @param sfWebRequest $request A request object
   */
  public function executeCategoryEdit(sfWebRequest $request)
  {
    $form = new PluginCategoryForm(Doctrine::getTable('PluginCategory')->find($request['id']));
    if ($request->isMethod(sfRequest::POST))
    {
      if ($form->bindAndSave($request['plugin_category']))
      {
        $this->getUser()->setFlash('notice', 'Saved.');
      }
      else
      {
        $this->getUser()->setFlash('error', $form->getErrorSchema()->getMessage());
      }
    }
    $this->redirect('opPluginChannelServerPlugin/category');
  }

  /**
   * Executes categoryDelete action
   *
   * @param sfWebRequest $request A request object
   */
  public function executeCategoryDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $category = Doctrine::getTable('PluginCategory')->find($request['id']);
    $this->forward404Unless($category);

    $category->delete();

    $this->getUser()->setFlash('notice', 'Deleted.');
    $this->redirect('opPluginChannelServerPlugin/category');
  }
}
