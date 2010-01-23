<?php

/**
 * PluginPluginPackage form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginPluginPackageForm extends BasePluginPackageForm
{
  public function setup()
  {
    parent::setup();

    unset($this['id']);
    $this->useFields(array(
      'name', 'description', 'repository',
      'bts', 'category_id', 'file_id',
    ));

    $this
      ->setWidget('repository', new sfWidgetFormInputText(array('label' => 'Repository URL')))
      ->setWidget('bts', new sfWidgetFormInputText(array('label' => 'BTS URL')))
      ->setWidget('file_id', new sfWidgetFormInputFile(array('label' => 'Image')))

      ->setValidator('name', new sfValidatorCallback(array('callback' => array($this, 'validatePluginName'), 'required' => true)))
      ->setValidator('repository', new sfValidatorUrl(array('required' => false)))
      ->setValidator('bts', new sfValidatorUrl(array('required' => false)))
      ->setValidator('file_id', new opValidatorImageFile(array('required' => false)))
    ;

    $this->widgetSchema
      ->setLabel('name', 'Plugin Name')
      ->setHelp('name', 'Plugin name must start with "op" and end with "Plugin"')
    ;

    if (sfConfig::get('op_is_use_captcha', false) && $this->isNew())
    {
      $this->embedForm('captcha', new opCaptchaForm());
    }
  }

  public function validatePluginName($validator, $value, $arguments)
  {
    $_validator = new opValidatorString(array('max_length' => 64));
    $value = $_validator->clean($value);

    if (!preg_match('/^op.+Plugin$/', $value))
    {
      throw new sfValidatorError($validator, 'invalid');
    }

    return $value;
  }

  public function updateObject($values = null)
  {
    if (is_null($values))
    {
      $values = $this->getValues();
    }

    $image = null;
    if (array_key_exists('file_id', $values))
    {
      $image = $values['file_id'];
      unset($values['file_id']);
    }

    $obj = parent::updateObject($values);

    if ($this->isNew())
    {
      $obj->PluginMember[0]->Member = sfContext::getInstance()->getUser()->getMember();
      $obj->PluginMember[0]->position = 'lead';
    }

    if ($image instanceof sfValidatedFile)
    {
      unset($obj->Image);

      $obj->Image->setFromValidatedFile($image);
      $obj->Image->name = 'plugin_'.$obj->getId().'_'.$obj->Image->name;
    }

    $obj->save();
  }

  protected function processUploadedFile($field, $filename = null, $values = null)
  {
    return '';
  }
}
