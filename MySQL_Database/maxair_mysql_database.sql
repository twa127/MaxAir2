-- ------------------------------------------------------------------------
--     __  __                             _
--    |  \/  |                    /\     (_)
--    | \  / |   __ _  __  __    /  \     _   _ __
--    | |\/| |  / _` | \ \/ /   / /\ \   | | |  __|
--    | |  | | | (_| |  >  <   / ____ \  | | | |
--    |_|  |_|  \__,_| /_/\_\ /_/    \_\ |_| |_|
--
--          S M A R T   T H E R M O S T A T
--
-- *************************************************************************
-- * MaxAir is a Linux based Central Heating Control systems. It runs from *
-- * a web interface and it comes with ABSOLUTELY NO WARRANTY, to the      *
-- * extent permitted by applicable law. I take no responsibility for any  *
-- * loss or damage to you or your property.                               *
-- * DO NOT MAKE ANY CHANGES TO YOUR HEATING SYSTEM UNTILL UNLESS YOU KNOW *
-- * WHAT YOU ARE DOING                                                    *
-- *************************************************************************
-- --------------------------------------------------------
-- Host:                         192.168.99.11
-- Server version:               10.3.15-MariaDB-1 - Raspbian testing-staging
-- Server OS:                    debian-linux-gnueabihf
-- HeidiSQL Version:             10.2.0.5599
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table maxair.node_id
DROP TABLE IF EXISTS `node_id`;
CREATE TABLE IF NOT EXISTS `node_id` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync` tinyint(4) DEFAULT NULL,
  `purge` tinyint(4) DEFAULT NULL,
  `node_id` int(11) DEFAULT NULL,
  `sent` tinyint(4) DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4;

-- Dumping structure for table maxair.add_on_logs
DROP TABLE IF EXISTS `add_on_logs`;
CREATE TABLE IF NOT EXISTS `add_on_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync` tinyint(4) NOT NULL,
  `purge` tinyint(4) NOT NULL COMMENT 'Mark For Deletion',
  `zone_id` int(11),
  `start_datetime` timestamp NULL,
  `start_cause` char(50) COLLATE utf16_bin,
  `stop_datetime` timestamp NULL,
  `stop_cause` char(50) COLLATE utf16_bin,
  `expected_end_date_time` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

-- Dumping structure for table maxair.away
DROP TABLE IF EXISTS `away`;
CREATE TABLE IF NOT EXISTS `away` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync` tinyint(4) NOT NULL,
  `purge` tinyint(4) NOT NULL COMMENT 'Mark For Deletion',
  `status` tinyint(4),
  `start_datetime` timestamp NULL ON UPDATE current_timestamp(),
  `end_datetime` timestamp NULL,
  `away_button_id` int(11),
  `away_button_child_id` int(11),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

-- Dumping data for table maxair.away: 1 rows
/*!40000 ALTER TABLE `away` DISABLE KEYS */;
/*!40000 ALTER TABLE `away` ENABLE KEYS */;

-- Dumping structure for table maxair.system_controller
DROP TABLE IF EXISTS `system_controller`;
CREATE TABLE `system_controller` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync` tinyint(4) NOT NULL,
  `purge` tinyint(4) NOT NULL COMMENT 'Mark For Deletion',
  `mode` tinyint(1) NOT NULL,
  `status` tinyint(4),
  `active_status` tinyint(4),
  `name` char(50) CHARACTER SET utf16 COLLATE utf16_bin,
  `node_id` int(11),
  `hysteresis_time` tinyint(4),
  `max_operation_time` tinyint(4),
  `overrun` smallint(6),
  `datetime` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `sc_mode` tinyint(4),
  `sc_mode_prev` tinyint(4),
  `heat_relay_id` int(11),
  `cool_relay_id` int(11),
  `fan_relay_id` int(11),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Dumping data for table maxair.system_controller: ~0 rows (approximately)
/*!40000 ALTER TABLE `system_controller` DISABLE KEYS */;
/*!40000 ALTER TABLE `system_controller` ENABLE KEYS */;

-- Dumping structure for table maxair.controller_zone_logs
DROP TABLE IF EXISTS `controller_zone_logs`;
CREATE TABLE `controller_zone_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync` tinyint(4) NOT NULL,
  `purge` tinyint(4) NOT NULL COMMENT 'Mark For Deletion',
  `zone_id` int(11) NOT NULL,
  `start_datetime` timestamp NULL DEFAULT NULL,
  `start_cause` char(50) COLLATE utf16_bin DEFAULT NULL,
  `stop_datetime` timestamp NULL DEFAULT NULL,
  `stop_cause` char(50) COLLATE utf16_bin DEFAULT NULL,
  `expected_end_date_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=133 DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

-- Dumping data for table maxair.controller_zone_logs: ~0 rows (approximately)
/*!40000 ALTER TABLE `controller_zone_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `controller_zone_logs` ENABLE KEYS */;

