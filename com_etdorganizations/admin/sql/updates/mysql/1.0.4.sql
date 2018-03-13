ALTER TABLE `#__etdorganizations_organization_contacts` DROP COLUMN `id`;
ALTER TABLE `#__etdorganizations_organization_contacts` ADD PRIMARY KEY (`organization_id`, `contact_id`);
ALTER TABLE `#__etdorganizations_organization_contacts` DROP KEY `idx_organization_contact`;
ALTER TABLE `#__etdorganizations_organizations` DROP COLUMN `id`;
ALTER TABLE `#__etdorganizations_organizations` ADD COLUMN `id` INT(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT FIRST;