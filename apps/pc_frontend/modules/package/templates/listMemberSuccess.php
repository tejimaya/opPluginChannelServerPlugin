<?php

op_include_parts('photoTable', 'pluginList', array(
  'title' => __('Developing Plugin List'),
  'list' => $pager->getResults(),
  'link_to' => '@package_home_id?id=',
  'pager' => $pager,
  'link_to_pager' => '@package_listMember_member?page=%d&id='.$member->id,
));
