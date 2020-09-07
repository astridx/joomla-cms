<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_modules
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Modules\Administrator\View\Select;

\defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * HTML View class for the Modules component
 *
 * @since  1.6
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * The model state
	 *
	 * @var  \JObject
	 */
	protected $state;

	/**
	 * An array of items
	 *
	 * @var  array
	 */
	protected $items;

	/**
	 * A suffix for links for modal use
	 *
	 * @var  string
	 */
	protected $modalLink;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->modalLink = '';

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new GenericDataException(implode("\n", $errors), 500);
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		$state = $this->get('State');

		// Add page title
		if ((int) $state->get('client_id') === 1)
		{
			ToolbarHelper::title(Text::_('COM_MODULES_MANAGER_MODULES_ADMIN'), 'cube module');
		}
		else
		{
			ToolbarHelper::title(Text::_('COM_MODULES_MANAGER_MODULES_SITE'), 'cube module');
		}

		// Get the toolbar object instance
		$bar = Toolbar::getInstance('toolbar');

		// Instantiate a new FileLayout instance and render the layout
		$layout = new FileLayout('toolbar.cancelselect');

		$bar->appendButton('Custom', $layout->render(), 'new');
	}
}
