-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versi server:                 10.1.22-MariaDB - mariadb.org binary distribution
-- OS Server:                    Win32
-- HeidiSQL Versi:               9.5.0.5196
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Membuang struktur basisdata untuk meter
CREATE DATABASE IF NOT EXISTS `meter` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `meter`;

-- membuang struktur untuk procedure meter.insert_rec
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_rec`(
	IN `dt` DATETIME,
	IN `id` VARCHAR(50),
	IN `val01` FLOAT(11,4),
	IN `val02` FLOAT(11,4),
	IN `val03` FLOAT(11,4),
	IN `val04` FLOAT(11,4),
	IN `val05` FLOAT(11,4),
	IN `val06` FLOAT(11,4),
	IN `val07` FLOAT(11,4),
	IN `val08` FLOAT(11,4),
	IN `val09` FLOAT(11,4),
	IN `val10` FLOAT(11,4),
	IN `val11` FLOAT(11,4),
	IN `val12` FLOAT(11,4),
	IN `val13` FLOAT(11,4),
	IN `val14` FLOAT(11,4),
	IN `val15` FLOAT(11,4),
	IN `val16` FLOAT(11,4),
	IN `val17` FLOAT(11,4),
	IN `val18` FLOAT(11,4),
	IN `val19` FLOAT(11,4),
	IN `val20` FLOAT(11,4),
	IN `val21` FLOAT(11,4),
	IN `val22` FLOAT(11,4),
	IN `val23` FLOAT(11,4),
	IN `val24` FLOAT(11,4),
	IN `val25` FLOAT(11,4),
	IN `val26` FLOAT(11,4),
	IN `val27` FLOAT(11,4),
	IN `val28` FLOAT(11,4),
	IN `val29` FLOAT(11,4),
	IN `val30` FLOAT(11,4),
	IN `val31` FLOAT(11,4),
	IN `val32` FLOAT(11,4),
	IN `val33` FLOAT(11,4),
	IN `val34` FLOAT(11,4),
	IN `val35` FLOAT(11,4)

)
BEGIN
	/*
	 * INSERT RAW DATA & AVERAGE EVERY 15 MINUTE
	 * **************************************************
	 */
	 
	-- every 15 menit --> avg data
	IF CONVERT(DATE_FORMAT(dt, "%i"),UNSIGNED)%15 = 0 THEN
		
		SELECT COUNT(0) INTO @dtExist
		FROM rec_15m a
		WHERE 
			DATE_FORMAT(a.Date_Time, "%Y%m%d %H:%i") = DATE_FORMAT(dt, "%Y%m%d %H:%i")
			AND a.Device_ID = id
		;
		
		-- if avg data not exist
		IF @dtExist = 0 THEN
			-- insert avg data into recording 15 menit (rec_15m)
			INSERT INTO rec_15m
			SELECT 
				-- MAX(DATE_FORMAT(a.Date_Time, "%Y-%m-%d %H:%i:00")),
				DATE_FORMAT(dt, "%Y-%m-%d %H:%i:00"),
				a.Device_ID,
				AVG(a.`Current_A`),
				AVG(a.`Current_B`),
				AVG(a.`Current_C`),
				AVG(a.`Current_N`),
				AVG(a.`Current_G`),
				AVG(a.`Current_Avg`),
				AVG(a.`Voltage_A-B`),
				AVG(a.`Voltage_B-C`),
				AVG(a.`Voltage_C-A`),
				AVG(a.`Voltage_L-L_Avg`),
				AVG(a.`Voltage_A-N`),
				AVG(a.`Voltage_B-N`),
				AVG(a.`Voltage_C-N`),
				AVG(a.`Voltage_L-N_Avg`),
				AVG(a.`Active_Power_A`),
				AVG(a.`Active_Power_B`),
				AVG(a.`Active_Power_C`),
				AVG(a.`Active_Power_Total`),
				AVG(a.`Reactive_Power_A`),
				AVG(a.`Reactive_Power_B`),
				AVG(a.`Reactive_Power_C`),
				AVG(a.`Reactive_Power_Total`),
				AVG(a.`Apparent_Power_A`),
				AVG(a.`Apparent_Power_B`),
				AVG(a.`Apparent_Power_C`),
				AVG(a.`Apparent_Power_Total`),
				AVG(a.`Power_Factor_A`),
				AVG(a.`Power_Factor_B`),
				AVG(a.`Power_Factor_C`),
				AVG(a.`Power_Factor_Total`),
				AVG(a.`Displacement_Power_Factor_A`),
				AVG(a.`Displacement_Power_Factor_B`),
				AVG(a.`Displacement_Power_Factor_C`),
				AVG(a.`Displacement_Power_Factor_Total`),
				AVG(a.`Frequency`)
			FROM 
				rec_raw a
			WHERE
				a.Device_ID = id
				AND a.Date_Time > SYSDATE() - INTERVAL 30 MINUTE -- calculate (AVG) maximal in 30 minutes (2x cycle)
			GROUP BY
				2;
			
			
			-- del raw data (after save avg in 15 minutes)
			DELETE FROM rec_raw WHERE Device_ID = id; 
			
			-- del rec_15m data after 1 year (only save 1 year)
			DELETE FROM rec_15m WHERE Date_Time <= SYSDATE() - INTERVAL 1 YEAR; 

		END IF;
	END IF;
	
	-- insert raw data recording (rec_raw)
	-- put at last --> so there is always 1 record left
	INSERT IGNORE INTO rec_raw
	VALUES(dt,id,val01,val02,val03,val04,val05,val06,val07,val08,val09,val10,val11,val12,val13,val14,val15,val16,val17,val18,val19,val20,val21,val22,val23,val24,val25,val26,val27,val28,val29,val30,val31,val32,val33,val34,val35);
	
	
END//
DELIMITER ;

-- membuang struktur untuk table meter.rec_15m
CREATE TABLE IF NOT EXISTS `rec_15m` (
  `Date_Time` datetime NOT NULL,
  `Device_ID` varchar(50) NOT NULL,
  `Current_A` float(11,2) DEFAULT '0.00',
  `Current_B` float(11,2) DEFAULT '0.00',
  `Current_C` float(11,2) DEFAULT '0.00',
  `Current_N` float(11,2) DEFAULT '0.00',
  `Current_G` float(11,2) DEFAULT '0.00',
  `Current_Avg` float(11,2) DEFAULT '0.00',
  `Voltage_A-B` float(11,2) DEFAULT '0.00',
  `Voltage_B-C` float(11,2) DEFAULT '0.00',
  `Voltage_C-A` float(11,2) DEFAULT '0.00',
  `Voltage_L-L_Avg` float(11,2) DEFAULT '0.00',
  `Voltage_A-N` float(11,2) DEFAULT '0.00',
  `Voltage_B-N` float(11,2) DEFAULT '0.00',
  `Voltage_C-N` float(11,2) DEFAULT '0.00',
  `Voltage_L-N_Avg` float(11,2) DEFAULT '0.00',
  `Active_Power_A` float(11,2) DEFAULT '0.00',
  `Active_Power_B` float(11,2) DEFAULT '0.00',
  `Active_Power_C` float(11,2) DEFAULT '0.00',
  `Active_Power_Total` float(11,2) DEFAULT '0.00',
  `Reactive_Power_A` float(11,2) DEFAULT '0.00',
  `Reactive_Power_B` float(11,2) DEFAULT '0.00',
  `Reactive_Power_C` float(11,2) DEFAULT '0.00',
  `Reactive_Power_Total` float(11,2) DEFAULT '0.00',
  `Apparent_Power_A` float(11,2) DEFAULT '0.00',
  `Apparent_Power_B` float(11,2) DEFAULT '0.00',
  `Apparent_Power_C` float(11,2) DEFAULT '0.00',
  `Apparent_Power_Total` float(11,2) DEFAULT '0.00',
  `Power_Factor_A` float(11,2) DEFAULT '0.00',
  `Power_Factor_B` float(11,2) DEFAULT '0.00',
  `Power_Factor_C` float(11,2) DEFAULT '0.00',
  `Power_Factor_Total` float(11,2) DEFAULT '0.00',
  `Displacement_Power_Factor_A` float(11,2) DEFAULT '0.00',
  `Displacement_Power_Factor_B` float(11,2) DEFAULT '0.00',
  `Displacement_Power_Factor_C` float(11,2) DEFAULT '0.00',
  `Displacement_Power_Factor_Total` float(11,2) DEFAULT '0.00',
  `Frequency` float(11,2) DEFAULT '0.00',
  PRIMARY KEY (`Date_Time`,`Device_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- membuang struktur untuk table meter.rec_raw
