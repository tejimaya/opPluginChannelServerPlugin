<div id="pluginSearchBox" class="parts">
<form action="<?php echo url_for('@package_search') ?>" method="get">
<input type="text" class="input_text"  value="" name="plugin_package_filters[keyword][text]" />
<input type="submit" class="input_submit" value="<?php echo __('Search') ?>" />
</form>
</div>
