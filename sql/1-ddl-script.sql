CREATE TABLE ItemType (
  id INTEGER UNSIGNED NOT NULL,
  name VARCHAR(25) NOT NULL,
  PRIMARY KEY(id),
  UNIQUE INDEX item_types_name(name)
)
TYPE=InnoDB;

CREATE TABLE Organization (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  title VARCHAR(100) NOT NULL,
  recommendations_folder_id INTEGER UNSIGNED,
  PRIMARY KEY(id),
  INDEX organization_recommendations_folder_id(recommendations_folder_id)
)
TYPE=InnoDB;

CREATE TABLE Tag (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(25) NOT NULL,
  PRIMARY KEY(id),
  UNIQUE INDEX tags_name(name)
)
TYPE=InnoDB;

CREATE TABLE TagsMapping
(
	from_tag	VARCHAR(25) NOT NULL,
	to_tag		VARCHAR(25) NOT NULL,
	UNIQUE INDEX tm (from_tag)
) TYPE=InnoDB;

CREATE TABLE AccountType (
  id INTEGER UNSIGNED NOT NULL,
  name VARCHAR(25) NOT NULL,
  manage_preferences ENUM('N','Y') NOT NULL DEFAULT 'N',
  manage_users ENUM('N','Y') NOT NULL DEFAULT 'N',
  manage_organizations ENUM('N','Y') NOT NULL DEFAULT 'N',
  manage_tags ENUM('N','Y') NOT NULL DEFAULT 'N',
  edit_content ENUM('N','Y') NOT NULL DEFAULT 'N',
  edit_others_content ENUM('N','Y') NOT NULL DEFAULT 'N',
  do_backups_restores ENUM('N','Y') NOT NULL DEFAULT 'N',
  manage_news ENUM('N', 'Y') NOT NULL DEFAULT 'N',
  manage_tasks ENUM('N', 'Y') NOT NULL DEFAULT 'N',
  PRIMARY KEY(id),
  UNIQUE INDEX account_types_name(name)
)
TYPE=InnoDB;

CREATE TABLE ViewType (
  id INTEGER UNSIGNED NOT NULL,
  name VARCHAR(25) NOT NULL,
  PRIMARY KEY(id),
  UNIQUE INDEX view_type_name(name)
)
TYPE=InnoDB;

