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
	user_id Int NOT NULL,
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
	content_id Int NOT NULL,
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
	trans_id Int NOT NULL,
	user_id Int NOT NULL,
	date Char(10) NOT NULL,
	time Char(8) NOT NULL,
	amount Int NOT NULL,
	description Text,
 Primary Key (trans_id)) ENGINE = MyISAM;

Create table content_type (
	type_id Int NOT NULL,
	type_name Char(255) NOT NULL,
 Primary Key (type_id)) ENGINE = MyISAM;

Create table expand_content_define (
	expand_id Int NOT NULL,
	expand_name Char(255) NOT NULL,
	type_id Int NOT NULL,
	is_ai_feature Bool,
 Primary Key (expand_id)) ENGINE = MyISAM;

Create table expand_content (
	content_id Int NOT NULL,
	expand_content Text NOT NULL,
	expand_id Int NOT NULL,
 Primary Key (content_id,expand_id)) ENGINE = MyISAM;

Create table images (
	image_id Int NOT NULL,
	image_url Char(255) NOT NULL,
	content_id Int NOT NULL,
 Primary Key (image_id)) ENGINE = MyISAM;

Create table role_define (
	role_id Int NOT NULL,
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


/* Users permissions */


