--
-- phpmybatch - phpmybatch - An open source batches of goods management system software.
-- Copyright (C)2012 Andrea Boccaccio
-- contact email: andrea@andreaboccaccio.com
-- 
-- This file is part of phpmybatch.
-- 
-- phpmybatch is free software: you can redistribute it and/or modify
-- it under the terms of the GNU Affero General Public License as published by
-- the Free Software Foundation, either version 3 of the License, or
-- (at your option) any later version.
-- 
-- phpmybatch is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU Affero General Public License for more details.
-- 
-- You should have received a copy of the GNU Affero General Public License
-- along with phpmybatch. If not, see <http://www.gnu.org/licenses/>.
-- 
--
DROP FUNCTION  IF EXISTS getBatchKind;

DELIMITER |

CREATE FUNCTION getBatchKind (batch VARCHAR(50)) RETURNS VARCHAR(50)
BEGIN
DECLARE FC VARCHAR(1);
DECLARE RET VARCHAR(50);

SET FC=SUBSTRING(batch,1,1);

SET RET=CASE FC
	WHEN 'S' THEN 'SUINI'
	WHEN 'P' THEN 'POLLAME'
	WHEN 'O' THEN 'OVINI'
	ELSE
		'BOVINI'
	END;

RETURN RET;
END|

delimiter ;

UPDATE DBVERSION SET version='004' WHERE (version='003');