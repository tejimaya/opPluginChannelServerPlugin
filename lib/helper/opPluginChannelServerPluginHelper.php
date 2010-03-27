<?php

/**
* Copyright 2010 Kousuke Ebihara
*
* Licensed under the Apache License, Version 2.0 (the "License");
* you may not use this file except in compliance with the License.
* You may obtain a copy of the License at
*
* http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS,
* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and
* limitations under the License.
*/

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

function render_package_dependency_list($dependencies_array, $class = 'deplist')
{
  $required = array();

  foreach ($dependencies_array as $v)
  {
    if (isset($v['optional']) && 'no' !== $v['optional'])
    {
      continue;
    }

    if ('php' === $v['type'])
    {
      $required['PHP'][$v['rel']] = $v['version'];
    }
    elseif ('pkg' === $v['type'])
    {
      $name = $v['name'];
      if ('openpne' === $name)
      {
        $name = 'OpenPNE';
      }
      elseif ('PEAR' === $name || 'symfony' === $name)
      {
        continue;
      }

      if ('not' === $v['rel'])
      {
        $v['version'] = true;
      }
      $required[$name][$v['rel']] = $v['version'];
    }
  }

  $result = '<dl class="'.$class.'">';
  foreach ($required as $k => $v)
  {
    $result .= '<dt>'.$k.'</dt>';
    $result .= '<dd><ul>';

    if (!empty($v['not']))
    {
      $result .= '<li>'.__('If you installed %1%, you cannot install this plugin.', array('%1%' => $k)).'</li>';
    }

    if (isset($v['ge']))
    {
      $result .= '<li>'.__('Greater than %1%', array('%1%' => $v['ge'])).'</li>';
    }

    if (isset($v['le']))
    {
      $result .= '<li>'.__('Less than %1%', array('%1%' => $v['le'])).'</li>';
    }

    $result .= '</ul></dd>';
  }
  $result .= '</dl>';

  return $result;
}

function get_target_openpne(PluginRelease $release)
{
  if ($release->op_version_ge && $release->op_version_le)
  {
    return __('Compatible with OpenPNE %1% to %2%', array('%1%' => $release->op_version_ge_string, '%2%' => $release->op_version_le_string));
  }
  elseif ($release->op_version_ge)
  {
    return __('Compatible with greater than OpenPNE %1%', array('%1%' => $release->op_version_ge_string));
  }
  elseif ($release->op_version_le)
  {
    return __('Compatible with less than OpenPNE %1%', array('%1%' => $release->op_version_le_string));
  }

  return __('Compatible with all OpenPNE 3');
}
