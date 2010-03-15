<div class="dparts recentList"><div class="parts">
<div class="partsHeading"><h3><?php echo __('Recently Releases') ?>
 <?php echo link_to(image_tag('/opPluginChannelServerPlugin/images/feed-icon-14x14.png'), '@package_list_recent_release_atom') ?></h3></div>
<?php echo op_include_pager_navigation($pager, '@package_list_recent_release?page=%d'); ?>
<?php foreach ($pager->getResults() as $release): ?>
<dl>
<dt><?php echo op_format_date($release->created_at, 'XDateTimeJa') ?></dt>
<dd><?php echo link_to($release->Package->name.'-'.$release->version, 'release_detail', $release) ?></dd>
</dl>
<?php endforeach; ?>
<?php echo op_include_pager_navigation($pager, '@package_list_recent_release?page=%d'); ?>
</div></div>
