<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Finder\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Session\Session;

/**
 * Methods supporting a list of search terms.
 *
 * @since  4.0.0
 */
class SearchesController extends BaseController
{
	/**
	 * Method to reset the search log table.
	 *
	 * @return  boolean
	 */
	public function reset()
	{
		// Check for request forgeries.
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		$model = $this->getModel('Searches');

		if (!$model->reset())
		{
			$this->app->enqueueMessage($model->getError(), 'error');
		}

		$this->setRedirect('index.php?option=com_finder&view=searches');
	}
}
