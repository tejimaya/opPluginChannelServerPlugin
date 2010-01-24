<div id="AddReleaseByTgz" class="dparts form">
<div class="parts">

<div class="partsHeading"><h3><?php echo __('Add release by package file') ?></h3></div>

<form action="<?php echo url_for('package_add_release', $package) ?>" method="post" enctype="multipart/form-data">
<table>
<?php $form->renderGlobalErrors(); ?>
<?php echo $form['tgz_file']->renderRow() ?>
</table>
<div class="operation">
<ul class="moreInfo button">
<li>
<?php echo $form->renderHiddenFields(); ?>
<input type="submit" value="<?php echo __('Send') ?>" class="input_submit" />
</li>
</ul>
</div>
</form>

</div>
</div>

<div id="AddReleaseBySvn" class="dparts">
<div class="parts">

<div class="partsHeading"><h3><?php echo __('Add release by Subversion repository') ?></h3></div>

<form action="<?php echo url_for('package_add_release', $package) ?>" method="post">
<table>
<?php $form->renderGlobalErrors(); ?>
<?php echo $form['svn_url']->renderRow() ?>
</table>
<div class="operation">
<ul class="moreInfo button">
<li>
<?php echo $form->renderHiddenFields(); ?>
<input type="submit" value="<?php echo __('Send') ?>" class="input_submit" />
</li>
</ul>
</div>
</form>

</div>
</div>

<div id="AddReleaseByGit" class="dparts">
<div class="parts">

<div class="partsHeading"><h3><?php echo __('Add release by Git repository') ?></h3></div>

<table>
<tr>
  <th>File</th><td></td>
</tr>
</table>

<?php
/*
echo op_include_form('PackageAddReleaseForm', $form, array(
  'url'         => url_for('package_add_release', $package),
  'title'       => 'Add Release',
  'isMultipart' => true,
))
*/
?>

</div>
</div>
