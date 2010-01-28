<?php

op_include_parts('listBox', 'releaseInfoList', array(
  'title' =>  __('Detail of this release'),
  'list' => array(
    __('Plugin') => $release->Package->name,
    __('Version') => $release->version,
    __('Stability') => __($release->stability),
    __('Release Note') => nl2br($info['notes']),
    __('Download') => link_to(
      url_for('@plugin_download_tgz?version='.$release->version.'&name='.$release->Package->name, true),
      '@plugin_download_tgz?version='.$release->version.'&name='.$release->Package->name),
  ),
));
