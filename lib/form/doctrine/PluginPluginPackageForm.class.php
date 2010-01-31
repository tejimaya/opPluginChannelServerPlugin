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

    $this->useFields(array(
      'name', 'summary', 'description', 'license',
      'repository', 'bts', 'category_id', 'file_id',
    ));

    $this
      ->setWidget('repository', new sfWidgetFormInputText(array('label' => 'Repository URL')))
      ->setWidget('bts', new sfWidgetFormInputText(array('label' => 'BTS URL')))
      ->setWidget('summary', new sfWidgetFormInputText())
      ->setWidget('license', new sfWidgetFormInputText())
      ->addEditableImageFormWidget('file_id', array('label' => 'Image'))

      ->setValidator('name', new sfValidatorCallback(array('callback' => array($this, 'validatePluginName'), 'required' => true)))
      ->setValidator('repository', new sfValidatorUrl(array('required' => false)))
      ->setValidator('bts', new sfValidatorUrl(array('required' => false)))
      ->setValidator('file_id', new opValidatorImageFile(array('required' => false)))
      ->setValidator('id', new opValidatorString(array('required' => false)))
    ;

    $this->widgetSchema
      ->setLabel('name', 'Plugin Name')
      ->setHelp('name', 'Plugin name must start with "op" and end with "Plugin"')
      ->setHelp('license', 'License should be "MIT", "BSD", "LGPL", "PHP", "Apache" (case-insensitive). If you select other license, plugin installer will output notice.')
    ;

    if (sfConfig::get('op_is_use_captcha', false) && $this->isNew())
    {
      $this->embedForm('captcha', new opCaptchaForm());
    }

    if (!$this->isNew())
    {
      unset($this['name']);
    }
  }

  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    unset($taintedValues['id']);
    if (!$this->isNew())
    {
      $taintedValues['id'] = $this->getObject()->id;
    }

    parent::bind($taintedValues, $taintedFiles);
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
    $obj->save();

    if ($this->isNew())
    {
      $obj->PluginMember[0]->Member = sfContext::getInstance()->getUser()->getMember();
      $obj->PluginMember[0]->position = 'lead';
      $obj->PluginMember[0]->is_active = true;
    }

    if ($image instanceof sfValidatedFile)
    {
      $oldImage = clone $obj->Image;

      $obj->Image = new File();
      $obj->Image->setFromValidatedFile($image);
      $obj->Image->name = 'plugin_'.$obj->getId().'_'.$obj->Image->name;
      if ($oldImage)
      {
        $oldImage->delete();
      }
    }
    elseif ($this->getValue('file_id_delete'))
    {
      if ($obj->Image)
      {
        $obj->Image->delete();
        $obj->Image = null;
      }
    }

    $obj->save();
  }

  protected function processUploadedFile($field, $filename = null, $values = null)
  {
    return '';
  }

  public function addEditableImageFormWidget($name, $options = array())
  {
    $options = array_merge(array(
      'file_src'     => '',
      'is_image'     => true,
      'with_delete'  => true,
      'delete_label' => sfContext::getInstance()->getI18N()->__('Remove the current photo')
    ), $options);

    if (!$this->isNew() && $this->getObject()->$name)
    {
      sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
      $options['edit_mode'] = true;
      $options['template'] = get_partial('default/formEditImage', array('image' => $this->getObject()));
      $this->setValidator('file_id_delete', new sfValidatorBoolean(array('required' => false)));
    }
    else
    {
      $options['edit_mode'] = false;
    }

    $this->setWidget($name, new sfWidgetFormInputFileEditable($options, array('size' => 40)));

    return $this;
  }
}
