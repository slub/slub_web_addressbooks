#
# Table structure for table 'tx_slubwebaddressbooks_domain_model_book'
#
CREATE TABLE tx_slubwebaddressbooks_domain_model_book (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	year_string varchar(255) DEFAULT '' NOT NULL,
	year int(11) DEFAULT '0' NOT NULL,
	ppn varchar(255) DEFAULT '' NOT NULL,
	page_behoerdenverzeichnis int(11) DEFAULT '0' NOT NULL,
	page_berufsklassen_und_gewerbe int(11) DEFAULT '0' NOT NULL,
	page_handelsregister int(11) DEFAULT '0' NOT NULL,
	page_genossenschaftsregister int(11) DEFAULT '0' NOT NULL,
	link_map varchar(255) DEFAULT '' NOT NULL,
	link_map_thumb varchar(255) DEFAULT '' NOT NULL,
	order_umlaute varchar(255) DEFAULT '0' NOT NULL,
	order_i_j varchar(255) DEFAULT '' NOT NULL,
	place_id int(11) unsigned DEFAULT '0',
	streets mediumtext NOT NULL,
	persons mediumtext NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,

	sorting int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),

);

#
# Table structure for table 'tx_slubwebaddressbooks_domain_model_place'
#
CREATE TABLE tx_slubwebaddressbooks_domain_model_place (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	place varchar(255) DEFAULT '' NOT NULL,
	lat varchar(255) DEFAULT '' NOT NULL,
	lon varchar(255) DEFAULT '' NOT NULL,
	hov_link varchar(255) DEFAULT '' NOT NULL,
	gndid varchar(255) DEFAULT '' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),
);
