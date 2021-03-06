/*
Created		5/2/2018
Modified		6/3/2018
Project		
Model		
Company		
Author		
Version		
Database		mySQL 5 
*/


drop table IF EXISTS roles_on_type;
drop table IF EXISTS role_define;
drop table IF EXISTS images;
drop table IF EXISTS expand_content;
drop table IF EXISTS expand_content_define;
drop table IF EXISTS content_type;
drop table IF EXISTS transaction;
drop table IF EXISTS content;
drop table IF EXISTS member;


Create table member (
	user_id Int NOT NULL AUTO_INCREMENT,
	name Char(255) NOT NULL,
	phone Char(10) NOT NULL,
	email Char(255) NOT NULL,
	password Char(255) NOT NULL,
	avatar Char(255),
	address Char(255),
	role_id Int NOT NULL,
	balance Int NOT NULL DEFAULT 0,
	is_mod Bool NOT NULL DEFAULT false,
	validated Bool NOT NULL DEFAULT false,
	session Char(7),
	UNIQUE (user_id),
	UNIQUE (phone),
	UNIQUE (email),
 Primary Key (user_id)) ENGINE = MyISAM;

Create table content (
	content_id Int NOT NULL AUTO_INCREMENT,
	title Char(255) NOT NULL,
	content Text NOT NULL,
	address Char(255) NOT NULL,
	stretch Int,
	price Int,
	avatar Text,
	priority Int NOT NULL,
	status Int NOT NULL,
	date Char(10) NOT NULL,
	expiredate Char(10) NOT NULL,
	user_id Int NOT NULL,
	type_id Int NOT NULL,
	UNIQUE (content_id),
 Primary Key (content_id)) ENGINE = MyISAM;

Create table transaction (
	trans_id Int NOT NULL AUTO_INCREMENT,
	user_id Int NOT NULL,
	date Char(10) NOT NULL,
	time Char(8) NOT NULL,
	amount Int NOT NULL,
	description Text,
 Primary Key (trans_id)) ENGINE = MyISAM;

Create table content_type (
	type_id Int NOT NULL AUTO_INCREMENT,
	type_name Char(255) NOT NULL,
 Primary Key (type_id)) ENGINE = MyISAM;

Create table expand_content_define (
	expand_id Int NOT NULL AUTO_INCREMENT,
	expand_name Char(255) NOT NULL,
	type_id Int NOT NULL,
	measure_unit Char(20) NOT NULL,
	is_ai_feature Bool,
 Primary Key (expand_id)) ENGINE = MyISAM;

Create table expand_content (
	content_id Int NOT NULL AUTO_INCREMENT,
	expand_content Text NOT NULL,
	expand_id Int NOT NULL,
 Primary Key (content_id,expand_id)) ENGINE = MyISAM;

Create table images (
	image_id Int NOT NULL AUTO_INCREMENT,
	image_url Char(255) NOT NULL,
	content_id Int NOT NULL,
 Primary Key (image_id)) ENGINE = MyISAM;

Create table role_define (
	role_id Int NOT NULL AUTO_INCREMENT,
	name Char(255) NOT NULL,
 Primary Key (role_id)) ENGINE = MyISAM;

Create table roles_on_type (
	role_id Int NOT NULL,
	type_id Int NOT NULL,
	role_code Int NOT NULL,
 Primary Key (role_id,type_id)) ENGINE = MyISAM;


Alter table transaction add Foreign Key (user_id) references member (user_id) on delete  restrict on update  restrict;
Alter table content add Foreign Key (user_id) references member (user_id) on delete  restrict on update  restrict;
Alter table expand_content add Foreign Key (content_id) references content (content_id) on delete  restrict on update  restrict;
Alter table images add Foreign Key (content_id) references content (content_id) on delete  restrict on update  restrict;
Alter table expand_content_define add Foreign Key (type_id) references content_type (type_id) on delete  restrict on update  restrict;
Alter table content add Foreign Key (type_id) references content_type (type_id) on delete  restrict on update  restrict;
Alter table roles_on_type add Foreign Key (type_id) references content_type (type_id) on delete  restrict on update  restrict;
Alter table expand_content add Foreign Key (expand_id) references expand_content_define (expand_id) on delete  restrict on update  restrict;
Alter table member add Foreign Key (role_id) references role_define (role_id) on delete  restrict on update  restrict;
Alter table roles_on_type add Foreign Key (role_id) references role_define (role_id) on delete  restrict on update  restrict;

ALTER TABLE member MODIFY COLUMN name VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE member MODIFY COLUMN address VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE content MODIFY COLUMN title VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE content MODIFY COLUMN content VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE content MODIFY COLUMN address VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE transaction MODIFY COLUMN description VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE content_type MODIFY COLUMN type_name VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE expand_content_define MODIFY COLUMN expand_name VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE expand_content_define MODIFY COLUMN measure_unit VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE expand_content MODIFY COLUMN expand_content VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE role_define MODIFY COLUMN name VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;


/* Users permissions */


