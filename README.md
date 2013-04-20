compnent_permissions_test
=========================

Joomla component for testing specifying defaults for component-defined
permissions

This test component should work on Joomla 2.5.x and 3.x with the necessary
patches to Joomla.

To use this, install the component in the regular way then go to the
'component-permissions-test' component under the 'Components' menu in the back
end.  There are instructions there on how to test it.

The following section documents why the patch was needed and the functionality
that it provides.


Default Component ACL Rules
===========================

Joomla Tracker: http://joomlacode.org/gf/project/joomla/tracker/?action=TrackerItemEdit&tracker_item_id=30550 (submitted for both Joomla 2.5 and 3.x)

Background / Motivation
-----------------------

There is currently no way to specify the default action permission rules for a
component that has component-specific custom permission rules.  If component
developers want default ACL rules for their custom ACL rules, they need to
create code that modifies the root asset.  This is extra work for component
developers and a potential source of errors and problems.

Proposed Fix
------------

The proposed fix addresses this problem by extending the syntax of component
'access.xml' file that lists the component-specific ACL rules.  The goal is to
allow the component developer to specify the groups that should be allowed to
perform the ACL action as part of the XML definition for each ACL rule.  This
would require updates to Joomla (the platform) to interpret the additions to
the 'access.xml' and add the appropriate permissions to the system.  

The proposed function provides new functions to install or remove the default
permissions specified in the 'access.xml' file.  Normally this would be done
in by the installer by having the component developer call the new function
during component installation.  In the following description, assume the new
function is called by the installer.

These additions will be backwards compatible since the additional XML
attributes will be ignored by versions of Joomla that have not implemented
this fix.

Since there are many different ways that a particular component can implement
ACL rules, a few guiding principles are necessary:

  1. If the default for a custom ACL rule is not specified, the default root
     ACL rules will not be modified.  This is the current behavior.

  2. If the group with the specified permssions in the default rules does not
     exist in the Joomla installation, no root permission will be set and an
     information message will be generated.  Some Joomla installations have
     been known to delete all common groups (such as Public, Registered) and
     create new groups. So there is no guarantee that normally expected groups
     will exist.

  3. The component developer will specify a component (usually com_content)
     and an action to check and the installer will find a group with that
     permission.  The component developer may give a suggestion for which
     group to check first.  If the group is found and has the required
     permissions, then it will be used.  Otherwise the installer will search
     for a suitable group with the required permissions.  All groups will be
     checked and the one with the least authority will be used (eg, if both
     Author and Publisher have the required permission, Author will be chosen
     since Publisher is more authoritative).

  4. This new feature would only be applied to any custom component-specific
     rule in a components 'access.xml'.  This approach cannot be used to
     modify the core rules in the root rule.


Example 'access.xml' syntax additions
-------------------------------------

The following syntax change is proposed.  In the following examples, the
component for the 'access.xml' file is 'com_example':

```xml
<action name="example.delete.own"
        title="COM_EXAMPLE_PERMISSION_DELETEOWN"
        description="COM_EXAMPLE_PERMISSION_DELETEOWN_DESC" 
        default="com_content:core.delete" 
        />
```

Notice the new 'default' attribute at the end.  The groups will be searched to
find the least authoritative group that has the 'core.delete' permission for
com_content (for instance, delete example items they created).  This will mean
that all derived groups also have this permission.

Note that this could be a comma-separated list of group names.

The component developer could also specify a hint at which group to check
first: 

```xml
<action name="example.delete.own"
        title="COM_EXAMPLE_PERMISSION_DELETEOWN"
        description="COM_EXAMPLE_PERMISSION_DELETEOWN_DESC" 
        default="com_content:core.delete[Author]" 
        />
```

In this case, the installer would first check to see if the 'Author' group has
the required permission.  If it does, it would be used.  If not, a warning
message would be given and the search would continue.

Note that the test component could be something other than 'com_content':

```xml
<action name="example.delete.own"
        title="COM_EXAMPLE_PERMISSION_DELETEOWN"
        description="COM_EXAMPLE_PERMISSION_DELETEOWN_DESC" 
        default="com_xyz123:core.delete" 
        />
```

Which would find a group that has permission to do 'core.delete' for the
'com_xyz123' component.


Testing the new functionality
-----------------------------

A test component 'com_permtest' is available on github:

   https://github.com/jmcameron/compnent_permissions_test

This includes a zipped up version of the component that is ready to install.

   comp_permtest.zip

This test component should work on Joomla 2.5.x and 3.x.  Obviously the
functions will not do anything if the fixes to Joomla described here are not
implemented.

To try these fixes:

   * Install the fixes on your Joomla 2.5.7+ or 3.x site (by doing 'git pull'
     or using a patch file)

   * Install the component in the regular way then go to the
     'component-permissions-test' component under the 'Components' menu in the
     back end.

   * Read the  instructions there on how to test it.

Note that these fixes include unit tests for the new capabilities.  See the
bottom of the this document.


Implementation Notes
--------------------

The functionality above is implemented by adding functions to JAccess and
JRules:


**JRules::removeActions($action_regexp)**

 * Remove all actions from the rule whose action name matches the regular
   expression.  This a general function and could be used to remove core
   actions from the root rule, if the calling code requested it (not
   recommended).


**JAccess::getGroupId($groupname)**

 * Returns a group ID for the given group name.

 * This could be moved elsewhere, but there did not seem to be a suitable
   place.  We may want to add a JUserGroup class (in
   libraries/joomla/user/group.php) and move this function into that class.


**JAccess::installComponentDefaultRules($component, $file = null)**

 * Parse the rules for custom rules in component's 'access.xml' file and
   install new permissions in the root asset, as needed.

 * Does not allow modifying core rules.

 * If a group with the required permission is not found or the group name
   specified in the hint part is unknown by the Joomla installation, the
   default specified will be ignored.  Currently a log message is being added.
   This should probably be a JApplication::enqueueMessage() call but I could
   not get that to work in the unit tests.

 * This function can be called any time, but it would make sense to call this
   during component installation in the postflight section.

 * The optional $file can be used to specify an alternate location for the
   'acess.xml' file (for testing).


**JAccess::purgeComponentDefaultRules($component)**

 * Removes all custom rules for $component defined in the root asset.

 * Core rules will not be modified.

 * This function can be called at any time, but it would make sense to call
   this during the uninstallation of a component (during preflight or the
   uninstall function).


Unit tests
----------

 * Unit tests for Joomla 3 that cover the features above have been added to
   the *tests/unit/suites/libraries/access* folder.

 * The custom unit test configuration file
   **tests/system/servers/configdef.php** was added to the toplevel .gitignore
   since it could contain private data and should be ignored (like .gitignore
   already ignores the top-level phpunit config file **phpunit.xml**).
