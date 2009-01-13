ALTER TABLE Folder
  ADD show_in_nav_bar BIT NOT NULL DEFAULT 0;

ALTER TABLE Item
  ADD show_in_nav_bar BIT NOT NULL DEFAULT 0;

CREATE TABLE TagsMapping
(
  from_tag  VARCHAR(25) NOT NULL,
  to_tag    VARCHAR(25) NOT NULL,
  UNIQUE INDEX tm (from_tag)
) TYPE=InnoDB;