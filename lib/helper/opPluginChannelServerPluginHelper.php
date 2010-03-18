<?php

function get_plugin_download_url($name, $version, $extension = '')
{
  $base_url = opPluginChannelServerToolkit::getConfig('package_download_base_url');
  if ($base_url)
  {
    $filename = $name.'-'.$version;
    if ($extension)
    {
      $filename .= '.'.$extension;
    }

    return $base_url.$filename;
  }

  $route = '@plugin_download_without_extension';
  if ($extension)
  {
    $route = '@plugin_download_'.$extension;
  }
  $route .= '?version='.$version.'&name='.$name;

  return url_for($route, true);
}
