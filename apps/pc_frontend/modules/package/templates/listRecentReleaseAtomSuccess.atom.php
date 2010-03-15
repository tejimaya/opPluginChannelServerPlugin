<?php echo '<?xml version="1.0" encoding="utf-8"?>' ?>

<feed xmlns="http://www.w3.org/2005/Atom">
  <title><?php echo __('%1% Recently Releases', array('%1%' => $channel_name)) ?></title>
  <link href="<?php echo url_for('@package_list_recent_release_atom', true) ?>" rel="self"/>
  <link href="<?php echo url_for('@homepage', true) ?>"/>
  <updated><?php echo gmstrftime('%Y-%m-%dT%H:%M:%SZ', $list[0]->getDateTimeObject('updated_at')->format('U')) ?></updated>
  <author>
    <name><?php echo $channel_name ?></name>
  </author>
  <id><?php echo sha1(url_for('@package_list_recent_release_atom', true)) ?></id>

<?php foreach ($list as $release): ?>
  <entry>
    <title><?php echo $release->Package->name.'-'.$release->version ?></title>
    <link href="<?php echo url_for('release_detail', $release, true) ?>" />
    <id><?php echo sha1($release->id) ?></id>
    <updated><?php echo gmstrftime('%Y-%m-%dT%H:%M:%SZ', $release->getDateTimeObject('updated_at')->format('U')) ?></updated>
    <summary><?php echo $release->Package->summary ?></summary>
    <author>
      <name><?php echo $release->Member->name ?></name>
    </author>
  </entry>
<?php endforeach; ?>
</feed>
