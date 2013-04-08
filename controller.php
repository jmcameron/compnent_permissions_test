<?php
/**
 * Attachments component Legacy controller class compatibility
 *
 * @package Attachments
 * @subpackage Attachments_Component
 *
 * @copyright Copyright (C) 2011-2012 Jonathan M. Cameron, All Rights Reserved
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link http://joomlacode.org/gf/project/attachments/frs/
 * @author Jonathan M. Cameron
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

require_once('view.php');

// Make sure we have the legacy controller class
if (!class_exists('JControllerLegacy', false))
{
	if (version_compare(JVERSION, '3.0', 'ge'))
	{
		// Joomla 3.0
		jimport('legacy.controller.legacy');
	}
	else if (version_compare(JVERSION, '2.5', 'ge'))
	{
		// Joomla 2.5
		jimport('joomla.application.component.controller');
		jimport('cms.controller.legacy');
	}
}

class PermtestController extends JControllerLegacy
{
	public function display($cachable = false, $urlparams = false)
	{
		$view = new PermtestView();
		$view->display();
	}


	public function installDefaultRules()
	{
		$app = JFactory::getApplication();

		if ( method_exists('JAccess', 'installComponentDefaultRules') ) {
			JAccess::installComponentDefaultRules('com_permtest');
			$app->enqueueMessage('Installed defaults for custom rules for permtest', 'message');
			}
		else {
			$app->enqueueMessage('Unable to install defaults for custom rules for permtest (JAccess not patched)', 'message');
			}

		$this->execute('display');
		$this->redirect();
	}


	public function purgeDefaultRules()
	{
		$app = JFactory::getApplication();

		if ( method_exists('JAccess', 'installComponentDefaultRules') ) {
			JAccess::purgeComponentDefaultRules('com_permtest');
			$app->enqueueMessage('Purged defaults for custom rules for permtest', 'message');
			}
		else {
			$app->enqueueMessage('Unable to purge defaults for custom rules for permtest (JAccess not patched)', 'message');
			}

		$this->execute('display');
		$this->redirect();
	}
}