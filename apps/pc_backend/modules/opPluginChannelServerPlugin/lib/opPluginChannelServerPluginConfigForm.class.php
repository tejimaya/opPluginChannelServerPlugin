<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

class opPluginChannelServerPluginConfigForm extends BaseForm
{
  public function configure()
  {
    $this
      ->setWidget('channel_name', new sfWidgetFormInputText())
      ->setValidator('channel_name', new opValidatorString())

      ->setWidget('summary', new sfWidgetFormInputText())
      ->setValidator('summary', new opValidatorString())

      ->setWidget('suggestedalias', new sfWidgetFormInputText())
      ->setValidator('suggestedalias', new opValidatorString())
    ;

    $this->getWidgetSchema()
      ->setNameFormat('plugin_config[%s]')

      ->setHelp('channel_name', 'In default, it will be accessing server name.')
      ->setHelp('summary', 'In default, it will be channel name.')
      ->setHelp('suggestedalias', 'In default, it will be channel name.')
    ;
  }

  public function save()
  {
    foreach ($this->getValues() as $k => $v)
    {
      $key = opPluginChannelServerPluginConfiguration::CONFIG_KEY_PREFIX.$k;
      $config = Doctrine::getTable('SnsConfig')->retrieveByName($key);
      if (!$config)
      {
        $config = new SnsConfig();
        $config->setName($key);
      }
      $config->setValue($v);
      $config->save();
    }
  }
}
