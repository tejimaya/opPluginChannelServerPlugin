<?php
/**
 */
class PluginPluginMemberTable extends Doctrine_Table
{
  public function getLeadPlugins($id)
  {
    return $this->createQuery()
      ->where('member_id = ?', $id)
      ->andWhere('position = ?', 'lead')
      ->execute();
  }

  public function countJoinRequests($id)
  {
    $ids = array();
    foreach ($this->getLeadPlugins($id) as $v)
    {
      $ids[] = $v->package_id;
    }

    if (!$ids)
    {
      return 0;
    }

    return (int)$this->createQuery('p')
      ->select('COUNT(p.id)')
      ->whereIn('p.package_id', $ids)
      ->andWhere('p.is_active = ?', false)
      ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
  }

  public function getJoinRequests($id)
  {
    $ids = array();
    foreach ($this->getLeadPlugins($id) as $v)
    {
      $ids[] = $v->package_id;
    }

    if (!$ids)
    {
      return false;
    }

    return $this->createQuery('p')
      ->whereIn('p.package_id', $ids)
      ->andWhere('p.is_active = ?', false)
      ->execute();
  }
}
