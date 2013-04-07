<?php

// No direct access.
defined('_JEXEC') or die('Restricted access');

if (!class_exists('JViewLegacy', false))
{
	if (version_compare(JVERSION, '3.0', 'ge'))
	{
		// Joomla 3.0
		jimport('legacy.view.legacy');
	}
	else if (version_compare(JVERSION, '2.5', 'ge'))
	{
		// Joomla 2.5
		jimport('joomla.access.access');
		jimport('joomla.application.component.view');
		jimport('cms.view.legacy');
	}
}

class PermtestView extends JViewLegacy
{
	public function display($cachable = false, $urlparams = false)
	{
		JToolBarHelper::title('Permissions test');
		JToolBarHelper::preferences('com_permtest');

		echo '<h2 style="padding-left: 30px"><a style="color: #F00" href="index.php?option=com_permtest&amp;task=installDefaultRules">Install Default Permissions</a></h2>';
	}

}