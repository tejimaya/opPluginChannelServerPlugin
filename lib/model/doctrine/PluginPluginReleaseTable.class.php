<?php
/**
 */
class PluginPluginReleaseTable extends Doctrine_Table
{
  public function getPager($id, $page = 1, $size = 20)
  {
    $q = Doctrine::getTable('PluginRelease')->createQuery()
      ->where('package_id = ?', $id);

    $pager = new sfDoctrinePager('PluginPackage', $size);
    $pager->setQuery($q);
    $pager->setPage($page);
    $pager->init();

    return $pager;
  }

  public function createByPackageInfo(array $info, File $file, $memberId, $xml)
  {
    return Doctrine::getTable('PluginRelease')->create(array(
      'version'   => $info['version'],
      'stability' => $info['stability']['release'],
      'file_id'   => $file->id,
      'member_id' => $memberId,
      'package_definition' => $xml,
    ));
  }
}
