<?php
/**
 * @package      VipPortfolio
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('itprism.controller.admin');

/**
 * Vip Portfolio Projects Controller
 *
 * @package     VipPortfolio
 * @subpackage  Components
 */
class VipPortfolioControllerProjects extends ITPrismControllerAdmin
{
    /**
     * Proxy for getModel.
     * @since   1.6
     */
    public function getModel($name = 'Project', $prefix = 'VipPortfolioModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        // Load the component parameters.
        $params = JComponentHelper::getParams($this->option);
        /** @var  $params Joomla\Registry\Registry */

        // Extension parameters
        $model->setImagesUri($params->get("images_directory", "images/vipportfolio"));
        $model->setImagesFolder(JPATH_ROOT . DIRECTORY_SEPARATOR . $params->get("images_directory", "images/vipportfolio"));

        return $model;
    }
}
