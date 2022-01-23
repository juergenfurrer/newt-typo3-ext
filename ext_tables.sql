CREATE TABLE tx_newt_domain_model_method (
	endpoint int(11) unsigned DEFAULT '0' NOT NULL,
	type varchar(255) NOT NULL DEFAULT '',
	users int(11) unsigned NOT NULL DEFAULT '0',
	usergroups int(11) unsigned NOT NULL DEFAULT '0'
);

CREATE TABLE tx_newt_domain_model_endpoint (
	name varchar(255) NOT NULL DEFAULT '',
	description text NOT NULL DEFAULT '',
	endpoint_class varchar(255) NOT NULL DEFAULT '',
	methods int(11) unsigned NOT NULL DEFAULT '0',
	page_uid varchar(11) NOT NULL DEFAULT ''
);

CREATE TABLE be_users (
	tx_newt_token varchar(100) DEFAULT '' NOT NULL,
	tx_newt_token_issued int(11) DEFAULT '0' NOT NULL
);