-- Dumping structure for table maxair.boost
DROP TABLE IF EXISTS `boost`;
CREATE TABLE IF NOT EXISTS `boost` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync` tinyint(4) NOT NULL,
  `purge` tinyint(4) NOT NULL COMMENT 'Mark For Deletion',
  `status` tinyint(4),
  `zone_id` int(11),
  `time` timestamp NOT NULL ON UPDATE current_timestamp(),
  `temperature` tinyint(4),
  `minute` tinyint(4),
  `boost_button_id` int(11),
  `boost_button_child_id` int(11),
  `hvac_mode` tinyint(4),
  PRIMARY KEY (`id`),
  KEY `FK_boost_zone` (`zone_id`),
  CONSTRAINT `FK_boost_zone` FOREIGN KEY (`zone_id`) REFERENCES `zone` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

-- Table structure for table `relays`
DROP TABLE IF EXISTS `relays`;
CREATE TABLE `relays` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync` tinyint(4) NOT NULL,
  `purge` tinyint(4) NOT NULL COMMENT 'Mark For Deletion',
  `relay_id` int(11) DEFAULT NULL,
  `relay_child_id` int(11) DEFAULT NULL,
  `name` char(50) COLLATE utf8_bin DEFAULT NULL,
  `type` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_relays_nodes` (`relay_id`),
  CONSTRAINT `FK_relays_nodes` FOREIGN KEY (`relay_id`) REFERENCES `nodes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Dumping data for table `relays`
/*!40000 ALTER TABLE `relays` DISABLE KEYS */;
/*!40000 ALTER TABLE `relays` ENABLE KEYS */;

-- Dumping structure for table maxair.email
DROP TABLE IF EXISTS `email`;
CREATE TABLE IF NOT EXISTS `email` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync` tinyint(4) NOT NULL,
  `purge` tinyint(4) NOT NULL COMMENT 'Mark For Deletion',
  `smtp` char(50) COLLATE utf16_bin,
  `username` char(50) COLLATE utf16_bin,
  `password` char(50) COLLATE utf16_bin,
  `from` char(50) COLLATE utf16_bin,
  `to` char(50) COLLATE utf16_bin,
  `status` tinyint(4),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

-- Dumping data for table maxair.email: 0 rows
/*!40000 ALTER TABLE `email` DISABLE KEYS */;
/*!40000 ALTER TABLE `email` ENABLE KEYS */;

-- Dumping structure for table maxair.gateway
DROP TABLE IF EXISTS `gateway`;
CREATE TABLE IF NOT EXISTS `gateway` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` tinyint(4) NOT NULL,
  `sync` tinyint(4) NOT NULL,
  `purge` tinyint(4) NOT NULL COMMENT 'Mark For Deletion',
  `type` char(50) COLLATE utf16_bin NOT NULL COMMENT 'serial or wifi',
  `location` char(50) COLLATE utf16_bin NOT NULL COMMENT 'ip address or serial port location i.e. /dev/ttyAMA0',
  `port` char(50) COLLATE utf16_bin NOT NULL COMMENT 'port number 5003 or baud rate115200 for serial gateway',
  `timout` char(50) COLLATE utf16_bin NOT NULL,
  `pid` char(50) COLLATE utf16_bin,
  `pid_running_since` char(50) COLLATE utf16_bin,
  `reboot` tinyint(4),
  `find_gw` tinyint(4),
  `version` char(50) COLLATE utf16_bin,
  `enable_outgoing` tinyint(4),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

-- Dumping data for table maxair.gateway: ~0 rows (approximately)
/*!40000 ALTER TABLE `gateway` DISABLE KEYS */;
/*!40000 ALTER TABLE `gateway` ENABLE KEYS */;

-- Dumping structure for table maxair.gateway_logs
DROP TABLE IF EXISTS `gateway_logs`;
CREATE TABLE IF NOT EXISTS `gateway_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync` tinyint(4) NOT NULL,
  `purge` tinyint(4) NOT NULL COMMENT 'Mark For Deletion',
  `type` char(50) COLLATE utf16_bin COMMENT 'serial or wifi',
  `location` char(50) COLLATE utf16_bin COMMENT 'ip address or serial port location i.e. /dev/ttyAMA0',
  `port` char(50) COLLATE utf16_bin COMMENT 'port number or baud rate for serial gateway',
  `pid` char(50) COLLATE utf16_bin,
  `pid_start_time` char(50) COLLATE utf16_bin,
  `pid_datetime` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

