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

DROP TRIGGER IF EXISTS TRG_DOCUMENT_DENORM_INSERT_AFT;
DROP TRIGGER IF EXISTS TRG_DOCUMENT_DENORM_UPDATE_BFR;
DROP TRIGGER IF EXISTS TRG_DOCUMENT_DENORM_DELETE_BFR;

ALTER TABLE DOCUMENT_DENORM ADD COLUMN country BIGINT AFTER contractor;
ALTER TABLE DOCUMENT_DENORM_LOG ADD COLUMN country BIGINT AFTER contractor;

CREATE TRIGGER TRG_DOCUMENT_DENORM_INSERT_AFT AFTER INSERT
ON DOCUMENT_DENORM
FOR EACH ROW
INSERT INTO DOCUMENT_DENORM_LOG (
	utctt_start
	,utctt_end
	,opcode
	,idorig
	,year
	,kind
	,code
	,contractor_kind
	,contractor_code
	,contractor
	,country
	,date
	,description
) VALUES (
	UTC_TIMESTAMP()
	,UTC_TIMESTAMP()
	,'INS'
	,NEW.id
	,NEW.year
	,NEW.kind
	,NEW.code
	,NEW.contractor_kind
	,NEW.contractor_code
	,NEW.contractor
	,NEW.country
	,NEW.date
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
	,year
	,kind
	,code
	,contractor_kind
	,contractor_code
	,contractor
	,country
	,date
	,description
) VALUES (
	UTC_TIMESTAMP()
	,UTC_TIMESTAMP()
	,'UPD'
	,NEW.id
	,NEW.year
	,NEW.kind
	,NEW.code
	,NEW.contractor_kind
	,NEW.contractor_code
	,NEW.contractor
	,NEW.country
	,NEW.date
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
	,year
	,kind
	,code
	,contractor_kind
	,contractor_code
	,contractor
	,country
	,date
	,vt_start
	,vt_end
	,description
) VALUES (
	UTC_TIMESTAMP()
	,UTC_TIMESTAMP()
	,'DEL'
	,OLD.id
	,OLD.year
	,OLD.kind
	,OLD.code
	,OLD.contractor_kind
	,OLD.contractor_code
	,OLD.contractor
	,OLD.country
	,OLD.date
	,OLD.description
);
END;

|

delimiter ;

DROP TRIGGER IF EXISTS TRG_ITEM_DENORM_INSERT_AFT;
DROP TRIGGER IF EXISTS TRG_ITEM_DENORM_UPDATE_BFR;
DROP TRIGGER IF EXISTS TRG_ITEM_DENORM_DELETE_BFR;

ALTER TABLE ITEM_DENORM ADD COLUMN batch_orig VARCHAR(100) AFTER batch;
ALTER TABLE ITEM_DENORM ADD COLUMN country BIGINT AFTER batch_orig;
ALTER TABLE ITEM_DENORM ADD COLUMN district VARCHAR(50) AFTER country;
ALTER TABLE ITEM_DENORM ADD COLUMN stabCEE VARCHAR(50) AFTER district;
ALTER TABLE ITEM_DENORM ADD COLUMN kg DECIMAL(12,2) AFTER qty;
ALTER TABLE ITEM_DENORM ADD COLUMN arrival VARCHAR(10) VARCHAR(100) AFTER kg;
ALTER TABLE ITEM_DENORM_LOG ADD COLUMN batch_orig VARCHAR(100) AFTER batch;
ALTER TABLE ITEM_DENORM_LOG ADD COLUMN country BIGINT AFTER batch_orig;
ALTER TABLE ITEM_DENORM_LOG ADD COLUMN district VARCHAR(50) AFTER country;
ALTER TABLE ITEM_DENORM_LOG ADD COLUMN stabCEE VARCHAR(50) AFTER district;
ALTER TABLE ITEM_DENORM_LOG ADD COLUMN kg DECIMAL(12,2) AFTER qty;
ALTER TABLE ITEM_DENORM_LOG ADD COLUMN arrival VARCHAR(10) VARCHAR(100) AFTER kg;

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

DROP TRIGGER IF EXISTS TRG_ITEM_OUT_INSERT_AFT;
DROP TRIGGER IF EXISTS TRG_ITEM_OUT_UPDATE_BFR;
DROP TRIGGER IF EXISTS TRG_ITEM_OUT_DELETE_BFR;

