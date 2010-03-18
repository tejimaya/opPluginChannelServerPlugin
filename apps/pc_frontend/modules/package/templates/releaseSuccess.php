<?php slot('op_sidemenu'); ?>
<?php include_partial('pluginInformationBar', array('package' => $release->Package)) ?>
<?php end_slot(); ?>

<?php
use_helper('opPluginChannelServerPlugin');

$channelOption = '';
if (opPluginChannelServerToolkit::getConfig('channel_name') !== opPluginManager::OPENPNE_PLUGIN_CHANNEL)
{
  $channelOption = ' --channel='.opPluginChannelServerToolkit::getConfig('channel_name');
}

op_include_parts('listBox', 'releaseInfoList', array(
  'title' =>  __('Detail of this release'),
  'list' => array(
    __('Plugin') => $release->Package->name,
    __('Version') => $release->version,
    __('Stability') => __($release->stability),
    __('Release Note') => (PEAR::isError($info->getRawValue())) ? '' : nl2br($info['notes']),
    __('Installation') => 
      __('Install the plugin:').'<br />
      <code>$ ./symfony opPlugin:install '.$release->Package->name.' -r '.$release->version.$channelOption.'</code><br />
      <br />'.
      __('Migrate your model and database:').'<br />
      <code>$ ./symfony openpne:migrate --target='.$release->Package->name.'</code><br />
      ',
    __('Download') => link_to(
      get_plugin_download_url($release->Package->name, $release->version, 'tgz'),
      get_plugin_download_url($release->Package->name, $release->version, 'tgz')
    ),
)));

if ($release->isAllowed($sf_user->getRawValue()->getMember(), 'delete'))
{
  op_include_form('removeRelease', $form, array(
    'title'  => __('Do you want to delete this release?'),
    'button' => __('Delete'),
    'url'    => url_for('@release_delete?id='.$release->id),
  ));
}