-- Dumping data for table maxair.gateway_logs: ~0 rows (approximately)
/*!40000 ALTER TABLE `gateway_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `gateway_logs` ENABLE KEYS */;

-- Dumping structure for table maxair.holidays
DROP TABLE IF EXISTS `holidays`;
CREATE TABLE IF NOT EXISTS `holidays` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync` tinyint(4) NOT NULL,
  `purge` tinyint(4) NOT NULL COMMENT 'Mark For Deletion',
  `status` tinyint(4),
  `start_date_time` datetime,
  `end_date_time` datetime,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

-- Dumping data for table maxair.holidays: ~0 rows (approximately)
/*!40000 ALTER TABLE `holidays` DISABLE KEYS */;
/*!40000 ALTER TABLE `holidays` ENABLE KEYS */;

-- Dumping structure for table maxair.http_messages
DROP TABLE IF EXISTS `http_messages`;
CREATE TABLE IF NOT EXISTS `http_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync` tinyint(4) NOT NULL,
  `purge` tinyint(4) NOT NULL COMMENT 'Mark For Deletion',
  `zone_name` char(50) COLLATE utf16_bin,
  `node_id` char(50) COLLATE utf16_bin,
  `message_type` char(50) COLLATE utf16_bin,
  `command` char(50) COLLATE utf16_bin,
  `parameter` char(50) COLLATE utf16_bin,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

-- Dumping structure for table maxair.jobs
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_name` char(50) NOT NULL,
  `script` char(100) NOT NULL,
  `enabled` tinyint(1),
  `log_it` tinyint(1),
  `time` char(50) NOT NULL,
  `output` text NOT NULL,
  `datetime` timestamp NOT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- Dumping structure for table maxair.livetemp
DROP TABLE IF EXISTS `livetemp`;
CREATE TABLE IF NOT EXISTS `livetemp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync` tinyint(4) NOT NULL,
  `purge` tinyint(4) NOT NULL COMMENT 'Mark For Deletion',
  `status` tinyint(4),
  `zone_id` int(11),
  `active` tinyint(1),
  `temperature` decimal(4,1),
  `hvac_mode` tinyint(4),
  PRIMARY KEY (`id`),
  KEY `FK_livetemp_zone` (`zone_id`),
  CONSTRAINT `FK_livetemp_zone` FOREIGN KEY (`zone_id`) REFERENCES `zone` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

-- Dumping structure for table maxair.messages_in
DROP TABLE IF EXISTS `messages_in`;
CREATE TABLE IF NOT EXISTS `messages_in` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync` tinyint(4) NOT NULL,
  `purge` tinyint(4) NOT NULL COMMENT 'Mark For Deletion',
  `node_id` char(15) COLLATE utf16_bin,
  `child_id` tinyint(4),
  `sub_type` int(11),
  `payload` decimal(10,2),
  `datetime` timestamp NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

-- Dumping data for table maxair.messages_in: ~1 rows (approximately)
/*!40000 ALTER TABLE `messages_in` DISABLE KEYS */;
/*!40000 ALTER TABLE `messages_in` ENABLE KEYS */;

-- Dumping structure for table maxair.messages_out
DROP TABLE IF EXISTS `messages_out`;
CREATE TABLE IF NOT EXISTS `messages_out` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync` tinyint(4) NOT NULL,
  `purge` tinyint(4) NOT NULL COMMENT 'Mark For Deletion',
  `node_id` char(50) COLLATE utf32_bin NOT NULL COMMENT 'Node ID',
  `child_id` int(11) NOT NULL COMMENT 'Child Sensor',
  `sub_type` int(11) NOT NULL COMMENT 'Command Type',
  `ack` int(11) NOT NULL COMMENT 'Ack Req/Resp',
  `type` int(11) NOT NULL COMMENT 'Type',
  `payload` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT 'Payload',
  `sent` tinyint(1) NOT NULL COMMENT 'Sent Status 0 No - 1 Yes',
  `datetime` timestamp NOT NULL ON UPDATE current_timestamp() COMMENT 'Current datetime',
  `zone_id` int(11) NOT NULL COMMENT 'Zone ID related to this entery',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf32 COLLATE=utf32_bin;

-- Dumping data for table maxair.messages_out: ~9 rows (approximately)
/*!40000 ALTER TABLE `messages_out` DISABLE KEYS */;
/*!40000 ALTER TABLE `messages_out` ENABLE KEYS */;

