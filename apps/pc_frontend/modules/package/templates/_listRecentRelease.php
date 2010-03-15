<?php if (count($releases)): ?>
<div id="homeRecentList_<?php echo $gadget->getId() ?>" class="dparts homeRecentList"><div class="parts">
<div class="partsHeading"><h3><?php echo __('Recently Releases') ?>
 <?php echo link_to(image_tag('/opPluginChannelServerPlugin/images/feed-icon-14x14.png'), '@package_list_recent_release_atom') ?></h3></div>
<div class="block">
<ul class="articleList">
<?php foreach ($releases as $release): ?>
<li><span class="date"><?php echo op_format_date($release->created_at, 'XShortDateJa') ?></span>
<?php echo link_to($release->Package->name.'-'.$release->version, 'release_detail', $release) ?>
</li>
<?php endforeach; ?>
</ul>
<div class="moreInfo">
<ul class="moreInfo">
<li><?php echo link_to(__('More'), '@package_list_recent_release') ?></li>
</ul>
</div>
</div>
</div></div>
<?php endif; ?>
