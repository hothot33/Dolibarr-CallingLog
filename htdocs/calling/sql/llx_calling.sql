-- --------------------------------------------------------

--
-- Table structure for table llx_calling
--

CREATE TABLE IF NOT EXISTS llx_calling (
  rowid int(11) NOT NULL AUTO_INCREMENT,
  mode tinyint(1) NOT NULL DEFAULT '1',
  fk_user_id int(11) NOT NULL,
  call_to varchar(15) NOT NULL,
  call_from varchar(15) NOT NULL,
  call_to_user_id int(11) NOT NULL,
  time_init timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  time_connect timestamp NULL DEFAULT NULL,
  time_release timestamp NULL DEFAULT NULL,
  data tinytext NOT NULL,
  PRIMARY KEY (rowid)
)  ENGINE=innodb;   ;