CREATE TABLE IF NOT EXISTS `rec_raw` (
  `Date_Time` datetime NOT NULL,
  `Device_ID` varchar(50) NOT NULL,
  `Current_A` float(11,2) DEFAULT '0.00',
  `Current_B` float(11,2) DEFAULT '0.00',
  `Current_C` float(11,2) DEFAULT '0.00',
  `Current_N` float(11,2) DEFAULT '0.00',
  `Current_G` float(11,2) DEFAULT '0.00',
  `Current_Avg` float(11,2) DEFAULT '0.00',
  `Voltage_A-B` float(11,2) DEFAULT '0.00',
  `Voltage_B-C` float(11,2) DEFAULT '0.00',
  `Voltage_C-A` float(11,2) DEFAULT '0.00',
  `Voltage_L-L_Avg` float(11,2) DEFAULT '0.00',
  `Voltage_A-N` float(11,2) DEFAULT '0.00',
  `Voltage_B-N` float(11,2) DEFAULT '0.00',
  `Voltage_C-N` float(11,2) DEFAULT '0.00',
  `Voltage_L-N_Avg` float(11,2) DEFAULT '0.00',
  `Active_Power_A` float(11,2) DEFAULT '0.00',
  `Active_Power_B` float(11,2) DEFAULT '0.00',
  `Active_Power_C` float(11,2) DEFAULT '0.00',
  `Active_Power_Total` float(11,2) DEFAULT '0.00',
  `Reactive_Power_A` float(11,2) DEFAULT '0.00',
  `Reactive_Power_B` float(11,2) DEFAULT '0.00',
  `Reactive_Power_C` float(11,2) DEFAULT '0.00',
  `Reactive_Power_Total` float(11,2) DEFAULT '0.00',
  `Apparent_Power_A` float(11,2) DEFAULT '0.00',
  `Apparent_Power_B` float(11,2) DEFAULT '0.00',
  `Apparent_Power_C` float(11,2) DEFAULT '0.00',
  `Apparent_Power_Total` float(11,2) DEFAULT '0.00',
  `Power_Factor_A` float(11,2) DEFAULT '0.00',
  `Power_Factor_B` float(11,2) DEFAULT '0.00',
  `Power_Factor_C` float(11,2) DEFAULT '0.00',
  `Power_Factor_Total` float(11,2) DEFAULT '0.00',
  `Displacement_Power_Factor_A` float(11,2) DEFAULT '0.00',
  `Displacement_Power_Factor_B` float(11,2) DEFAULT '0.00',
  `Displacement_Power_Factor_C` float(11,2) DEFAULT '0.00',
  `Displacement_Power_Factor_Total` float(11,2) DEFAULT '0.00',
  `Frequency` float(11,2) DEFAULT '0.00',
  PRIMARY KEY (`Date_Time`,`Device_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_15m` AS select * from  `rec_15m` order by Date_Time desc limit 100 ;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_real_time` AS select 
    max(`a`.`Date_Time`) AS `Date_Time`,
    `a`.`Device_ID` AS `Device_ID`,
    `a`.`Current_A` AS `Current_A`,
    `a`.`Current_B` AS `Current_B`,
    `a`.`Current_C` AS `Current_C`,
    `a`.`Current_N` AS `Current_N`,
    `a`.`Current_G` AS `Current_G`,
    `a`.`Current_Avg` AS `Current_Avg`,
    `a`.`Voltage_A-B` AS `Voltage_A-B`,
    `a`.`Voltage_B-C` AS `Voltage_B-C`,
    `a`.`Voltage_C-A` AS `Voltage_C-A`,
    `a`.`Voltage_L-L_Avg` AS `Voltage_L-L_Avg`,
    `a`.`Voltage_A-N` AS `Voltage_A-N`,
    `a`.`Voltage_B-N` AS `Voltage_B-N`,
    `a`.`Voltage_C-N` AS `Voltage_C-N`,
    `a`.`Voltage_L-N_Avg` AS `Voltage_L-N_Avg`,
    `a`.`Active_Power_A` AS `Active_Power_A`,
    `a`.`Active_Power_B` AS `Active_Power_B`,
    `a`.`Active_Power_C` AS `Active_Power_C`,
    `a`.`Active_Power_Total` AS `Active_Power_Total`,
    `a`.`Reactive_Power_A` AS `Reactive_Power_A`,
    `a`.`Reactive_Power_B` AS `Reactive_Power_B`,
    `a`.`Reactive_Power_C` AS `Reactive_Power_C`,
    `a`.`Reactive_Power_Total` AS `Reactive_Power_Total`,
    `a`.`Apparent_Power_A` AS `Apparent_Power_A`,
    `a`.`Apparent_Power_B` AS `Apparent_Power_B`,
    `a`.`Apparent_Power_C` AS `Apparent_Power_C`,
    `a`.`Apparent_Power_Total` AS `Apparent_Power_Total`,
    `a`.`Power_Factor_A` AS `Power_Factor_A`,
    `a`.`Power_Factor_B` AS `Power_Factor_B`,
    `a`.`Power_Factor_C` AS `Power_Factor_C`,
    `a`.`Power_Factor_Total` AS `Power_Factor_Total`,
    `a`.`Displacement_Power_Factor_A` AS `Displacement_Power_Factor_A`,
    `a`.`Displacement_Power_Factor_B` AS `Displacement_Power_Factor_B`,
    `a`.`Displacement_Power_Factor_C` AS `Displacement_Power_Factor_C`,
    `a`.`Displacement_Power_Factor_Total` AS `Displacement_Power_Factor_Total`,
    `a`.`Frequency` AS `Frequency` 
  from 
    `rec_raw` `a`
group by 2 ;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
