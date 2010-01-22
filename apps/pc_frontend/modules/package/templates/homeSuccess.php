<?php slot('op_sidemenu'); ?>
<?php
$options = array(
  'object'      => $package,
  'name_method' => 'getName',
);
op_include_parts('memberImageBox', 'packageImageBox', $options);
?>

<div style = "
background-color:#AAEEFF;
color:#0000FF;
font-size:20px;
font-weight:bold;
margin:10px;
padding:10px;
text-align:center;
">
<span><?php echo $package->countUsers() ?></span><br /><span>users</span>
<p style="margin-top: 10px; text-align: center; font-size: 9px; color: #000;">I use this plugin</p>
</div>

<?php
$options = array(
  'title' => __('Plugin Developers'),
  'list'  => $package->getMembers(),
  'crownIds' => $package->getLeadMemberIds()->getRawValue(),
  'link_to' => '@member_profile?id=',
  'moreInfo' => array(link_to(sprintf('%s(%d)', __('Show all'), $package->countMembers()), 'package/memberList?id='.$package->id)),
);

op_include_parts('nineTable', 'developerList', $options);
?>
<?php end_slot(); ?>

<?php
$list = array(
  __('Description') => nl2br($package->description),
);

$options = array(
  'title' => __('Detail of this plugin'),
  'list' => $list,
);

op_include_parts('listBox', 'packageInformation', $options);
?>
