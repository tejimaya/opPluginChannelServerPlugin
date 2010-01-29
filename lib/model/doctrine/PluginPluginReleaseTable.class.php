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
}