-- Dumping structure for table maxair.mqtt
DROP TABLE IF EXISTS `mqtt`;
CREATE TABLE `mqtt` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
  	`name` varchar(50) COLLATE utf16_bin NOT NULL,
  	`ip` varchar(39) COLLATE utf16_bin NOT NULL,
  	`port` int(11) NOT NULL,
  	`username` varchar(50) COLLATE utf16_bin NOT NULL,
  	`password` varchar(50) COLLATE utf16_bin NOT NULL,
  	`enabled` tinyint(4) NOT NULL,
	`type` INT(11) NOT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='utf16_bin'
ENGINE=InnoDB;

-- Dumping data for table maxair.mqtt: ~0 rows (approximately)
/*!40000 ALTER TABLE `mqtt` DISABLE KEYS */;
/*!40000 ALTER TABLE `mqtt` ENABLE KEYS */;

-- Dumping structure for table maxair.network_settings
DROP TABLE IF EXISTS `network_settings`;
CREATE TABLE IF NOT EXISTS `network_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync` tinyint(4) NOT NULL,
  `purge` tinyint(4) NOT NULL COMMENT 'Mark For Deletion',
  `primary_interface` tinyint(4),
  `ap_mode` tinyint(1),
  `interface_num` tinyint(4),
  `interface_type` char(50) COLLATE utf16_bin,
  `mac_address` char(50) COLLATE utf16_bin,
  `hostname` char(50) COLLATE utf16_bin,
  `ip_address` char(50) COLLATE utf16_bin,
  `gateway_address` char(50) COLLATE utf16_bin,
  `net_mask` char(50) COLLATE utf16_bin,
  `dns1_address` char(50) COLLATE utf16_bin,
  `dns2_address` char(50) COLLATE utf16_bin,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

-- Dumping data for table maxair.network_settings: ~0 rows (approximately)
/*!40000 ALTER TABLE `network_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `network_settings` ENABLE KEYS */;

-- Dumping structure for table maxair.nodes
DROP TABLE IF EXISTS `nodes`;
CREATE TABLE IF NOT EXISTS `nodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync` tinyint(4) NOT NULL,
  `purge` tinyint(4) NOT NULL COMMENT 'Mark For Deletion',
  `type` CHAR(50) NOT NULL COLLATE 'utf8_bin',
  `node_id` char(50) COLLATE utf16_bin NOT NULL,
  `max_child_id` int(11) NOT NULL,
  `name` char(50) CHARACTER SET utf8 COLLATE utf8_bin,
  `last_seen` timestamp NULL ON UPDATE current_timestamp(),
  `notice_interval` int(11) NOT NULL,
  `min_value` int(11),
  `status` char(50) CHARACTER SET utf8 COLLATE utf8_bin,
  `ms_version` char(50) COLLATE utf16_bin,
  `sketch_version` char(50) COLLATE utf16_bin,
  `repeater` tinyint(4) COMMENT 'Repeater Feature Enabled=1 or Disable=0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

-- Dumping data for table maxair.nodes: ~7 rows (approximately)
/*!40000 ALTER TABLE `nodes` DISABLE KEYS */;
/*!40000 ALTER TABLE `nodes` ENABLE KEYS */;

-- Dumping structure for table maxair.nodes_battery
DROP TABLE IF EXISTS `nodes_battery`;
CREATE TABLE IF NOT EXISTS `nodes_battery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync` tinyint(4) NOT NULL,
  `purge` tinyint(4) NOT NULL COMMENT 'Mark For Deletion',
  `node_id` int(11),
  `bat_voltage` decimal(10,2),
  `bat_level` decimal(10,2),
  `update` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

-- Dumping data for table maxair.nodes_battery: ~0 rows (approximately)
/*!40000 ALTER TABLE `nodes_battery` DISABLE KEYS */;
/*!40000 ALTER TABLE `nodes_battery` ENABLE KEYS */;

-- Dumping structure for table maxair.notice
DROP TABLE IF EXISTS `notice`;
CREATE TABLE IF NOT EXISTS `notice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync` tinyint(4) NOT NULL,
  `purge` tinyint(4) NOT NULL,
  `datetime` timestamp NULL,
  `message` varchar(200) COLLATE utf16_bin,
  `status` tinyint(4),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=181 DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

-- Dumping data for table maxair.notice: ~0 rows (approximately)
/*!40000 ALTER TABLE `notice` DISABLE KEYS */;
/*!40000 ALTER TABLE `notice` ENABLE KEYS */;

