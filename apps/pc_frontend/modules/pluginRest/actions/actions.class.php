<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * pluginRest actions.
 *
 * @package    OpenPNE
 * @subpackage pluginRest
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 9301 2008-05-27 01:08:46Z dwhittle $
 */
class pluginRestActions extends sfActions
{
  public function preExecute()
  {
    foreach (array('channel_name', 'summary', 'suggestedalias') as $v)
    {
      $this->$v = Doctrine::getTable('SnsConfig')->get(opPluginChannelServerPluginConfiguration::CONFIG_KEY_PREFIX.$v, str_replace(':80', '', $this->getRequest()->getHost()));
    }
  }

  public function executeChannel(sfWebRequest $request)
  {
  }

  public function executeRoot(sfWebRequest $request)
  {
    $this->forward404();
  }

  public function executeCategoryAll(sfWebRequest $request)
  {
    $this->categories = Doctrine::getTable('PluginCategory')->findAll();
  }

  public function executeCategoryInfo(sfWebRequest $request)
  {
    $this->category = $this->getRoute()->getObject();
  }

  public function executeMaintainerAll(sfWebRequest $request)
  {
    $this->handles = Doctrine::getTable('MemberConfig')->findByName('pear_handle', Doctrine_Core::HYDRATE_ON_DEMAND);
  }

  public function executeMaintainerInfo(sfWebRequest $request)
  {
    $this->config = Doctrine::getTable('MemberConfig')->retrieveByNameAndValue('pear_handle', $request['name']);
    $this->forward404Unless($this->config);
  }
}
