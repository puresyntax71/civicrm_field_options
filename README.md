INTRODUCTION
------------

##### CiviCRM Field Options

This module Provides CiviCRM Option Group options in Drupal Node. In case you 
want to use the CiviCRM Fields option to be used in Drupal node, so instead of 
creating the same option manually, use the same field option through this module.
 
This module does not sync the saved data on the drupal node to civicrm. It 
just provides civicrm field options to drupal fields.

INSTALLATION
------------

 * Install as you would normally install a contributed Drupal module. Visit
   https://www.drupal.org/docs/8/extending-drupal-8/installing-drupal-8-modules
   for further information.
 - with drush
 ```drush pm-enable -y civicrm_field_options```

REQUIREMENTS
------------

This module require CiviCRM Module.

## Requirements

* PHP v7.0+
* Drupal 8 || 9
* CiviCRM


## Usage

![Screenshot](/images/screenshot.gif)

### Add a CiviCRM field to a content type
1. Navigate to the Content types page (Administer > Structure > Content types).
2. In the table, locate the row that contains your content type and click the 
manage fields link.
3. In the Add new field section, select one of the following types. 
    * CiviCRM Field Options
4. Enter a label, machine name for the field.
5. Click Save.

#### Choose the Option Group

6. Select `Field settings` Tab.
7. Select option from `CiviCRM Option Groups` Field.
8. Click Save, form get reloaded. Now you will see all option from selected 
option group under `User Selection` Field.
    > Choose your options you want to configure for this field, No selection 
    mean it display all options
9. Select `Field Type` , currently Radio and Select options are available.
10. Click Save.
