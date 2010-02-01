<?php

/**
 * PluginPluginPackage form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage filter
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormFilterPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginPluginPackageFormFilter extends BasePluginPackageFormFilter
{
  public function __construct($defaults = array(), $options = array(), $CSRFSecret = null)
  {
    parent::__construct($defaults, $options, false);
  }

  public function setup()
  {
    parent::setup();

    $this->useFields(array('name', 'description', 'category_id'));
  }
}
