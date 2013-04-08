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

		echo "<div style=\"font-size: 1.5em\">";
		echo "<h2>Suggested test sequence:</h2>";
		echo "<ol>";
		echo "  <li><p>After installing this component, check the component permissions (Use the 'Options' button on the toolbar).</p>";
		echo       "<p><i>Notice that the custom permissions are all denied for everyone (except super-user)</i><p></li>";
		echo "  <li><p>Now click on the red 'Install Default Permissions' link below.</p></li>";
		echo "  <li><p>Check the component permissions again. </p>";
		echo       "<p><i>Notice that some of the custom permissions are now allowed (eg, check the Author group).</i></p></li>";
		echo "  <li><p>Now click on the red 'Purge Default Permissions' link below. </p>";
		echo       "<p><i>The custom permissions should revert to the denied for everyone.</i></p></li>";
		echo "</ol>";

		echo '<h2 style="padding-left: 30px"><a style="color: #F00" href="index.php?option=com_permtest&amp;task=installDefaultRules">Install Default Permissions</a></h2>';

		echo '<h2 style="padding-left: 30px"><a style="color: #F00" href="index.php?option=com_permtest&amp;task=purgeDefaultRules">Purge Default Permissions</a></h2>';

	}

}