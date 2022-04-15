CREATE TABLE tx_newt_domain_model_method (
	endpoint int(11) unsigned DEFAULT '0' NOT NULL,
	type varchar(255) NOT NULL DEFAULT '',
	beusergroups int(11) unsigned NOT NULL DEFAULT '0',
	feusergroups int(11) unsigned NOT NULL DEFAULT '0'
);

CREATE TABLE tx_newt_method_backendgroups_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid_local,uid_foreign),
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

CREATE TABLE tx_newt_method_frontendgroups_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid_local,uid_foreign),
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

CREATE TABLE tx_newt_domain_model_endpoint (
	name varchar(255) NOT NULL DEFAULT '',
	description text NOT NULL DEFAULT '',
	endpoint_class varchar(255) NOT NULL DEFAULT '',
	options int(11) unsigned NOT NULL DEFAULT '0',
	methods int(11) unsigned NOT NULL DEFAULT '0',
	page_uid varchar(11) NOT NULL DEFAULT ''
);

CREATE TABLE tx_newt_domain_model_endpointoption (
	endpoint int(11) unsigned DEFAULT '0' NOT NULL,
	option_name varchar(255) NOT NULL DEFAULT '',
	option_value text NOT NULL DEFAULT '',
);

CREATE TABLE tx_newt_domain_model_notification (
	title varchar(255) NOT NULL DEFAULT '',
	message text NOT NULL DEFAULT '',
	send_datetime int(11) DEFAULT '0' NOT NULL,
	result_datetime int(11) DEFAULT '0' NOT NULL,
	result text NOT NULL DEFAULT '',
	is_topic tinyint(4) unsigned DEFAULT '0' NOT NULL,
	beusers int(11) unsigned NOT NULL DEFAULT '0',
	feusers int(11) unsigned NOT NULL DEFAULT '0',
	beusergroups int(11) unsigned NOT NULL DEFAULT '0',
	feusergroups int(11) unsigned NOT NULL DEFAULT '0'
);

CREATE TABLE tx_newt_notification_beusers_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid_local,uid_foreign),
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

CREATE TABLE tx_newt_notification_feusers_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid_local,uid_foreign),
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

CREATE TABLE tx_newt_notification_backendgroups_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid_local,uid_foreign),
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

CREATE TABLE tx_newt_notification_frontendgroups_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid_local,uid_foreign),
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

CREATE TABLE be_users (
	tx_newt_token varchar(100) DEFAULT '' NOT NULL,
	tx_newt_token_issued int(11) DEFAULT '0' NOT NULL
);

CREATE TABLE fe_users (
	tx_newt_token varchar(100) DEFAULT '' NOT NULL,
	tx_newt_token_issued int(11) DEFAULT '0' NOT NULL
);
