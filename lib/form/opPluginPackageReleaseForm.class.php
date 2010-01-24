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
 * Plugin release from
 *
 * @package    opPluginChannelServerPlugin
 * @subpackage form
 * @author     Kousuke Ebihara <ebihara@tejimaya.com>
 */
class opPluginPackageReleaseForm extends BaseForm
{
  protected $package;

  public function configure()
  {
    $this
      ->setWidget('tgz_file', new sfWidgetFormInputFile())
      ->setValidator('tgz_file', new sfValidatorFile(array('required' => false)))

      ->setWidget('svn_url', new sfWidgetFormInputText())
      ->setValidator('svn_url', new sfValidatorUrl(array('required' => false)))

      ->setWidget('git_url', new sfWidgetFormInputText())
      ->setValidator('git_url', new sfValidatorString(array('required' => false)))

      ->setWidget('git_commit', new sfWidgetFormInputText())
      ->setValidator('git_commit', new sfValidatorString(array('required' => false)))
    ;

    $this->widgetSchema->setNameFormat('plugin_release[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
  }

  public function setPluginPackage($package)
  {
    $this->package = $package;
  }

  public function uploadPackage()
  {
    error_reporting(error_reporting() & ~(E_STRICT | E_DEPRECATED));

    $tgz = $this->getValue('tgz_file');
    $svn = $this->getValue('svn_url');
    $gitUrl = $this->getValue('git_url');
    $gitCommit = $this->getValue('git_commit');

    if ($tgz)
    {
      require_once 'PEAR.php';
      require_once 'PEAR/Common.php';
      require_once 'PEAR/ChannelFile.php';

      $channel = new PEAR_ChannelFile();
      $channel->fromXmlFile(sfConfig::get('sf_web_dir').'/channel.xml');
      $channel->setName('plugins.openpne.jp');
      $registry = new PEAR_Registry(sfConfig::get('sf_cache_dir'), $channel);

      $pear = new PEAR_Common();

      $pearConfig = $pear->config;
      $pearConfig->setRegistry($registry);

      if (!$registry->channelExists($channel->getName()))
      {
        $registry->addChannel($channel);
      }
      else
      {
        $registry->updateChannel($channel);
      }

      $info = $pear->infoFromTgzFile($tgz->getTempName());
      if ($info instanceof PEAR_Error)
      {
        throw new RuntimeException($info->message());
      }

      $file = new File();
      $file->setFromValidatedFile($tgz);
      $file->save();
      $release = Doctrine::getTable('PluginRelease')->create(array(
        'version'   => $info['version'],
        'stability' => $info['stability']['release'],
        'file_id'   => $file->id,
      ));
      $this->package->PluginRelease[] = $release;
      $this->package->save();
    }
    elseif ($svn)
    {
    }
    elseif ($gitUrl && $gitCommit)
    {
    }
  }
}
