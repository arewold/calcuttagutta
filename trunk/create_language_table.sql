
CREATE TABLE `languages` (
  `languageid` int(10) unsigned NOT NULL auto_increment,
  `code` varchar(10) collate latin1_danish_ci NOT NULL default '',
  `name` varchar(100) collate latin1_danish_ci NOT NULL default '',
  PRIMARY KEY  (`languageid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_danish_ci;

mysql> alter table articles add column languageid int(10) unsigned;
Query OK, 6475 rows affected (0.11 sec)
Records: 6475  Duplicates: 0  Warnings: 0


mysql> alter table articles add foreign key (languageid) references languages(la
nguageid);
Query OK, 6475 rows affected (0.14 sec)
Records: 6475  Duplicates: 0  Warnings: 0

