<?php slot('op_sidemenu'); ?>
<?php include_partial('pluginInformationBar', array('package' => $package)) ?>
<?php end_slot(); ?>

<?php
$title = __('Releases of this plugin');
?>
<?php if ($pager->getNbResults()): ?>
<div class="dparts recentList"><div class="parts">
<div class="partsHeading"><h3><?php echo $title ?></h3></div>

<form action="<?php url_for('@package_list_release?name='.$package->name) ?>" method="get">
<p>
<?php echo __('Display releases that are usable on a version of OpenPNE %1%', array(
  '%1%' => '<input type="text" class="input_text" name="version" size="10" value="'.$version.'" />',
)); ?>
<input type="submit" class="input_submit" value="<?php echo __('Filter') ?>" />
</p>
</form>

<?php

$pager_url = '@package_list_release?name='.$package->name.'&page=%d';
if ($version)
{
  $pager_url .= '&version='.$version;
}

?>

<?php echo op_include_pager_navigation($pager, $pager_url); ?>
<?php foreach ($pager->getResults() as $release): ?>
<dl>
<dt><?php echo op_format_date($release->created_at, 'XDateTimeJa') ?></dt>
<dd><?php echo link_to($release->version, 'release_detail', $release) ?></dd>
</dl>
<?php endforeach; ?>
<?php echo op_include_pager_navigation($pager, $pager_url); ?>
</div></div>
<?php else: ?>
<?php op_include_box('pluginList', __('There are no releases.'), array('title' => $title)) ?>
<?php endif; ?>
