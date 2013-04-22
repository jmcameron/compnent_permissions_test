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

		$doc = JFactory::getDocument();
		$doc->addStyleSheet('components/com_permtest/permtest.css');

		echo "<div style=\"font-size: 1.5em\">";
		echo "<h2>Suggested test sequence:</h2>";
		echo '<ol class="test-instructions">';
		echo "  <li>First, check the post-install component permissions ";
		echo "         (Check the table below or use the 'Options' button on the toolbar). ";
		echo       "<br/><i>Notice that the custom permissions for com_permtest are all denied for everyone (except super-user)</i></li>";
		echo "  <li>Now click on the red 'Install Default Permissions' link below.</li>";
		echo "  <li>Check the component permissions again. ";
		echo       "<i>Notice that some of the custom permissions are now allowed (eg, check the Author group).</i></li>";
		echo "  <li>Now click on the red 'Purge Default Permissions' link below. ";
		echo       "<i>The custom permissions should revert to denied for everyone.</i></li>";
		echo "</ol>";
		echo '<h2 class="test-links">';
		echo '<a class="test-link" href="index.php?option=com_permtest&amp;task=installDefaultRules">Install Default Permissions</a>';
		echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		echo '<a class="test-link" href="index.php?option=com_permtest&amp;task=purgeDefaultRules">Purge Default Permissions</a>';
		echo '</h2>';
		echo "<h3>Hover over the non-core actions (first column) to see the actual default specification in the 'access.xml' file.";

		echo PermtestView::showPermissions();
	}

	public static function showPermissions()
	{
		$groups = Array( 1 => 'Public',
						 6 => 'Manager',
						 7 => 'Administrator',
						 2 => 'Registered',
						 3 => 'Author',
						 4 => 'Editor',
						 5 => 'Publisher',
						 8 => 'Super Users',
						 );

		$defaults = Array('core.admin' => '',
						  'core.manage' => '',
						  'core.create' => '',
						  'core.delete' => '',
						  'core.edit' => '',
						  'core.edit.own' => '',
						  'core.edit.state' => '',
						  'permtest.default' => 'No default give: Defaults to system default permissions (denied)',
						  'permtest.custom.author1' => 'com_content:core.create',
						  'permtest.custom.author2' => 'com_content:core.create[Author]',
						  'permtest.custom.editor1' => 'com_content:core.edit',
						  'permtest.custom.editor2' => 'com_content:core.edit[Editor]',
						  'permtest.custom.manager1' => 'com_content:core.edit.state',
						  'permtest.custom.manager2' => 'com_content:core.edit.state[Manager]',
						  'permtest.custom.publisher1' => 'com_content:core.edit.state',
						  'permtest.custom.publisher2' => 'com_content:core.edit.state[Publisher]',
						  'permtest.custom.manager1' => 'com_content:core.delete',
						  'permtest.custom.manager2' => 'com_content:core.delete[Manager]',
						  'permtest.custom.authmanage' => 'com_content:core.create[Author],com_content:core.delete[Manager]',
						  'permtest.custom.administrator1' => 'com_content:core.admin',
						  'permtest.custom.administrator2' => 'com_content:core.admin[Administrator]'
						  );

		$actions = array_keys($defaults);

		// $actons = array_keys($defaults);
		$html = "<h2>Default permissions for component 'com_permtest'</h2>\n";
		$html .= "<table id=\"permtest-aclgrid\">";
		$html .= "\n<thead><tr><th>Action</th>";
		foreach ($groups as $id => $name)
		{
			$html .= '<th>' . $name . '</th>';
		}
		$html .= '</tr><thead>';

		$html .= "\n<tbody>";
		foreach ($actions as $action)
		{
			$default = $defaults[$action];
			$html .= "\n<tr><td title=\"$default\">" . $action . '</td>';
			foreach ($groups as $id => $name)
			{
				$ok = JAccess::checkGroup($id, $action) ? 'OK' : 'Denied';
				$html .= '<td>'. $ok . '</td>';
			}
			$html .= '</tr>';
		}

		$html .= '</tbody></table>';
		return $html;
	}

}