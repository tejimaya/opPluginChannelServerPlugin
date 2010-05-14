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
 * opPluginChannelServerToolkit
 *
 * @package    opPluginChannelServerPlugin
 * @subpackage util
 * @author     Kousuke Ebihara <ebihara@tejimaya.com>
 */
class opPluginChannelServerToolkit
{
  public static function registerPearChannel(PEAR_ChannelFile $channel, $cacheDir = null)
  {
    error_reporting(error_reporting() & ~(E_STRICT | E_DEPRECATED));

    require_once 'PEAR.php';
    require_once 'PEAR/Common.php';
    require_once 'PEAR/ChannelFile.php';

    if (null === $cacheDir)
    {
      $cacheDir = sfConfig::get('sf_cache_dir');
    }

    $registry = new PEAR_Registry($cacheDir, $channel);

    $pear = new PEAR_Common();
    $pear->config->setRegistry($registry);

    if (!$registry->channelExists($channel->getName()))
    {
      $registry->addChannel($channel);
    }
    else
    {
      $registry->updateChannel($channel);
    }

    return $pear;
  }

  public static function generatePearChannelFile($channelName, $summary, $alias, $baseUrl)
  {
    error_reporting(error_reporting() & ~(E_STRICT | E_DEPRECATED));

    require_once 'PEAR.php';
    require_once 'PEAR/Common.php';
    require_once 'PEAR/ChannelFile.php';

    $channel = new PEAR_ChannelFile();
    $channel->setName($channelName);
    $channel->setSummary($summary);
    $channel->setAlias($alias);
    $channel->setBaseURL('REST1.0', $baseUrl);
    $channel->setBaseURL('REST1.1', $baseUrl);
    $channel->setBaseURL('REST1.2', $baseUrl);
    $channel->setBaseURL('REST1.3', $baseUrl);

    return $channel;
  }

  public static function getConfig($name, $default = null)
  {
    return Doctrine::getTable('SnsConfig')->get(opPluginChannelServerPluginConfiguration::CONFIG_KEY_PREFIX.$name, $default);
  }

  public static function generateTarByPluginDir(array $info, $filename, $input, $output, $isCompress = true)
  {
    $timeLimit = ini_get('max_execution_time');
    set_time_limit(0);

    require_once 'Archive/Tar.php';

    $tar = new Archive_Tar($output.'/'.$filename, $isCompress);
    foreach ($info['filelist'] as $file => $data)
    {
      $tar->addString($info['name'].'-'.$info['version'].'/'.$file, file_get_contents($input.'/'.$file));
    }
    $tar->addString('package.xml', file_get_contents($input.'/package.xml'));

    set_time_limit($timeLimit);
  }

  public static function getFilePathToCache($name, $version)
  {
    $ds = DIRECTORY_SEPARATOR;
    $path = sfConfig::get('sf_plugins_dir').$ds.'opPluginChannelServerPlugin'.$ds.'web'.$ds.'get'.$ds.$name.'-'.$version.'.tgz';

    return $path;
  }

  public static function uploadFileToS3($account, $secret, $bucket, File $file)
  {
    require_once 'Services/Amazon/S3.php';

    $s3 = Services_Amazon_S3::getAccount($account, $secret);
    $bucket = $s3->getBucket($bucket);

    $object = $bucket->getObject($file->original_filename);
    $object->contentType = $file->type;
    $object->acl = Services_Amazon_S3_AccessControlList::ACL_PUBLIC_READ;
    $object->data = $file->FileBin->bin;
    $object->save();
  }

  public static function deleteFileFromS3($account, $secret, $bucket, $filename)
  {
    require_once 'Services/Amazon/S3.php';

    $s3 = Services_Amazon_S3::getAccount($account, $secret);
    $bucket = $s3->getBucket($bucket);

    $object = $bucket->getObject($filename);
    $object->delete();
  }

  public static function calculateVersionId($version)
  {
    if (!$version)
    {
      return null;
    }

    $extras = array(
      'dev' => -0.9,
      'alpha' => -0.8,
      'beta' => -0.7,
      'RC' => -0.6,
      'rc' => -0.6,
      'a' => -0.8,
      'b' => -0.7,
    );
    $extra = '';

    $pattern = '('.implode(array_keys($extras), '|').')';
    $matches = array();
    if (preg_match('/\b'.$pattern.'\b/', $version, $matches))
    {
      $extra = $matches[1];
    }

    $parts = array_pad(explode('.', $version, 4), 4, 0);
    list($major, $minor, $bug, $urgency) = $parts;

    $extraNum = 0.0;
    if (isset($extras[$extra]))
    {
      $extraNum = $extras[$extra];
    }

    return (float)sprintf('%d%02d%02d%02d', (int)$major, (int)$minor, (int)$bug, (int)$urgency) + $extraNum;
  }

  public function getOpenPNEDependencyFromArray($array)
  {
    $results = array(
      'ge' => null,
      'le' => null,
    );

    foreach ($array as $v)
    {
      if (isset($v['optional']) && 'no' !== $v['optional'])
      {
        continue;
      }

      if ('pkg' === $v['type'])
      {
        $name = $v['name'];
        if ('openpne' !== $name)
        {
          continue;
        }

        $results[$v['rel']] = $v['version'];
      }
    }

    return $results;
  }
}
