<?php echo '<?xml version="1.0" encoding="utf-8" ?>' ?>

<r xmlns="http://pear.php.net/dtd/rest.release2"
   xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
   xmlns:xlink="http://www.w3.org/1999/xlink"
   xsi:schemaLocation="http://pear.php.net/dtd/rest.release2
                       http://pear.php.net/dtd/rest.release2.xsd"
>
 <p xlink:href="<?php echo url_for('plugin_rest_package_info', $package) ?>"><?php echo $package->name ?></p>
 <c><?php echo $channel_name ?></c>
 <v><?php echo $release->version ?></v>
 <a><?php echo $release->version ?></a>
 <mp>5.2.3</mp>
 <st><?php echo $release->stability ?></st>
 <l>Dictatoric License</l>
 <m>ebihara</m>
 <s><?php echo $info['summary'] ?></s>
 <d><?php echo $info['description'] ?></d>
 <da><?php echo $info['date'] ?> <?php echo $info['time'] ?></da>
 <n><?php echo $info['notes'] ?></n>
 <f>19588</f>
 <g>http://pear.example/get/WorldDominator-0.1.2</g>
 <x xlink:href="package.<?php echo $release->version ?>.xml"/>
</r>
