#Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [1.1.1] - 2018-03-16
### Changed
- Bug fix: The contacts of an organization were not always saved.
- Bug fix: The modal allowing to edit a contact in the organization edit form was not always properly displayed.
- Bug fix: The contacts thumbnails were not always properly displayed.
- Bug fix: The images thumbnails of the organizations were not correctly generated.

## [1.1.0] - 2018-03-16
### Added
- The category of an organization is displayed in the organizations view of the administrator.
- The information of the contacts of the organization are retrieved for the frontend views.
- Config fields have been added to choose the order of the organizations for the frontend category view.
- The logo and the background image of an organization are now automatically resized using to config fields.

### Removed
- The renaming of the logo and the background image when there is a file with the same name, has been removed.

## [1.0.4] - 2018-03-13
### Added
- A contact field selector has been added in the admin organization form. It allows to associate zero or more contacts to the organization.
- The file *CHANGELOG.md* has been created.
- Category field selector added to the menu item of the view category.
- SQL updates files have been created.
- Comments added.

### Changed
- The file *language/fr-FR/fr-FR.pkg_etdorganizations.sys.ini* has been renamed.
- Translations of the com_etdorganizations component have been updated.
- Auto-increment added to the `#__etdorganizations_organizations.id` database field.
- Bug fixes.

### Removed
- The file *com_etdorganizations/admin/etdorganizations.xml* has been removed.
- Database field `#__etdorganizations_organization_contacts.id` removed.