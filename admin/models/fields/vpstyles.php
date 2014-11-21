<?php
/**
 * @package      VipPortfolio
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package      VipPortfolio
 * @subpackage   Components
 * @since        1.6
 */
class JFormFieldVpStyles extends JFormFieldList
{
    /**
     * The form field type.
     *
     * @var     string
     * @since   1.6
     */
    protected $type = 'VpStyles';

    /**
     * Method to get the field options.
     *
     * @return  array   The field option objects.
     * @since   1.6
     */
    protected function getOptions()
    {
        $options = array(
            array("value" => 'list', "text" => 'List View'),
            array("value" => 'lineal', "text" => 'Lineal View'),
            array("value" => 'slidegallery', "text" => 'SlideGallery View'),
            array("value" => 'camera', "text" => 'Camera View'),
            array("value" => 'galleria', "text" => 'Galleria View')
        );

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}