CREATE TABLE Person (
  id                	INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  organization_id   	INTEGER UNSIGNED NULL,
  type_id           	INTEGER UNSIGNED NOT NULL,
  userName          	VARCHAR(20) NOT NULL,
  fullName          	VARCHAR(25) NOT NULL,
  email             	VARCHAR(200) NULL,
  passwd            	VARCHAR(20) NOT NULL,
  description       	TEXT NULL,
  last_login        	BIGINT NULL,
  home_page         	TEXT NULL,
  license_text      	TEXT NULL,
  license_accepted  	BIGINT NULL,
  no_ads            	BIT NOT NULL DEFAULT 0,
  PRIMARY KEY(id),
  INDEX users_type(type_id),
  INDEX users_organization(organization_id),
  UNIQUE INDEX users_name(userName),
  FOREIGN KEY(type_id)
    REFERENCES AccountType(id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
  FOREIGN KEY(organization_id)
    REFERENCES Organization(id)
      ON DELETE RESTRICT
      ON UPDATE NO ACTION
)
TYPE=InnoDB;

CREATE TABLE Folder (
  id 									INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  viewType_id 				INTEGER UNSIGNED NOT NULL DEFAULT 1,
  owner_id 						INTEGER UNSIGNED NOT NULL DEFAULT 1,
  title 							VARCHAR(100) NOT NULL,
  description 				TEXT NULL,
  created 						BIGINT NOT NULL,
  viewTypeParam 			TEXT NULL,
  opml 								TEXT NULL,
  opml_url 						TEXT NULL,
  opml_user 					TEXT NULL,
  opml_password 			TEXT NULL,
  opml_updates_period BIGINT NOT NULL DEFAULT 0,
  opml_last_updated 	BIGINT NOT NULL DEFAULT 0,
  opml_last_error 		TEXT NULL,
  dynamic 						BIT NOT NULL DEFAULT 0,
  ord 								INT NOT NULL DEFAULT 99999,
  autoTags						BIT NOT NULL DEFAULT 1,
  show_in_nav_bar			BIT NOT NULL DEFAULT 0,
  PRIMARY KEY(id),
  INDEX folder_owner(owner_id),
  INDEX folder_view_type(viewType_id),
  FOREIGN KEY(owner_id)
    REFERENCES Person(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  FOREIGN KEY(viewType_id)
    REFERENCES ViewType(id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
)
TYPE=InnoDB;

CREATE TABLE Bookmark (
  person_id INTEGER UNSIGNED NOT NULL,
  folder_id INTEGER UNSIGNED NOT NULL,
  INDEX bookmarks_user(person_id),
  INDEX bookmarks_folder(folder_id),
  FOREIGN KEY(person_id)
    REFERENCES Person(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  FOREIGN KEY(folder_id)
    REFERENCES Folder(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
)
TYPE=InnoDB;

CREATE TABLE Item (
  id 									INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  type_id 						INTEGER UNSIGNED NOT NULL,
  owner_id 						INTEGER UNSIGNED NOT NULL DEFAULT 1,
  title 							VARCHAR(100) NOT NULL,
  description 				TEXT,
  created 						BIGINT NOT NULL,
  siteUrl 						TEXT,
  dataUrl 						TEXT,
  accessCount 				INTEGER UNSIGNED NOT NULL DEFAULT 0,
  dynamic 						BIT NOT NULL DEFAULT 0,
  technoInlinks 			INT,
  technoRank 					INT,
  lastMetadataUpdate 	BIGINT NOT NULL DEFAULT 0,
  ord 								INT NOT NULL DEFAULT 99999,
  showPreview					BIT NOT NULL DEFAULT 0,
  useITunesURL				BIT NOT NULL DEFAULT 0,
  iTunesURL 					TEXT,
	usePlayButtons			BIT NOT NULL DEFAULT 0,
	checkLastTime       BIGINT NOT NULL DEFAULT 0,
	checkLastLetterTime BIGINT,
	checkCode           INT,
	checkFailureTime    BIGINT,
  autoTags						BIT NOT NULL DEFAULT 1,
	show_in_nav_bar 		BIT NOT NULL DEFAULT 0,
  PRIMARY KEY(id),
  INDEX item_owner(owner_id),
  INDEX item_type(type_id),
  INDEX item_ord(ord),
  FOREIGN KEY(owner_id)
    REFERENCES Person(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  FOREIGN KEY(type_id)
    REFERENCES ItemType(id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
)
TYPE=InnoDB;

CREATE TABLE Folder_Tag (
  folder_id INTEGER UNSIGNED NOT NULL,
  tag_id INTEGER UNSIGNED NOT NULL,
  INDEX folder_tags_folder(folder_id),
  INDEX folder_tags_tag(tag_id),
  FOREIGN KEY(folder_id)
    REFERENCES Folder(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  FOREIGN KEY(tag_id)
    REFERENCES Tag(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
)
TYPE=InnoDB;

CREATE TABLE Folder_Item (
  item_id INTEGER UNSIGNED NOT NULL,
  folder_id INTEGER UNSIGNED NOT NULL,
  INDEX folder_items_item(item_id),
  INDEX folder_items_folder(folder_id),
  FOREIGN KEY(item_id)
    REFERENCES Item(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  FOREIGN KEY(folder_id)
    REFERENCES Folder(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
)
TYPE=InnoDB;

CREATE TABLE Item_Tag (
  item_id INTEGER UNSIGNED NOT NULL,
  tag_id INTEGER UNSIGNED NOT NULL,
  INDEX item_tags_item(item_id),
  INDEX item_tags_tag(tag_id),
  FOREIGN KEY(item_id)
    REFERENCES Item(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  FOREIGN KEY(tag_id)
    REFERENCES Tag(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
)
TYPE=InnoDB;

CREATE TABLE Person_Tag (
  person_id INTEGER UNSIGNED NOT NULL,
  tag_id INTEGER UNSIGNED NOT NULL,
  INDEX person_tags_person(person_id),
  INDEX person_tags_tag(tag_id),
  FOREIGN KEY(person_id)
    REFERENCES Person(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  FOREIGN KEY(tag_id)
    REFERENCES Tag(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
)
TYPE=InnoDB;

CREATE TABLE FolderShortcut (
  parent_id INTEGER UNSIGNED NOT NULL,
  folder_id INTEGER UNSIGNED NOT NULL,
  INDEX folder_parents_folder(folder_id),
  INDEX folder_parents_parent(parent_id),
  FOREIGN KEY(folder_id)
    REFERENCES Folder(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  FOREIGN KEY(parent_id)
    REFERENCES Folder(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
)
TYPE=InnoDB;

CREATE TABLE NewsItem (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  title VARCHAR(250) NOT NULL,
  created BIGINT NOT NULL,
  author_id INTEGER UNSIGNED NOT NULL,
  public TINYINT(1) NOT NULL DEFAULT '1',
  txt TEXT NOT NULL,
  folder_id INTEGER UNSIGNED,
  PRIMARY KEY (id),
  INDEX newsitem_author_id(author_id),
  FOREIGN KEY(author_id)
    REFERENCES Person(id)
      ON DELETE CASCADE
) TYPE=InnoDB;

ALTER TABLE Organization ADD FOREIGN KEY (recommendations_folder_id)
    REFERENCES Folder(id)
      ON DELETE SET NULL;

CREATE TABLE ApplicationProperty (
  name VARCHAR(200) NOT NULL,
  value TEXT,
  UNIQUE INDEX application_property_name (name)
) TYPE=InnoDB;

CREATE TABLE TaskPeriod (
  id INTEGER UNSIGNED NOT NULL,
  name VARCHAR(50) NOT NULL,
  seconds BIGINT NOT NULL,
  PRIMARY KEY(id)
) TYPE=InnoDB;

CREATE TABLE Task (
  name VARCHAR(200) NOT NULL,
  title VARCHAR(100) NOT NULL,
  description TEXT,
  period_id INTEGER UNSIGNED NOT NULL,
  min_period_id INTEGER UNSIGNED NOT NULL,
  last_exec BIGINT NOT NULL DEFAULT '0'
) TYPE=InnoDB;

CREATE TABLE ShortestPaths (
  source_id INTEGER UNSIGNED NOT NULL,
  target_id INTEGER UNSIGNED NOT NULL,
  path TEXT NULL,
  is_source_folder BIT NOT NULL DEFAULT 1,
  INDEX folder_paths_source (source_id),
  INDEX folder_paths_target (target_id),
  FOREIGN KEY(target_id)
    REFERENCES Folder(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) TYPE=InnoDB;