-- Dumping structure for table maxair.override
DROP TABLE IF EXISTS `override`;
CREATE TABLE IF NOT EXISTS `override` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync` tinyint(4) NOT NULL,
  `purge` tinyint(4) NOT NULL COMMENT 'Mark For Deletion',
  `status` tinyint(4),
  `zone_id` int(11),
  `time` timestamp NULL ON UPDATE current_timestamp(),
  `temperature` tinyint(4),
  `hvac_mode` tinyint(4),
  PRIMARY KEY (`id`),
  KEY `FK_override_zone` (`zone_id`),
  CONSTRAINT `FK_override_zone` FOREIGN KEY (`zone_id`) REFERENCES `zone` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

-- Dumping data for table maxair.override: ~3 rows (approximately)
/*!40000 ALTER TABLE `override` DISABLE KEYS */;
/*!40000 ALTER TABLE `override` ENABLE KEYS */;

-- Dumping structure for table maxair.piconnect
DROP TABLE IF EXISTS `piconnect`;
CREATE TABLE `piconnect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `sync` tinyint(4) NOT NULL DEFAULT 0,
  `protocol` varchar(50) COLLATE utf16_bin DEFAULT NULL,
  `url` varchar(50) COLLATE utf16_bin DEFAULT NULL,
  `script` char(50) COLLATE utf16_bin DEFAULT NULL,
  `api_key` varchar(200) COLLATE utf16_bin DEFAULT NULL,
  `version` char(50) COLLATE utf16_bin DEFAULT NULL,
  `build` char(50) COLLATE utf16_bin DEFAULT NULL,
  `connect_datetime` datetime DEFAULT NULL,
  `delay` int(11) DEFAULT NULL,
  `away` bit(1) DEFAULT NULL,
  `boiler` bit(1) DEFAULT NULL,
  `boiler_logs` bit(1) DEFAULT NULL,
  `boost` bit(1) DEFAULT NULL,
  `email` bit(1) DEFAULT NULL,
  `frost_protection` bit(1) DEFAULT NULL,
  `gateway` bit(1) DEFAULT NULL,
  `gateway_log` bit(1) DEFAULT NULL,
  `holidays` bit(1) DEFAULT NULL,
  `messages_in` bit(1) DEFAULT NULL,
  `messages_out` bit(1) DEFAULT NULL,
  `mqtt` bit(1) DEFAULT NULL,
  `nodes` bit(1) DEFAULT NULL,
  `nodes_battery` bit(1) DEFAULT NULL,
  `notice` bit(1) DEFAULT NULL,
  `override` bit(1) DEFAULT NULL,
  `piconnect_logs` bit(1) DEFAULT NULL,
  `schedule` bit(1) DEFAULT NULL,
  `system` bit(1) DEFAULT NULL,
  `weather` bit(1) DEFAULT NULL,
  `zone` bit(1) DEFAULT NULL,
  `zone_logs` bit(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

-- Dumping data for table maxair.piconnect: ~0 rows (approximately)
/*!40000 ALTER TABLE `piconnect` DISABLE KEYS */;
/*!40000 ALTER TABLE `piconnect` ENABLE KEYS */;

-- Dumping structure for table maxair.piconnect_logs
DROP TABLE IF EXISTS `piconnect_logs`;
CREATE TABLE IF NOT EXISTS `piconnect_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync` int(11) DEFAULT NULL,
  `picurl` char(200) CHARACTER SET utf8mb4 DEFAULT NULL,
  `content_type` char(200) CHARACTER SET utf8mb4 DEFAULT NULL,
  `http_code` char(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `header_size` char(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `request_size` char(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `filetime` char(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `ssl_verify_result` char(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `redirect_count` char(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `total_time` char(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `connect_time` char(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `pretransfer_time` char(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `size_upload` char(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `size_download` char(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `speed_download` char(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `speed_upload` char(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `download_content_length` char(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `upload_content_length` char(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `starttransfer_time` char(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `primary_port` char(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `local_port` char(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `start_time` char(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `end_time` char(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `n_tables` char(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `records` char(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


-- Dumping structure for table maxair.schedule_daily_time
DROP TABLE IF EXISTS `schedule_daily_time`;
CREATE TABLE IF NOT EXISTS `schedule_daily_time` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync` tinyint(4) NOT NULL,
  `purge` tinyint(4) NOT NULL COMMENT 'Mark For Deletion',
  `status` tinyint(4),
  `start` time,
  `end` time,
  `WeekDays` smallint(6) NOT NULL,
  `sch_name` varchar(200) COLLATE utf16_bin,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

-- Dumping data for table maxair.schedule_daily_time: ~0 rows (approximately)
/*!40000 ALTER TABLE `schedule_daily_time` DISABLE KEYS */;
/*!40000 ALTER TABLE `schedule_daily_time` ENABLE KEYS */;

-- Dumping structure for table maxair.schedule_daily_time_zone
DROP TABLE IF EXISTS `schedule_daily_time_zone`;
CREATE TABLE IF NOT EXISTS `schedule_daily_time_zone` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync` tinyint(4) NOT NULL,
  `purge` tinyint(4) NOT NULL COMMENT 'Mark For Deletion',
  `status` tinyint(4),
  `schedule_daily_time_id` int(11),
  `zone_id` int(11),
  `temperature` float NOT NULL,
  `holidays_id` int(11),
  `coop` tinyint(4) NOT NULL,
  `sunset` tinyint(1),
  `sunset_offset` int(11),
  PRIMARY KEY (`id`),
  KEY `FK_schedule_daily_time_zone_schedule_daily_time` (`schedule_daily_time_id`),
  KEY `FK_schedule_daily_time_zone_zone` (`zone_id`),
  CONSTRAINT `FK_schedule_daily_time_zone_schedule_daily_time` FOREIGN KEY (`schedule_daily_time_id`) REFERENCES `schedule_daily_time` (`id`),
  CONSTRAINT `FK_schedule_daily_time_zone_zone` FOREIGN KEY (`zone_id`) REFERENCES `zone` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

-- Dumping data for table maxair.schedule_daily_time_zone: ~0 rows (approximately)
/*!40000 ALTER TABLE `schedule_daily_time_zone` DISABLE KEYS */;
/*!40000 ALTER TABLE `schedule_daily_time_zone` ENABLE KEYS */;

-- Dumping structure for table maxair.schedule_night_climate_time
DROP TABLE IF EXISTS `schedule_night_climate_time`;
CREATE TABLE IF NOT EXISTS `schedule_night_climate_time` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync` tinyint(4) NOT NULL,
  `purge` tinyint(4) NOT NULL COMMENT 'Mark For Deletion',
  `status` tinyint(4),
  `start_time` time,
  `end_time` time,
  `WeekDays` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

-- Dumping data for table maxair.schedule_night_climate_time: ~0 rows (approximately)
/*!40000 ALTER TABLE `schedule_night_climate_time` DISABLE KEYS */;
/*!40000 ALTER TABLE `schedule_night_climate_time` ENABLE KEYS */;

-- Dumping structure for table maxair.schedule_night_climat_zone
DROP TABLE IF EXISTS `schedule_night_climat_zone`;
CREATE TABLE IF NOT EXISTS `schedule_night_climat_zone` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync` tinyint(4) NOT NULL,
  `purge` tinyint(4) NOT NULL COMMENT 'Mark For Deletion',
  `status` tinyint(4),
  `zone_id` int(11),
  `schedule_night_climate_id` int(11),
  `min_temperature` float NOT NULL,
  `max_temperature` float NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_schedule_zone_night_climat_zone` (`zone_id`),
  KEY `FK_schedule_zone_night_climat_schedule_night_climate` (`schedule_night_climate_id`),
  CONSTRAINT `FK_schedule_zone_night_climat_schedule_night_climate` FOREIGN KEY (`schedule_night_climate_id`) REFERENCES `schedule_night_climate_time` (`id`),
  CONSTRAINT `FK_schedule_zone_night_climat_zone` FOREIGN KEY (`zone_id`) REFERENCES `zone` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

-- Dumping data for table maxair.schedule_night_climat_zone: ~3 rows (approximately)
/*!40000 ALTER TABLE `schedule_night_climat_zone` DISABLE KEYS */;
/*!40000 ALTER TABLE `schedule_night_climat_zone` ENABLE KEYS */;

-- Dumping structure for table maxair.sensor_type
DROP TABLE IF EXISTS `sensor_type`;
CREATE TABLE IF NOT EXISTS `sensor_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync` tinyint(4) NOT NULL,
  `purge` tinyint(4) NOT NULL COMMENT 'Mark For Deletion',
  `type` char(50) COLLATE utf8_bin,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Dumping structure for table maxair.sw_install
DROP TABLE IF EXISTS `sw_install`;
CREATE TABLE IF NOT EXISTS `sw_install` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `script` char(100) NOT NULL,
  `pid` int(11),
  `start_datetime` timestamp NULL,
  `stop_datetime` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- Dumping structure for table maxair.system
DROP TABLE IF EXISTS `system`;
CREATE TABLE IF NOT EXISTS `system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync` tinyint(4) NOT NULL,
  `purge` tinyint(4) NOT NULL COMMENT 'Mark For Deletion',
  `name` varchar(50) COLLATE utf16_bin,
  `version` varchar(50) CHARACTER SET latin1,
  `build` varchar(50) COLLATE utf16_bin,
  `update_location` char(250) CHARACTER SET latin1,
  `update_file` char(100) CHARACTER SET latin1,
  `update_alias` char(100) CHARACTER SET latin1,
  `country` char(2) CHARACTER SET latin1,
  `language` char(10) COLLATE utf16_bin,
  `city` char(100) CHARACTER SET latin1,
  `zip` char(100) COLLATE utf16_bin,
  `openweather_api` char(100) CHARACTER SET latin1,
  `backup_email` char(100) COLLATE utf16_bin,
  `ping_home` bit(1),
  `timezone` varchar(50) COLLATE utf16_bin,
  `shutdown` tinyint(4),
  `reboot` tinyint(4),
  `c_f` tinyint(4) NOT NULL COMMENT '0=C, 1=F',
  `mode` tinyint(4),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

-- Dumping data for table maxair.system: ~0 rows (approximately)
/*!40000 ALTER TABLE `system` DISABLE KEYS */;
/*!40000 ALTER TABLE `system` ENABLE KEYS */;

-- Dumping structure for table maxair.sensors
DROP TABLE IF EXISTS `sensors`;
CREATE TABLE `sensors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync` tinyint(4) NOT NULL,
  `purge` tinyint(4) NOT NULL COMMENT 'Mark For Deletion',
  `zone_id` int(11) DEFAULT NULL,
  `sensor_id` int(11) DEFAULT NULL,
  `sensor_child_id` int(11) DEFAULT NULL,
  `sensor_type_id` int(11) DEFAULT NULL,
  `index_id` tinyint(4) NOT NULL,
  `pre_post` tinyint(1) NOT NULL,
  `name` char(50) COLLATE utf8_bin DEFAULT NULL,
  `graph_num` tinyint(4) NOT NULL,
  `show_it` tinyint(1) NOT NULL,
  `frost_temp` int(11) NOT NULL,
  `frost_controller` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_sensors_nodes` (`sensor_id`),
  KEY `FK_sensors_zone` (`zone_id`),
  CONSTRAINT `FK_sensors_nodes` FOREIGN KEY (`sensor_id`) REFERENCES `nodes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Dumping data for table maxair.sensors: ~0 rows (approximately)
/*!40000 ALTER TABLE `sensors` DISABLE KEYS */;
/*!40000 ALTER TABLE `sensors` ENABLE KEYS */;

-- Dumping structure for table maxair.user
DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_enable` tinyint(1),
  `fullname` varchar(100) NOT NULL,
  `username` varchar(25) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `cpdate` timestamp NOT NULL ON UPDATE current_timestamp(),
  `account_date` timestamp NOT NULL,
  `admin_account` tinyint(4),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Dumping data for table maxair.user: ~0 rows (approximately)
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

-- Dumping structure for table maxair.userhistory
DROP TABLE IF EXISTS `userhistory`;
CREATE TABLE IF NOT EXISTS `userhistory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50),
  `password` varchar(50),
  `date` datetime,
  `audit` tinytext,
  `ipaddress` tinytext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=144 DEFAULT CHARSET=latin1;

-- Dumping data for table maxair.userhistory: ~139 rows (approximately)
/*!40000 ALTER TABLE `userhistory` DISABLE KEYS */;
/*!40000 ALTER TABLE `userhistory` ENABLE KEYS */;

-- Dumping structure for table maxair.weather
DROP TABLE IF EXISTS `weather`;
CREATE TABLE IF NOT EXISTS `weather` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync` tinyint(4) NOT NULL,
  `location` varchar(50) COLLATE utf8_bin,
  `c` tinyint(4),
  `wind_speed` varchar(50) COLLATE utf8_bin,
  `title` varchar(50) COLLATE utf8_bin,
  `description` varchar(50) COLLATE utf8_bin,
  `sunrise` varchar(50) COLLATE utf8_bin,
  `sunset` varchar(50) COLLATE utf8_bin,
  `img` varchar(50) COLLATE utf8_bin,
  `last_update` timestamp NOT NULL ON UPDATE current_timestamp() COMMENT 'Last weather update',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Dumping data for table maxair.weather: ~0 rows (approximately)
/*!40000 ALTER TABLE `weather` DISABLE KEYS */;
/*!40000 ALTER TABLE `weather` ENABLE KEYS */;

-- Dumping structure for table maxair.zone
DROP TABLE IF EXISTS `zone`;
CREATE TABLE IF NOT EXISTS `zone` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync` tinyint(4) NOT NULL,
  `purge` tinyint(4) NOT NULL COMMENT 'Mark For Deletion',
  `status` tinyint(4),
  `zone_state` tinyint(4),
  `index_id` tinyint(4),
  `name` char(50) COLLATE utf8_bin,
  `type_id` int(11),
  `max_operation_time` SMALLINT(4),
  PRIMARY KEY (`id`),
  KEY `FK_zone_type_id` (`type_id`),
  CONSTRAINT `FK_zone_type_id` FOREIGN KEY (`type_id`) REFERENCES `zone_type` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Dumping data for table maxair.zone: ~3 rows (approximately)
/*!40000 ALTER TABLE `zone` DISABLE KEYS */;
/*!40000 ALTER TABLE `zone` ENABLE KEYS */;

-- Dumping structure for table maxair.zone_relays
DROP TABLE IF EXISTS `zone_relays`;
CREATE TABLE `zone_relays` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync` tinyint(4) NOT NULL,
  `purge` tinyint(4) NOT NULL COMMENT 'Mark For Deletion',
  `state` tinyint(4) DEFAULT NULL,
  `current_state` tinyint(4) NOT NULL,
  `zone_id` int(11) DEFAULT NULL,
  `zone_relay_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_zone_relays_zone` (`zone_id`),
  CONSTRAINT `FK_zone_relays_zone` FOREIGN KEY (`zone_id`) REFERENCES `zone` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Dumping data for table maxair.zone_relayss: 
/*!40000 ALTER TABLE `zone_relays` DISABLE KEYS */;
/*!40000 ALTER TABLE `zone_relays` ENABLE KEYS */;

-- Dumping structure for table maxair.zone_current_state
DROP TABLE IF EXISTS `zone_current_state`;
CREATE TABLE IF NOT EXISTS `zone_current_state` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync` tinyint(4) NOT NULL,
  `purge` tinyint(4) NOT NULL,
  `zone_id` int(11) NOT NULL,
  `mode` int(11),
  `status` tinyint(1),
  `temp_reading` decimal(4,1),
  `temp_target` decimal(4,1),
  `temp_cut_in` decimal(4,1),
  `temp_cut_out` decimal(4,1),
  `controler_fault` int(1),
  `controler_seen_time` timestamp NULL,
  `sensor_fault` int(1),
  `sensor_seen_time` timestamp NULL,
  `sensor_reading_time` timestamp NULL,
  `overrun` tinyint(1),
 PRIMARY KEY (`id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

-- Dumping data for table maxair.zone_current_state: 8 rows
/*!40000 ALTER TABLE `zone_current_state` DISABLE KEYS */;
/*!40000 ALTER TABLE `zone_current_state` ENABLE KEYS */;

-- Dumping structure for table maxair.zone_graphs
DROP TABLE IF EXISTS `zone_graphs`;
CREATE TABLE IF NOT EXISTS `zone_graphs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync` tinyint(4) NOT NULL,
  `purge` tinyint(4) NOT NULL COMMENT 'Mark For Deletion',
  `zone_id` int(11),
  `name` char(50) COLLATE utf8_bin,
  `type` char(50) COLLATE utf8_bin,
  `category` int(11),
  `node_id` char(15) COLLATE utf16_bin,
  `child_id` tinyint(4),
  `sub_type` int(11),
  `payload` decimal(10,2),
  `datetime` timestamp NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

-- Dumping structure for table maxair.zone_sensor
DROP TABLE IF EXISTS `zone_sensors`;
CREATE TABLE IF NOT EXISTS `zone_sensors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync` tinyint(4) NOT NULL,
  `purge` tinyint(4) NOT NULL COMMENT 'Mark For Deletion',
  `zone_id` int(11),
  `min_c` tinyint(4),
  `max_c` tinyint(4),
  `default_c` tinyint(4),
  `hysteresis_time` tinyint(4),
  `sp_deadband` float NOT NULL,
  `zone_sensor_id` int(11),
  PRIMARY KEY (`id`),
  KEY `FK_zone_sensors_zone` (`zone_id`),
  KEY `FK_zone_sensors_sensors` (`zone_sensor_id`),
  CONSTRAINT `FK_zone_sensors_sensors` FOREIGN KEY (`zone_sensor_id`) REFERENCES `sensors` (`id`),
  CONSTRAINT `FK_zone_sensors_zone` FOREIGN KEY (`zone_id`) REFERENCES `zone` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Dumping structure for table maxair.zone_type
DROP TABLE IF EXISTS `zone_type`;
CREATE TABLE IF NOT EXISTS `zone_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync` tinyint(4) NOT NULL,
  `purge` tinyint(4) NOT NULL COMMENT 'Mark For Deletion',
  `type` char(50) COLLATE utf8_bin,
  `category` int(11),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

