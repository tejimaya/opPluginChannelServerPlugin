<?php use_helper('opPluginChannelServerPlugin', 'opJavascript'); ?>

<?php slot('op_sidemenu'); ?>
<?php include_partial('pluginInformationBar', array('package' => $release->Package)) ?>
<?php end_slot(); ?>

<?php slot('_op_depinfo'); ?>
<p id="op_depinfo">
<?php echo get_target_openpne($release->getRawValue()); ?>
<?php if ($release->isAllowed($sf_user->getRawValue()->getMember(), 'add_deps')): ?>
(<?php echo link_to_function(__('Edit'), visual_effect('fade', 'op_depinfo', array('afterFinishInternal' =>' function(effect){
'.visual_effect('appear', 'op_dep_edit').'}'))); ?>)
<?php endif; ?>
</p>

<?php if ($release->isAllowed($sf_user->getRawValue()->getMember(), 'add_deps')): ?>
<div id="op_dep_edit" style="display: none">
<form action="<?php echo url_for('release_add_deps', $release) ?>" method="post">
<?php echo $depForm->renderHiddenFields(); ?>
<?php echo __('Compatible with OpenPNE %1% to %2%', array('%1%' => $depForm['ge'], '%2%' => $depForm['le'])); ?>
<p><input type="submit" class="input_submit" value="<?php echo __('Update') ?>" /></p>
</form>
</div>
<?php endif; ?>
<?php end_slot(); ?>

<?php
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
    __('Target OpenPNE Version') => get_slot('_op_depinfo'),
    __('Dependency') => (PEAR::isError($info->getRawValue())) ? '' : render_package_dependency_list($info['release_deps']->getRawValue()),
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
