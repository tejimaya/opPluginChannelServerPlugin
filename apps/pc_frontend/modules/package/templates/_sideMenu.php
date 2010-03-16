<ul id="channelServerSideMenu">
  <li class="menu"><?php echo link_to(__('Search Plugin'), '@package_search') ?></li>
  <li class="menu"><?php echo link_to(__('Recently Releases'), '@package_list_recent_release') ?></li>
  <li class="tree"><span class="parentTitle"><?php echo __('Category') ?></span>
    <ul>
      <?php foreach ($categories as $category): ?>
        <li><?php echo link_to($category->name, '@package_search?plugin_package_filters[category_id]='.$category->id) ?></li>
      <?php endforeach; ?>
    </ul>
  </li>
</ul>
