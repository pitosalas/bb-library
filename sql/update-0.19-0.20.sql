ALTER TABLE Item
  ADD autoTags						BIT NOT NULL DEFAULT 1;

ALTER TABLE Folder
	ADD autoTags						BIT NOT NULL DEFAULT 1;

UPDATE Folder SET autoTags = 0 WHERE ID IN (SELECT DISTINCT(folder_id) FROM Folder_Tag);
UPDATE Item SET autoTags = 0 WHERE ID IN (SELECT DISTINCT(item_id) FROM Item_Tag);
