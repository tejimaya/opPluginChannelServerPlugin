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

  protected function getChannel()
  {
    $host = sfContext::getInstance()->getRequest()->getHost();
    $serverName = opPluginChannelServerToolkit::getConfig('server_name', str_replace(':80', '', $host));

    $browser = new opBrowser($serverName);
    $browser->get('/channel.xml');

    $channel = new PEAR_ChannelFile();
    $channel->fromXmlString($browser->getResponse()->getContent());
    $channel->setName($serverName);

    return $channel;
  }

  public function uploadPackage()
  {
    error_reporting(error_reporting() & ~(E_STRICT | E_DEPRECATED));

    $tgz = $this->getValue('tgz_file');
    $svn = $this->getValue('svn_url');
    $gitUrl = $this->getValue('git_url');
    $gitCommit = $this->getValue('git_commit');

    $pear = opPluginChannelServerToolkit::registerPearChannel($this->getChannel());

    if ($tgz)
    {
      require_once 'Archive/Tar.php';

      $info = $pear->infoFromTgzFile($tgz->getTempName());
      if ($info instanceof PEAR_Error)
      {
        throw new RuntimeException($info->getMessage());
      }

      $tar = new Archive_Tar($tgz->getTempName());
      $xml = '';
      foreach ($tar->listContent() as $file)
      {
        if ('package.xml' === $file['filename'])
        {
          $xml = $tar->extractInString($file['filename']);
        }
      }

      $file = new File();
      $file->setFromValidatedFile($tgz);
      $file->save();
      $release = Doctrine::getTable('PluginRelease')->create(array(
        'version'   => $info['version'],
        'stability' => $info['stability']['release'],
        'file_id'   => $file->id,
        'member_id' => sfContext::getInstance()->getUser()->getMemberId(),
        'package_definition' => $xml,
      ));
      $this->package->PluginRelease[] = $release;
      $this->package->save();
    }
    elseif ($svn)
    {
      require_once 'VersionControl/SVN.php';

      $dir = sfConfig::get('sf_cache_dir').'/svn-'.md5($svn);

      $result = VersionControl_SVN::factory('export', array(
        'svn_path' => 'svn', // FIXME: It should be configurable
      ))->run(array($svn, $dir));

      $info = $pear->infoFromDescriptionFile($dir.'/package.xml');
      if ($info instanceof PEAR_Error)
      {
        throw new RuntimeException($info->getMessage());
      }

      require_once 'Archive/Tar.php';

      $filename = sprintf('%s-%s.tgz', $info['name'], $info['version']);

      $tar = new Archive_Tar(sfConfig::get('sf_cache_dir').'/'.$filename, true);
      foreach ($info['filelist'] as $file => $data)
      {
        $tar->addString($info['name'].'-'.$info['version'].'/'.$file, file_get_contents($dir.'/'.$file));
      }
      $tar->addString('package.xml', file_get_contents($dir.'/package.xml'));

      $file = new File();
      $file->setType('application/x-gzip');
      $file->setOriginalFilename($filename);
      $file->setName(strtr($filename, '.', '_'));

      $bin = new FileBin();
      $bin->setBin(file_get_contents(sfConfig::get('sf_cache_dir').'/'.$filename));
      $file->setFileBin($bin);
      $file->save();

      $release = Doctrine::getTable('PluginRelease')->create(array(
        'version'   => $info['version'],
        'stability' => $info['stability']['release'],
        'file_id'   => $file->id,
        'member_id' => sfContext::getInstance()->getUser()->getMemberId(),
        'package_definition' => file_get_contents($dir.'/package.xml'),
      ));
      $this->package->PluginRelease[] = $release;
      $this->package->save();

      sfToolkit::clearDirectory($dir);
      $filesystem = new sfFilesystem();
      $filesystem->remove($dir);
    }
    elseif ($gitUrl && $gitCommit)
    {
      $filesystem = new sfFilesystem();

      require_once 'VersionControl/Git.php';

      $dir = sfConfig::get('sf_cache_dir').'/git-'.md5($gitUrl.$gitCommit);
      $filesystem->mkdirs($dir);

      $git = new VersionControl_Git(sfConfig::get('sf_cache_dir'));
      $git->createClone($gitUrl, false, $dir);
      $filesystem->chmod($dir, 0777);
      $git = new VersionControl_Git($dir);
      $git->checkout($gitCommit);

      $info = $pear->infoFromDescriptionFile($dir.'/package.xml');
      if ($info instanceof PEAR_Error)
      {
        throw new RuntimeException($info->message());
      }

      require_once 'Archive/Tar.php';

      $filename = sprintf('%s-%s.tgz', $info['name'], $info['version']);

      $tar = new Archive_Tar(sfConfig::get('sf_cache_dir').'/'.$filename, true);
      foreach ($info['filelist'] as $file => $data)
      {
        $tar->addString($info['name'].'-'.$info['version'].'/'.$file, file_get_contents($dir.'/'.$file));
      }
      $tar->addString('package.xml', file_get_contents($dir.'/package.xml'));

      $file = new File();
      $file->setType('application/x-gzip');
      $file->setOriginalFilename($filename);
      $file->setName(strtr($filename, '.', '_'));

      $bin = new FileBin();
      $bin->setBin(file_get_contents(sfConfig::get('sf_cache_dir').'/'.$filename));
      $file->setFileBin($bin);
      $file->save();

      $release = Doctrine::getTable('PluginRelease')->create(array(
        'version'   => $info['version'],
        'stability' => $info['stability']['release'],
        'file_id'   => $file->id,
        'member_id' => sfContext::getInstance()->getUser()->getMemberId(),
        'package_definition' => file_get_contents($dir.'/package.xml'),
      ));
      $this->package->PluginRelease[] = $release;
      $this->package->save();

      sfToolkit::clearDirectory($dir);
      $filesystem->remove($dir);
    }
  }
}