/* Add dada */
INSERT INTO `member` (`user_id`, `name`, `phone`, `email`, `password`, `avatar`, `address`, `role_id`, `balance`, `is_mod`, `validated`, `session`) VALUES
(1, 'Thanh Trúc', '0123456789', 'thanhtruc95@gmail.com', 'e807f1fcf82d132f9bb018ca6738a19f', '', 'Biên Hòa, Đồng Nai', 1, 0, 0, 0, 'kbRyfG9'),
(2, 'Ngọc Duy', '0981815431', 'ngocduy1842@gmail.com', 'e807f1fcf82d132f9bb018ca6738a19f', '', 'Trảng Bom, Đồng Nai', 1, 0, 1, 1, 'QT5yPGv');

INSERT INTO `content_type` (`type_id`, `type_name`) VALUES
(1, 'Nhà bán'),
(2, 'Nhà cho thuê');

INSERT INTO `expand_content_define` (`expand_id`, `expand_name`, `type_id`, `measure_unit`, `is_ai_feature`) VALUES
(1, 'Số tầng', 1, 'tầng', 0),
(2, 'Số phòng ngủ', 1, 'phòng', 0),
(3, 'Số WC', 1, 'phòng', 0),
(4, 'Rộng mặt tiền', 1, 'm', 0);

INSERT INTO `content` (`content_id`, `title`, `content`, `address`, `stretch`, `price`, `avatar`, `priority`, `status`, `date`, `expiredate`, `user_id`, `type_id`) VALUES
(1, 'CHÍNH CHỦ BÁN NHÀ, MẶT TIỀN LÊ VĂN SỸ, QUẬN 3', 'Q2jDrW5oIGNo4bunIGLDoW4gbmjDoCBt4bq3dCB0aeG7gW4gxJHGsOG7nW5nIEzDqiBWxINuIFPhu7ksIFBoxrDhu51uZyAxMywgUXXhuq1uIDMuDQpEVDogNCB4MTYuNW0gdnXDtG5nIHbhu6ljIGtow7RuZyBs4buZIGdp4bubaS4NCkvhur90IGPhuqV1IDEgdHLhu4d0LCAyIGzhuqd1LCBzw6JuIHRoxrDhu6NuZy4NClbhu4sgdHLDrSB0dXnhu4d0IMSR4bq5cCBn4bqnbiBjaOG7oyBOZ3V54buFbiBWxINuIFRy4buXaSwga2h1IHBo4buRIGtpbmggZG9hbmggYnXDtG4gYsOhbiBz4bqnbSB14bqldCwNCk7GoWkgbMO9IHTGsOG7n25nIMSR4buDIOG7nywga2luaCBkb2FuaCB2w6BuZyBi4bqhYyDEkcOhIHF1w70sIHRo4budaSB0cmFuZy4NCkhheSBraW5oIGRvYW5oIGPDoWMgbmfDoG5oIG5naOG7gSBzYW5nIHRy4buNbmcuDQpIb+G6t2MgxJHhuqd1IHTGsCBy4bqldCBoaeG7h3UgcXXhuqMuDQpHacOhIGLDoW4gY2jhu4kgMjEsMyB04bu3ICh0aMawxqFuZyBsxrDhu6NuZykuDQpMSDogMDkxNiAxODggNzcyIC0gMDk2MyAxOTEgNDk5IGfhurdwIFF1eeG6v3QuIA==', 'Đường Lê Văn Sỹ, Phường 13, Quận 3, Hồ Chí Minh', 64, 2130, './images/HvoVewA.png', 1, 1, '04/06/2018', '11/06/2018', 2, 1),
(2, 'Nhà MT Lê văn Sỹ, Quận 3, DT: 4,2 x 13m góc 3MT, 1 lầu, 17,5 tỷ', 'R2lhIMSRw6xuaCBjw7Mgdmnhu4djIGPhuqduIGLDoW4gZ+G6pXAgbmjDoCBNVCBMw6ogVsSDbiBT4bu5LCBn4bqnbiBuZ8OjIDQgVHLhuqduIFF1YW5nIERp4buHdSwgUXXhuq1uIDMuIEtodSBwaOG7kSBLRCBzYW5nIHRy4buNbmcsIHPhuqVtIHXhuqV0LiBEVDogNCwyeDEzbSBjw7MgaOG6u20gaMO0bmcgaOG6v3QgbOG7mSBnaeG7m2ksIGzhu4EgxJHGsOG7nW5nIHLhu5luZyA1bS4gTmjDoCBr4bq/dCBj4bqldSAxIGzhuqd1LCDEkWFuZyBjaG8gdGh1w6ogNDAgdHIvIHRow6FuZy4gR2nDoSBiw6FuIDE3LDUgdOG7ty4gWGVtIG5ow6AgTEggMDkxODE4IDkzOTYgQS4gUXVhbmcuIA==', 'Đường Lê Văn Sỹ, Phường 13, Quận 3, Hồ Chí Minh', 55, 17500, './images/C5fvMQA.png', 1, 1, '01/06/2018', '15/06/2018', 2, 1);

INSERT INTO `expand_content` (`content_id`, `expand_content`, `expand_id`) VALUES
(1, '4', 1),
(1, '5', 2),
(2, '5', 1),
(2, '4.5', 4);

INSERT INTO `images` (`image_id`, `image_url`, `content_id`) VALUES
(1, './images/WmU7hha.png', 1),
(2, './images/uGVws2y.png', 1),
(3, './images/AZBdDF6.png', 2),
(4, './images/5uOPQI2.png', 2);

