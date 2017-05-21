--
-- phpmybatch - phpmybatch - An open source batches of goods management system software.
-- Copyright (C)2012 Andrea Boccaccio
-- contact email: andrea@andreaboccaccio.it
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
-- along with phpmywhs. If not, see <http://www.gnu.org/licenses/>.
-- 
--
CREATE TABLE IF NOT EXISTS COUNTRY (id BIGINT AUTO_INCREMENT PRIMARY KEY
,codealpha2 VARCHAR(2) NOT NULL
,codealpha3 VARCHAR(3) NOT NULL
,number VARCHAR(3) NOT NULL
,enname VARCHAR(50) NOT NULL
,description VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS COUNTRY_LOG (id BIGINT AUTO_INCREMENT PRIMARY KEY
,utctt_start DATETIME NOT NULL
,utctt_end DATETIME NOT NULL
,opcode VARCHAR(3) NOT NULL DEFAULT 'UNK'
,idorig BIGINT NOT NULL
,codealpha2 VARCHAR(2) NOT NULL
,codealpha3 VARCHAR(3) NOT NULL
,number VARCHAR(3) NOT NULL
,enname VARCHAR(50) NOT NULL
,description VARCHAR(255)
);

CREATE TRIGGER TRG_COUNTRY_INSERT_AFT AFTER INSERT
ON COUNTRY
FOR EACH ROW
INSERT INTO COUNTRY_LOG (
	utctt_start
	,utctt_end
	,opcode
	,idorig
	,codealpha2
	,codealpha3
	,number
	,enname
	,description
) VALUES (
	UTC_TIMESTAMP()
	,UTC_TIMESTAMP()
	,'INS'
	,NEW.id
	,NEW.codealpha2
	,NEW.codealpha3
	,NEW.number
	,NEW.enname
	,NEW.description
);

delimiter |

CREATE TRIGGER TRG_COUNTRY_UPDATE_BFR BEFORE UPDATE
ON COUNTRY
FOR EACH ROW
BEGIN
UPDATE COUNTRY_LOG SET utctt_end = UTC_TIMESTAMP()
WHERE
(
	(OLD.id = idorig)
	AND
	(utctt_end = utctt_start)
);
INSERT INTO COUNTRY_LOG (
	utctt_start
	,utctt_end
	,opcode
	,idorig
	,codealpha2
	,codealpha3
	,number
	,enname
	,description
) VALUES (
	UTC_TIMESTAMP()
	,UTC_TIMESTAMP()
	,'UPD'
	,NEW.id
	,NEW.codealpha2
	,NEW.codealpha3
	,NEW.number
	,NEW.enname
	,NEW.description
);
END;

|

CREATE TRIGGER TRG_COUNTRY_DELETE_BFR BEFORE DELETE
ON COUNTRY
FOR EACH ROW
BEGIN
UPDATE COUNTRY_LOG SET utctt_end = UTC_TIMESTAMP()
WHERE
(
	(OLD.id = idorig)
	AND
	(utctt_end = utctt_start)
);
INSERT INTO COUNTRY_LOG (
	utctt_start
	,utctt_end
	,opcode
	,idorig
	,codealpha2
	,codealpha3
	,number
	,enname
	,description
) VALUES (
	UTC_TIMESTAMP()
	,UTC_TIMESTAMP()
	,'DEL'
	,OLD.id
	,OLD.codealpha2
	,OLD.codealpha3
	,OLD.number
	,OLD.enname
	,OLD.description
);
END;

|

delimiter ;

CREATE TABLE IF NOT EXISTS DOCUMENT_DENORM (id BIGINT AUTO_INCREMENT PRIMARY KEY
,kind VARCHAR(50)
,code VARCHAR(20) NOT NULL
,contractor_code VARCHAR(25)
,contractor VARCHAR(50)
,country BIGINT
,doc_date VARCHAR(10) NOT NULL
,description VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS DOCUMENT_DENORM_LOG (id BIGINT AUTO_INCREMENT PRIMARY KEY
,utctt_start DATETIME NOT NULL
,utctt_end DATETIME NOT NULL
,opcode VARCHAR(3) NOT NULL DEFAULT 'UNK'
,idorig BIGINT NOT NULL
,kind VARCHAR(50)
,code VARCHAR(20) NOT NULL
,contractor_code VARCHAR(25)
,contractor VARCHAR(50)
,country BIGINT
,doc_date VARCHAR(10) NOT NULL
,description VARCHAR(255)
);

CREATE TRIGGER TRG_DOCUMENT_DENORM_INSERT_AFT AFTER INSERT
ON DOCUMENT_DENORM
FOR EACH ROW
INSERT INTO DOCUMENT_DENORM_LOG (
	utctt_start
	,utctt_end
	,opcode
	,idorig
	,kind
	,code
	,contractor_code
	,contractor
	,country
	,doc_date
	,description
) VALUES (
	UTC_TIMESTAMP()
	,UTC_TIMESTAMP()
	,'INS'
	,NEW.id
	,NEW.kind
	,NEW.code
	,NEW.contractor_code
	,NEW.contractor
	,NEW.country
	,NEW.doc_date
	,NEW.description
);

delimiter |

CREATE TRIGGER TRG_DOCUMENT_DENORM_UPDATE_BFR BEFORE UPDATE
ON DOCUMENT_DENORM
FOR EACH ROW
BEGIN
UPDATE DOCUMENT_DENORM_LOG SET utctt_end = UTC_TIMESTAMP()
WHERE
(
	(OLD.id = idorig)
	AND
	(utctt_end = utctt_start)
);
INSERT INTO DOCUMENT_DENORM_LOG (
	utctt_start
	,utctt_end
	,opcode
	,idorig
	,kind
	,code
	,contractor_code
	,contractor
	,country
	,doc_date
	,description
) VALUES (
	UTC_TIMESTAMP()
	,UTC_TIMESTAMP()
	,'UPD'
	,NEW.id
	,NEW.kind
	,NEW.code
	,NEW.contractor_code
	,NEW.contractor
	,NEW.country
	,NEW.doc_date
	,NEW.description
);
END;

|

CREATE TRIGGER TRG_DOCUMENT_DENORM_DELETE_BFR BEFORE DELETE
ON DOCUMENT_DENORM
FOR EACH ROW
BEGIN
UPDATE DOCUMENT_DENORM_LOG SET utctt_end = UTC_TIMESTAMP()
WHERE
(
	(OLD.id = idorig)
	AND
	(utctt_end = utctt_start)
);
INSERT INTO DOCUMENT_DENORM_LOG (
	utctt_start
	,utctt_end
	,opcode
	,idorig
	,kind
	,code
	,contractor_code
	,contractor
	,country
	,doc_date
	,description
) VALUES (
	UTC_TIMESTAMP()
	,UTC_TIMESTAMP()
	,'DEL'
	,OLD.id
	,OLD.kind
	,OLD.code
	,OLD.contractor_code
	,OLD.contractor
	,OLD.country
	,OLD.doc_date
	,OLD.description
);
END;

|

delimiter ;

CREATE TABLE IF NOT EXISTS ITEM_DENORM (id BIGINT AUTO_INCREMENT PRIMARY KEY
,document BIGINT NOT NULL
,kind VARCHAR(50)
,code VARCHAR(50)
,name VARCHAR(50)
,producer VARCHAR(50)
,yearProd VARCHAR(4)
,batch VARCHAR(50)
,batch_orig VARCHAR(100)
,country BIGINT
,district VARCHAR(50)
,stabCEE VARCHAR(50)
,qty INT
,kg DECIMAL(12,2)
,arrival VARCHAR(10)
,vt_start VARCHAR(10)
,vt_end VARCHAR(10)
,description VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS ITEM_DENORM_LOG (id BIGINT AUTO_INCREMENT PRIMARY KEY
,utctt_start DATETIME NOT NULL
,utctt_end DATETIME NOT NULL
,opcode VARCHAR(3) NOT NULL DEFAULT 'UNK'
,idorig BIGINT NOT NULL
,document BIGINT NOT NULL
,kind VARCHAR(50)
,code VARCHAR(50)
,name VARCHAR(50)
,producer VARCHAR(50)
,yearProd VARCHAR(4)
,batch VARCHAR(50) NOT NULL
,batch_orig VARCHAR(100)
,country BIGINT
,district VARCHAR(50)
,stabCEE VARCHAR(50)
,qty INT
,kg DECIMAL(12,2)
,arrival VARCHAR(10)
,vt_start VARCHAR(10)
,vt_end VARCHAR(10)
,description VARCHAR(255)
);

CREATE TRIGGER TRG_ITEM_DENORM_INSERT_AFT AFTER INSERT
ON ITEM_DENORM
FOR EACH ROW
INSERT INTO ITEM_DENORM_LOG (
	utctt_start
	,utctt_end
	,opcode
	,idorig
	,document
	,kind
	,code
	,name
	,producer
	,yearProd
	,batch
	,batch_orig
	,country
	,district
	,stabCEE
	,qty
	,kg
	,arrival
	,vt_start
	,vt_end
	,description
) VALUES (
	UTC_TIMESTAMP()
	,UTC_TIMESTAMP()
	,'INS'
	,NEW.id
	,NEW.document
	,NEW.kind
	,NEW.code
	,NEW.name
	,NEW.producer
	,NEW.yearProd
	,NEW.batch
	,NEW.batch_orig
	,NEW.country
	,NEW.district
	,NEW.stabCEE
	,NEW.qty
	,NEW.kg
	,NEW.arrival
	,NEW.vt_start
	,NEW.vt_end
	,NEW.description
);

delimiter |

CREATE TRIGGER TRG_ITEM_DENORM_UPDATE_BFR BEFORE UPDATE
ON ITEM_DENORM
FOR EACH ROW
BEGIN
UPDATE ITEM_DENORM_LOG SET utctt_end = UTC_TIMESTAMP()
WHERE
(
	(OLD.id = idorig)
	AND
	(utctt_end = utctt_start)
);
INSERT INTO ITEM_DENORM_LOG (
	utctt_start
	,utctt_end
	,opcode
	,idorig
	,document
	,kind
	,code
	,name
	,producer
	,yearProd
	,batch
	,batch_orig
	,country
	,district
	,stabCEE
	,qty
	,kg
	,arrival
	,vt_start
	,vt_end
	,description
) VALUES (
	UTC_TIMESTAMP()
	,UTC_TIMESTAMP()
	,'UPD'
	,NEW.id
	,NEW.document
	,NEW.kind
	,NEW.code
	,NEW.name
	,NEW.producer
	,NEW.yearProd
	,NEW.batch
	,NEW.batch_orig
	,NEW.country
	,NEW.district
	,NEW.stabCEE
	,NEW.qty
	,NEW.kg
	,NEW.arrival
	,NEW.vt_start
	,NEW.vt_end
	,NEW.description
);
END;

|

CREATE TRIGGER TRG_ITEM_DENORM_DELETE_BFR BEFORE DELETE
ON ITEM_DENORM
FOR EACH ROW
BEGIN
UPDATE ITEM_DENORM_LOG SET utctt_end = UTC_TIMESTAMP()
WHERE
(
	(OLD.id = idorig)
	AND
	(utctt_end = utctt_start)
);
INSERT INTO ITEM_DENORM_LOG (
	utctt_start
	,utctt_end
	,opcode
	,idorig
	,document
	,kind
	,code
	,name
	,producer
	,yearProd
	,batch
	,batch_orig
	,country
	,district
	,stabCEE
	,qty
	,kg
	,arrival
	,vt_start
	,vt_end
	,description
) VALUES (
	UTC_TIMESTAMP()
	,UTC_TIMESTAMP()
	,'DEL'
	,OLD.id
	,OLD.document
	,OLD.kind
	,OLD.code
	,OLD.name
	,OLD.producer
	,OLD.yearprod
	,OLD.batch
	,batch_orig
	,OLD.country
	,OLD.district
	,OLD.stabCEE
	,OLD.qty
	,OLD.kg
	,OLD.arrival
	,OLD.vt_start
	,OLD.vt_end
	,OLD.description
);
END;

|

delimiter ;

CREATE TABLE IF NOT EXISTS CAUSE (id BIGINT AUTO_INCREMENT PRIMARY KEY
,in_out VARCHAR(1) NOT NULL
,name VARCHAR(50)
,description VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS CAUSE_LOG (id BIGINT AUTO_INCREMENT PRIMARY KEY
,utctt_start DATETIME NOT NULL
,utctt_end DATETIME NOT NULL
,opcode VARCHAR(3) NOT NULL DEFAULT 'UNK'
,idorig BIGINT NOT NULL
,in_out VARCHAR(1) NOT NULL
,name VARCHAR(50)
,description VARCHAR(255)
);

CREATE TRIGGER TRG_CAUSE_INSERT_AFT AFTER INSERT
ON CAUSE
FOR EACH ROW
INSERT INTO CAUSE_LOG (
	utctt_start
	,utctt_end
	,opcode
	,idorig
	,in_out
	,name
	,description
) VALUES (
	UTC_TIMESTAMP()
	,UTC_TIMESTAMP()
	,'INS'
	,NEW.id
	,NEW.in_out
	,NEW.name
	,NEW.description
);

delimiter |

CREATE TRIGGER TRG_CAUSE_UPDATE_BFR BEFORE UPDATE
ON CAUSE
FOR EACH ROW
BEGIN
UPDATE CAUSE_LOG SET utctt_end = UTC_TIMESTAMP()
WHERE
(
	(OLD.id = idorig)
	AND
	(utctt_end = utctt_start)
);
INSERT INTO CAUSE_LOG (
	utctt_start
	,utctt_end
	,opcode
	,idorig
	,in_out
	,name
	,description
) VALUES (
	UTC_TIMESTAMP()
	,UTC_TIMESTAMP()
	,'UPD'
	,NEW.id
	,NEW.in_out
	,NEW.name
	,NEW.description
);
END;

|

CREATE TRIGGER TRG_CAUSE_DELETE_BFR BEFORE DELETE
ON CAUSE
FOR EACH ROW
BEGIN
UPDATE CAUSE_LOG SET utctt_end = UTC_TIMESTAMP()
WHERE
(
	(OLD.id = idorig)
	AND
	(utctt_end = utctt_start)
);
INSERT INTO CAUSE_LOG (
	utctt_start
	,utctt_end
	,opcode
	,idorig
	,in_out
	,name
	,description
) VALUES (
	UTC_TIMESTAMP()
	,UTC_TIMESTAMP()
	,'DEL'
	,OLD.id
	,OLD.in_out
	,OLD.name
	,OLD.description
);
END;

|

delimiter ;

CREATE TABLE IF NOT EXISTS ITEM_OUT (id BIGINT AUTO_INCREMENT PRIMARY KEY
,cause BIGINT NOT NULL
,kind VARCHAR(50)
,code VARCHAR(50)
,name VARCHAR(50)
,producer VARCHAR(50)
,yearProd VARCHAR(4)
,batch VARCHAR(50)
,qty INT
,kg DECIMAL(12,2)
,ownDocumentYear varchar(4)
,ownDocumentCode VARCHAR(20)
,vt_start VARCHAR(10)
,vt_end VARCHAR(10)
,description VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS ITEM_OUT_LOG (id BIGINT AUTO_INCREMENT PRIMARY KEY
,utctt_start DATETIME NOT NULL
,utctt_end DATETIME NOT NULL
,opcode VARCHAR(3) NOT NULL DEFAULT 'UNK'
,idorig BIGINT NOT NULL
,cause BIGINT NOT NULL
,kind VARCHAR(50)
,code VARCHAR(50)
,name VARCHAR(50)
,producer VARCHAR(50) NOT NULL
,yearProd VARCHAR(4)
,batch VARCHAR(50) NOT NULL
,qty INT
,kg DECIMAL(12,2)
,ownDocumentYear varchar(4)
,ownDocumentCode VARCHAR(20)
,vt_start VARCHAR(10)
,vt_end VARCHAR(10)
,description VARCHAR(255)
);

CREATE TRIGGER TRG_ITEM_OUT_INSERT_AFT AFTER INSERT
ON ITEM_OUT
FOR EACH ROW
INSERT INTO ITEM_OUT_LOG (
	utctt_start
	,utctt_end
	,opcode
	,idorig
	,cause
	,kind
	,code
	,name
	,producer
	,yearProd
	,batch
	,qty
	,kg
	,ownDocumentYear
	,ownDocumentCode
	,vt_start
	,vt_end
	,description
) VALUES (
	UTC_TIMESTAMP()
	,UTC_TIMESTAMP()
	,'INS'
	,NEW.id
	,NEW.cause
	,NEW.kind
	,NEW.code
	,NEW.name
	,NEW.producer
	,NEW.yearProd
	,NEW.batch
	,NEW.qty
	,NEW.kg
	,NEW.ownDocumentYear
	,NEW.ownDocumentCode
	,NEW.vt_start
	,NEW.vt_end
	,NEW.description
);

delimiter |

CREATE TRIGGER TRG_ITEM_OUT_UPDATE_BFR BEFORE UPDATE
ON ITEM_OUT
FOR EACH ROW
BEGIN
UPDATE ITEM_OUT_LOG SET utctt_end = UTC_TIMESTAMP()
WHERE
(
	(OLD.id = idorig)
	AND
	(utctt_end = utctt_start)
);
INSERT INTO ITEM_OUT_LOG (
	utctt_start
	,utctt_end
	,opcode
	,idorig
	,cause
	,kind
	,code
	,name
	,producer
	,yearProd
	,batch
	,qty
	,kg
	,ownDocumentYear
	,ownDocumentCode
	,vt_start
	,vt_end
	,description
) VALUES (
	UTC_TIMESTAMP()
	,UTC_TIMESTAMP()
	,'UPD'
	,NEW.id
	,NEW.cause
	,NEW.kind
	,NEW.code
	,NEW.name
	,NEW.producer
	,NEW.yearProd
	,NEW.batch
	,NEW.qty
	,NEW.kg
	,NEW.ownDocumentYear
	,NEW.ownDocumentCode
	,NEW.vt_start
	,NEW.vt_end
	,NEW.description
);
END;

|

CREATE TRIGGER TRG_ITEM_OUT_DELETE_BFR BEFORE DELETE
ON ITEM_OUT
FOR EACH ROW
BEGIN
UPDATE ITEM_OUT_LOG SET utctt_end = UTC_TIMESTAMP()
WHERE
(
	(OLD.id = idorig)
	AND
	(utctt_end = utctt_start)
);
INSERT INTO ITEM_OUT_LOG (
	utctt_start
	,utctt_end
	,opcode
	,idorig
	,cause
	,kind
	,code
	,name
	,producer
	,yearProd
	,batch
	,qty
	,kg
	,ownDocumentYear
	,ownDocumentCode
	,vt_start
	,vt_end
	,description
) VALUES (
	UTC_TIMESTAMP()
	,UTC_TIMESTAMP()
	,'DEL'
	,OLD.id
	,OLD.cause
	,OLD.kind
	,OLD.code
	,OLD.name
	,OLD.producer
	,OLD.yearProd
	,OLD.batch
	,OLD.qty
	,OLD.kg
	,OLD.ownDocumentYear
	,OLD.ownDocumentCode
	,OLD.vt_start
	,OLD.vt_end
	,OLD.description
);
END;

|

delimiter ;

CREATE TABLE IF NOT EXISTS BATCH (id BIGINT AUTO_INCREMENT PRIMARY KEY
,batch VARCHAR(50)
,vt_start VARCHAR(10)
,vt_end VARCHAR(10)
,description VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS BATCH_LOG (id BIGINT AUTO_INCREMENT PRIMARY KEY
,utctt_start DATETIME NOT NULL
,utctt_end DATETIME NOT NULL
,opcode VARCHAR(3) NOT NULL DEFAULT 'UNK'
,idorig BIGINT NOT NULL
,batch VARCHAR(50) NOT NULL
,vt_start VARCHAR(10)
,vt_end VARCHAR(10)
,description VARCHAR(255)
);

CREATE TRIGGER TRG_BATCH_INSERT_AFT AFTER INSERT
ON BATCH
FOR EACH ROW
INSERT INTO BATCH_LOG (
	utctt_start
	,utctt_end
	,opcode
	,idorig
	,batch
	,vt_start
	,vt_end
	,description
) VALUES (
	UTC_TIMESTAMP()
	,UTC_TIMESTAMP()
	,'INS'
	,NEW.id
	,NEW.batch
	,NEW.vt_start
	,NEW.vt_end
	,NEW.description
);

delimiter |

CREATE TRIGGER TRG_BATCH_UPDATE_BFR BEFORE UPDATE
ON BATCH
FOR EACH ROW
BEGIN
UPDATE BATCH_LOG SET utctt_end = UTC_TIMESTAMP()
WHERE
(
	(OLD.id = idorig)
	AND
	(utctt_end = utctt_start)
);
INSERT INTO BATCH_LOG (
	utctt_start
	,utctt_end
	,opcode
	,idorig
	,batch
	,vt_start
	,vt_end
	,description
) VALUES (
	UTC_TIMESTAMP()
	,UTC_TIMESTAMP()
	,'UPD'
	,NEW.id
	,NEW.batch
	,NEW.vt_start
	,NEW.vt_end
	,NEW.description
);
END;

|

CREATE TRIGGER TRG_BATCH_DELETE_BFR BEFORE DELETE
ON BATCH
FOR EACH ROW
BEGIN
UPDATE BATCH_LOG SET utctt_end = UTC_TIMESTAMP()
WHERE
(
	(OLD.id = idorig)
	AND
	(utctt_end = utctt_start)
);
INSERT INTO BATCH_LOG (
	utctt_start
	,utctt_end
	,opcode
	,idorig
	,batch
	,vt_start
	,vt_end
	,description
) VALUES (
	UTC_TIMESTAMP()
	,UTC_TIMESTAMP()
	,'DEL'
	,OLD.id
	,OLD.batch
	,OLD.vt_start
	,OLD.vt_end
	,OLD.description
);
END;

|

delimiter ;