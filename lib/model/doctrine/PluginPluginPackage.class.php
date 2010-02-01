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
 * PluginPluginPackage
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    opPluginChannelServerPlugin
 * @subpackage model
 * @author     Kousuke Ebihara <ebihara@tejimaya.com>
 */
abstract class PluginPluginPackage extends BasePluginPackage
{
  public function getImageFilename()
  {
    return (string)$this->getImage();
  }

  public function getFile()
  {
    return $this->getImage();
  }

  public function getLeadMemberIds()
  {
    $results = array();

    $list = Doctrine::getTable('PluginMember')->createQuery()
      ->where('package_id = ?', $this->id)
      ->andWhere('position = ?', 'lead')
      ->fetchArray();

    foreach ($list as $member)
    {
      $results[] = $member['member_id'];
    }

    return $results;
  }

  public function getMembers($limit = null, $isRandom = false)
  {
    $q = Doctrine::getTable('PluginMember')->createQuery()
      ->where('package_id = ?', $this->id);

    if (!is_null($limit))
    {
      $q->limit($limit);
    }

    if ($isRandom)
    {
      $expr = new Doctrine_Expression('RANDOM()');
      $q->orderBy($expr);
    }

    $members = $q->execute();
    if (!$members->count())
    {
      return false;
    }

    $q = Doctrine::getTable('Member')->createQuery()
      ->whereIn('id', array_values($members->toKeyValueArray('id', 'member_id')));

    return $q->execute();
  }

  public function countMembers()
  {
    return Doctrine::getTable('PluginMember')
      ->createQuery()
      ->where('package_id = ?', array($this->id))
      ->count();
  }

  public function countUsers()
  {
    return Doctrine::getTable('PluginUser')
      ->createQuery()
      ->where('package_id = ?', array($this->id))
      ->count();
  }

  public function isUser($id)
  {
    return (bool)$this->getUser($id);
  }

  public function getUser($id)
  {
    return Doctrine::getTable('PluginUser')
      ->createQuery()
      ->where('package_id = ?', array($this->id))
      ->where('member_id = ?', array($id))
      ->fetchOne();
  }

  public function isLead($id)
  {
    return in_array($id, $this->getLeadMemberIds());
  }

  public function toggleUsing($id)
  {
    if ($user = $this->getUser($id))
    {
      $user->delete();
    }
    else
    {
      $this->PluginUser[]->member_id = $id;
      $this->save();
    }
  }

  public function getLatestRelease()
  {
    $versions = array();
    $_versions = Doctrine::getTable('PluginRelease')
      ->createQuery()
      ->select('version')
      ->where('package_id = ?', $this->id)
      ->fetchArray();

    foreach ($_versions as $v)
    {
      $versions[] = $v['version'];
    }

    if (!$versions)
    {
      return false;
    }

    usort($versions, 'version_compare');

    return array_shift($versions);
  }

  public function getStableRelease()
  {
    $versions = array();
    $_versions = Doctrine::getTable('PluginRelease')
      ->createQuery()
      ->select('version')
      ->where('package_id = ?', $this->id)
      ->andWhere('stability = ?', 'stable')
      ->fetchArray();

    foreach ($_versions as $v)
    {
      $versions[] = $v['version'];
    }

    if (!$versions)
    {
      return false;
    }

    usort($versions, 'version_compare');

    return array_shift($versions);
  }

  public function getAlphaRelease()
  {
    $versions = array();
    $_versions = Doctrine::getTable('PluginRelease')
      ->createQuery()
      ->select('version')
      ->where('package_id = ?', $this->id)
      ->andWhere('stability = ?', 'alpha')
      ->fetchArray();

    foreach ($_versions as $v)
    {
      $versions[] = $v['version'];
    }

    if (!$versions)
    {
      return false;
    }

    usort($versions, 'version_compare');

    return array_shift($versions);
  }

  public function getBetaRelease()
  {
    $versions = array();
    $_versions = Doctrine::getTable('PluginRelease')
      ->createQuery()
      ->select('version')
      ->where('package_id = ?', $this->id)
      ->andWhere('stability = ?', 'beta')
      ->fetchArray();

    foreach ($_versions as $v)
    {
      $versions[] = $v['version'];
    }

    if (!$versions)
    {
      return false;
    }

    usort($versions, 'version_compare');

    return array_shift($versions);
  }

  public function getDevelRelease()
  {
    $versions = array();
    $_versions = Doctrine::getTable('PluginRelease')
      ->createQuery()
      ->select('version')
      ->where('package_id = ?', $this->id)
      ->andWhere('stability = ?', 'devel')
      ->fetchArray();

    foreach ($_versions as $v)
    {
      $versions[] = $v['version'];
    }

    if (!$versions)
    {
      return false;
    }

    usort($versions, 'version_compare');

    return array_shift($versions);
  }

  public function getReleases($limit = 5)
  {
    return Doctrine::getTable('PluginRelease')->createQuery()
      ->where('package_id = ?', $this->id)
      ->limit($limit)
      ->orderBy('created_at DESC')
      ->execute();
  }
}
