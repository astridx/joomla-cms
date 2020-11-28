<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Console;

\defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Installer\InstallerHelper;
use Joomla\Console\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Console command for discovering extensions
 *
 * @since  __DEPLOY_VERSION__
 */
class ExtensionDiscoverCommand extends AbstractCommand
{
	/**
	 * The default command name
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	protected static $defaultName = 'extension:discover';

	/**
	 * Stores the Input Object
	 * @var InputInterface
	 * @since __DEPLOY_VERSION__
	 */
	private $cliInput;

	/**
	 * SymfonyStyle Object
	 * @var SymfonyStyle
	 * @since __DEPLOY_VERSION__
	 */
	private $ioStyle;

	/**
	 * Exit Code For Discover Failure
	 * @since
	 */
	public const DISCOVER_FAILED = 1;

	/**
	 * Exit Code For Discover Success
	 * @since
	 */
	public const DISCOVER_SUCCESSFUL = 0;

	/**
	 * Configures the IO
	 *
	 * @param   InputInterface   $input   Console Input
	 * @param   OutputInterface  $output  Console Output
	 *
	 * @return void
	 *
	 * @since __DEPLOY_VERSION__
	 *
	 */
	private function configureIO(InputInterface $input, OutputInterface $output): void
	{
		$this->cliInput = $input;
		$this->ioStyle = new SymfonyStyle($input, $output);
	}

	/**
	 * Initialise the command.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function configure(): void
	{
		$this->addOption('eid', null, InputOption::VALUE_REQUIRED, 'The ID of the extension to discover');

		$help = "<info>%command.name%</info> is used to discover extensions
		\nYou can provide the following option to the command:
		\n  --extension: The ID of the extension
		\nUsage:
		\n  <info>php %command.full_name% --extension=<id_of_the_extension></info>";

		$this->setDescription('Discover all extensions or an extension');
		$this->setHelp($help);
	}

	/**
	 * Used for installing extension from a path
	 *
	 * @param   string  $path  Path to the extension zip file
	 *
	 * @return boolean
	 *
	 * @since __DEPLOY_VERSION__
	 *
	 * @throws \Exception
	 */
	public function processDiscover($eid): bool
	{
		$jInstaller = new Installer;

		if ($eid === -1)
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true)
				->select($db->quoteName(['extension_id']))
				->from($db->quoteName('#__extensions'))
				->where($db->quoteName('state') . ' = -1');
			$db->setQuery($query);
			$eidsToDiscover = $db->loadObjectList();

			foreach ($eidsToDiscover as $eidToDiscover)
			{
				if (!$jInstaller->discover_install($eidToDiscover->extension_id))
				{
					return false;
				}

				return true;
			}

			$this->ioStyle->warning('There is no extension to discover.');

			return true;
		}
		else
		{
			return $jInstaller->discover_install($eid);
		}
	}

	/**
	 * Internal function to execute the command.
	 *
	 * @param   InputInterface   $input   The input to inject into the command.
	 * @param   OutputInterface  $output  The output to inject into the command.
	 *
	 * @return  integer  The command exit code
	 *
	 * @throws \Exception
	 * @since   __DEPLOY_VERSION__
	 */
	protected function doExecute(InputInterface $input, OutputInterface $output): int
	{
		$this->configureIO($input, $output);

		if ($eid = $this->cliInput->getOption('eid'))
		{
			$result = $this->processDiscover($eid);

			if (!$result)
			{
				$this->ioStyle->error('Unable to discover extension with ID ' . $eid);

				return self::DISCOVER_FAILED;
			}

			$this->ioStyle->success('Extension with ID ' . $eid . ' discovered successfully.');

			return self::DISCOVER_SUCCESSFUL;
		}
		else
		{
			$result = $this->processDiscover(-1);

			if (!$result)
			{
				$this->ioStyle->error('Unable to discover all extensions');

				return self::DISCOVER_FAILED;
			}

			$this->ioStyle->success('All extensions discovered successfully.');

			return self::DISCOVER_SUCCESSFUL;
		}
	}
}
