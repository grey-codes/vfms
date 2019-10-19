CREATE TABLE IF NOT EXISTS users (
  userid int NOT NULL AUTO_INCREMENT,
  username varchar(20) NOT NULL,
  passhash char(128) NOT NULL,/*sha512*/
  PRIMARY KEY (userid)
) ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS files (
  fileid int NOT NULL AUTO_INCREMENT,
  filepath text NOT NULL,
  ownerid int NOT NULL,
  unixperm smallint NOT NULL,
  PRIMARY KEY (fileid),
  FOREIGN KEY (ownerid) REFERENCES users(userid) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB;