ALTER TABLE ITEM_OUT ADD COLUMN kg DECIMAL(12,2) AFTER qty;
ALTER TABLE ITEM_OUT_LOG ADD COLUMN kg DECIMAL(12,2) AFTER qty;

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
	,OLD.description
);
END;

|

delimiter ;

INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('AX','ALA','248','AALAND ISLANDS');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('AF','AFG','004','AFGHANISTAN');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('AL','ALB','008','ALBANIA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('DZ','DZA','012','ALGERIA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('AS','ASM','016','AMERICAN SAMOA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('AD','AND','020','ANDORRA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('AO','AGO','024','ANGOLA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('AI','AIA','660','ANGUILLA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('AQ','ATA','010','ANTARCTICA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('AG','ATG','028','ANTIGUA AND BARBUDA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('AR','ARG','032','ARGENTINA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('AM','ARM','051','ARMENIA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('AW','ABW','533','ARUBA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('AU','AUS','036','AUSTRALIA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('AT','AUT','040','AUSTRIA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('AZ','AZE','031','AZERBAIJAN');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('BS','BHS','044','BAHAMAS');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('BH','BHR','048','BAHRAIN');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('BD','BGD','050','BANGLADESH');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('BB','BRB','052','BARBADOS');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('BY','BLR','112','BELARUS');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('BE','BEL','056','BELGIUM');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('BZ','BLZ','084','BELIZE');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('BJ','BEN','204','BENIN');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('BM','BMU','060','BERMUDA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('BT','BTN','064','BHUTAN');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('BO','BOL','068','BOLIVIA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('BA','BIH','070','BOSNIA AND HERZEGOWINA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('BW','BWA','072','BOTSWANA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('BV','BVT','074','BOUVET ISLAND');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('BR','BRA','076','BRAZIL');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('IO','IOT','086','BRITISH INDIAN OCEAN TERRITORY');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('BN','BRN','096','BRUNEI DARUSSALAM');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('BG','BGR','100','BULGARIA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('BF','BFA','854','BURKINA FASO');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('BI','BDI','108','BURUNDI');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('KH','KHM','116','CAMBODIA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('CM','CMR','120','CAMEROON');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('CA','CAN','124','CANADA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('CV','CPV','132','CAPE VERDE');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('KY','CYM','136','CAYMAN ISLANDS');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('CF','CAF','140','CENTRAL AFRICAN REPUBLIC');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('TD','TCD','148','CHAD');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('CL','CHL','152','CHILE');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('CN','CHN','156','CHINA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('CX','CXR','162','CHRISTMAS ISLAND');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('CC','CCK','166','COCOS (KEELING) ISLANDS');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('CO','COL','170','COLOMBIA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('KM','COM','174','COMOROS');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('CD','COD','180','CONGO, Democratic Republic of (was Zaire)');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('CG','COG','178','CONGO, Republic of');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('CK','COK','184','COOK ISLANDS');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('CR','CRI','188','COSTA RICA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('CI','CIV','384','COTE D''IVOIRE');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('HR','HRV','191','CROATIA (local name: Hrvatska)');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('CU','CUB','192','CUBA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('CY','CYP','196','CYPRUS');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('CZ','CZE','203','CZECH REPUBLIC');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('DK','DNK','208','DENMARK');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('DJ','DJI','262','DJIBOUTI');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('DM','DMA','212','DOMINICA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('DO','DOM','214','DOMINICAN REPUBLIC');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('EC','ECU','218','ECUADOR');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('EG','EGY','818','EGYPT');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('SV','SLV','222','EL SALVADOR');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('GQ','GNQ','226','EQUATORIAL GUINEA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('ER','ERI','232','ERITREA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('EE','EST','233','ESTONIA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('ET','ETH','231','ETHIOPIA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('FK','FLK','238','FALKLAND ISLANDS (MALVINAS)');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('FO','FRO','234','FAROE ISLANDS');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('FJ','FJI','242','FIJI');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('FI','FIN','246','FINLAND');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('FR','FRA','250','FRANCE');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('GF','GUF','254','FRENCH GUIANA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('PF','PYF','258','FRENCH POLYNESIA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('TF','ATF','260','FRENCH SOUTHERN TERRITORIES');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('GA','GAB','266','GABON');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('GM','GMB','270','GAMBIA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('GE','GEO','268','GEORGIA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('DE','DEU','276','GERMANY');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('GH','GHA','288','GHANA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('GI','GIB','292','GIBRALTAR');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('GR','GRC','300','GREECE');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('GL','GRL','304','GREENLAND');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('GD','GRD','308','GRENADA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('GP','GLP','312','GUADELOUPE');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('GU','GUM','316','GUAM');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('GT','GTM','320','GUATEMALA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('GN','GIN','324','GUINEA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('GW','GNB','624','GUINEA-BISSAU');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('GY','GUY','328','GUYANA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('HT','HTI','332','HAITI');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('HM','HMD','334','HEARD AND MC DONALD ISLANDS');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('HN','HND','340','HONDURAS');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('HK','HKG','344','HONG KONG');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('HU','HUN','348','HUNGARY');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('IS','ISL','352','ICELAND');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('IN','IND','356','INDIA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('ID','IDN','360','INDONESIA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('IR','IRN','364','IRAN (ISLAMIC REPUBLIC OF)');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('IQ','IRQ','368','IRAQ');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('IE','IRL','372','IRELAND');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('IL','ISR','376','ISRAEL');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('IT','ITA','380','ITALY');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('JM','JAM','388','JAMAICA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('JP','JPN','392','JAPAN');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('JO','JOR','400','JORDAN');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('KZ','KAZ','398','KAZAKHSTAN');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('KE','KEN','404','KENYA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('KI','KIR','296','KIRIBATI');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('KP','PRK','408','KOREA, DEMOCRATIC PEOPLE''S REPUBLIC OF');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('KR','KOR','410','KOREA, REPUBLIC OF');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('KW','KWT','414','KUWAIT');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('KG','KGZ','417','KYRGYZSTAN');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('LA','LAO','418','LAO PEOPLE''S DEMOCRATIC REPUBLIC');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('LV','LVA','428','LATVIA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('LB','LBN','422','LEBANON');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('LS','LSO','426','LESOTHO');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('LR','LBR','430','LIBERIA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('LY','LBY','434','LIBYAN ARAB JAMAHIRIYA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('LI','LIE','438','LIECHTENSTEIN');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('LT','LTU','440','LITHUANIA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('LU','LUX','442','LUXEMBOURG');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('MO','MAC','446','MACAU');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('MK','MKD','807','MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('MG','MDG','450','MADAGASCAR');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('MW','MWI','454','MALAWI');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('MY','MYS','458','MALAYSIA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('MV','MDV','462','MALDIVES');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('ML','MLI','466','MALI');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('MT','MLT','470','MALTA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('MH','MHL','584','MARSHALL ISLANDS');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('MQ','MTQ','474','MARTINIQUE');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('MR','MRT','478','MAURITANIA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('MU','MUS','480','MAURITIUS');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('YT','MYT','175','MAYOTTE');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('MX','MEX','484','MEXICO');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('FM','FSM','583','MICRONESIA, FEDERATED STATES OF');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('MD','MDA','498','MOLDOVA, REPUBLIC OF');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('MC','MCO','492','MONACO');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('MN','MNG','496','MONGOLIA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('MS','MSR','500','MONTSERRAT');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('MA','MAR','504','MOROCCO');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('MZ','MOZ','508','MOZAMBIQUE');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('MM','MMR','104','MYANMAR');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('NA','NAM','516','NAMIBIA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('NR','NRU','520','NAURU');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('NP','NPL','524','NEPAL');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('NL','NLD','528','NETHERLANDS');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('AN','ANT','530','NETHERLANDS ANTILLES');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('NC','NCL','540','NEW CALEDONIA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('NZ','NZL','554','NEW ZEALAND');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('NI','NIC','558','NICARAGUA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('NE','NER','562','NIGER');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('NG','NGA','566','NIGERIA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('NU','NIU','570','NIUE');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('NF','NFK','574','NORFOLK ISLAND');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('MP','MNP','580','NORTHERN MARIANA ISLANDS');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('NO','NOR','578','NORWAY');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('OM','OMN','512','OMAN');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('PK','PAK','586','PAKISTAN');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('PW','PLW','585','PALAU');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('PS','PSE','275','PALESTINIAN TERRITORY, Occupied');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('PA','PAN','591','PANAMA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('PG','PNG','598','PAPUA NEW GUINEA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('PY','PRY','600','PARAGUAY');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('PE','PER','604','PERU');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('PH','PHL','608','PHILIPPINES');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('PN','PCN','612','PITCAIRN');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('PL','POL','616','POLAND');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('PT','PRT','620','PORTUGAL');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('PR','PRI','630','PUERTO RICO');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('QA','QAT','634','QATAR');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('RE','REU','638','REUNION');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('RO','ROU','642','ROMANIA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('RU','RUS','643','RUSSIAN FEDERATION');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('RW','RWA','646','RWANDA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('SH','SHN','654','SAINT HELENA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('KN','KNA','659','SAINT KITTS AND NEVIS');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('LC','LCA','662','SAINT LUCIA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('PM','SPM','666','SAINT PIERRE AND MIQUELON');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('VC','VCT','670','SAINT VINCENT AND THE GRENADINES');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('WS','WSM','882','SAMOA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('SM','SMR','674','SAN MARINO');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('ST','STP','678','SAO TOME AND PRINCIPE');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('SA','SAU','682','SAUDI ARABIA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('SN','SEN','686','SENEGAL');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('CS','SCG','891','SERBIA AND MONTENEGRO');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('SC','SYC','690','SEYCHELLES');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('SL','SLE','694','SIERRA LEONE');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('SG','SGP','702','SINGAPORE');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('SK','SVK','703','SLOVAKIA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('SI','SVN','705','SLOVENIA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('SB','SLB','090','SOLOMON ISLANDS');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('SO','SOM','706','SOMALIA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('ZA','ZAF','710','SOUTH AFRICA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('GS','SGS','239','SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('ES','ESP','724','SPAIN');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('LK','LKA','144','SRI LANKA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('SD','SDN','736','SUDAN');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('SR','SUR','740','SURINAME');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('SJ','SJM','744','SVALBARD AND JAN MAYEN ISLANDS');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('SZ','SWZ','748','SWAZILAND');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('SE','SWE','752','SWEDEN');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('CH','CHE','756','SWITZERLAND');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('SY','SYR','760','SYRIAN ARAB REPUBLIC');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('TW','TWN','158','TAIWAN');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('TJ','TJK','762','TAJIKISTAN');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('TZ','TZA','834','TANZANIA, UNITED REPUBLIC OF');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('TH','THA','764','THAILAND');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('TL','TLS','626','TIMOR-LESTE');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('TG','TGO','768','TOGO');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('TK','TKL','772','TOKELAU');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('TO','TON','776','TONGA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('TT','TTO','780','TRINIDAD AND TOBAGO');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('TN','TUN','788','TUNISIA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('TR','TUR','792','TURKEY');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('TM','TKM','795','TURKMENISTAN');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('TC','TCA','796','TURKS AND CAICOS ISLANDS');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('TV','TUV','798','TUVALU');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('UG','UGA','800','UGANDA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('UA','UKR','804','UKRAINE');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('AE','ARE','784','UNITED ARAB EMIRATES');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('GB','GBR','826','UNITED KINGDOM');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('US','USA','840','UNITED STATES');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('UM','UMI','581','UNITED STATES MINOR OUTLYING ISLANDS');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('UY','URY','858','URUGUAY');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('UZ','UZB','860','UZBEKISTAN');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('VU','VUT','548','VANUATU');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('VA','VAT','336','VATICAN CITY STATE (HOLY SEE)');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('VE','VEN','862','VENEZUELA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('VN','VNM','704','VIET NAM');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('VG','VGB','092','VIRGIN ISLANDS (BRITISH)');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('VI','VIR','850','VIRGIN ISLANDS (U.S.)');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('WF','WLF','876','WALLIS AND FUTUNA ISLANDS');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('EH','ESH','732','WESTERN SAHARA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('YE','YEM','887','YEMEN');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('ZM','ZMB','894','ZAMBIA');
INSERT INTO COUNTRY (codealpha2, codealpha3, number, enname) values ('ZW','ZWE','716','ZIMBABWE');

UPDATE DBVERSION SET version='002' WHERE (version='001');