
/* Drop Tables */

DROP TABLE reservation_personal CASCADE CONSTRAINTS;




/* Create Tables */

CREATE TABLE reservation_personal
(
	reservation_no varchar2(14) NOT NULL,
	customer_name varchar2(15) NOT NULL,
	customer_kana varchar2(20) NOT NULL,
	mobile_tel varchar2(11),
	house_tel varchar2(11),
	created_at date NOT NULL,
	updated_at date NOT NULL,
	PRIMARY KEY (reservation_code)
);
