<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2025-11-27 08:19:52 --> Query error: Unknown column 'assigment_due' in 'where clause' - Invalid query: SELECT * FROM `list_assigment` WHERE `assignment_name` = 'asdfghj' AND `assigment_due` = '2025-11-26' LIMIT 1;
ERROR - 2025-11-27 08:20:54 --> Query error: Table 'gonz_database.list_assignment' doesn't exist - Invalid query: SELECT * FROM `list_assignment` WHERE `assignment_name` = 'asdfghj' AND `assigment_due` = '2025-11-26' LIMIT 1;
ERROR - 2025-11-27 08:22:46 --> Query error: Table 'gonz_database.list_assignment' doesn't exist - Invalid query: SELECT * FROM `list_assignment` WHERE `assignment_name` = 'asdfghj' AND `assigment_due` = '2025-11-26' LIMIT 1;
ERROR - 2025-11-27 08:33:34 --> Query error: Unknown column 'assigment_due' in 'field list' - Invalid query: INSERT INTO `list_assigment` (`assignment_name`, `assigment_due`)
            VALUES ('asdfghj', '2025-11-26')
ERROR - 2025-11-27 08:34:13 --> Query error: Cannot add or update a child row: a foreign key constraint fails (`gonz_database`.`list_assigment`, CONSTRAINT `list_assigment_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `list_class` (`class_id`) ON UPDATE CASCADE) - Invalid query: INSERT INTO `list_assigment` (`assignment_name`, `assignment_due`)
            VALUES ('asdfghj', '2025-11-26')
ERROR - 2025-11-27 08:40:25 --> Severity: Warning --> Undefined property: stdClass::$assignment_name C:\xampp\htdocs\web_techno\application\models\Students_Model.php 190
ERROR - 2025-11-27 08:40:25 --> Severity: Warning --> Undefined property: stdClass::$assignment_due C:\xampp\htdocs\web_techno\application\models\Students_Model.php 191
ERROR - 2025-11-27 09:06:55 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near '* TO `list_assigment` WHERE `assignment_name` = 'marve ltugas' AND `assignmen...' at line 1 - Invalid query: INSERT * TO `list_assigment` WHERE `assignment_name` = 'marve ltugas' AND `assignment_due` = '2025-11-05' LIMIT 1;
