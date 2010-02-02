<?php if (count($plugins)): ?>
<div id="homeRecentList_<?php echo $gadget->getId() ?>" class="dparts homeRecentList"><div class="parts">
<div class="partsHeading"><h3><?php echo __('Developing Plugin List') ?></h3></div>
<div class="block">
<ul class="articleList">
<?php foreach ($plugins as $plugin): ?>
<li>
<?php echo link_to($plugin->name, 'package_home', $plugin) ?>
</li>
<?php endforeach; ?>
</ul>
<div class="moreInfo">
<ul class="moreInfo">
<li><?php echo link_to(__('More'), '@package_listMember?id='.$member->id) ?></li>
</ul>
</div>
</div>
</div></div>
<?php endif; ?>
