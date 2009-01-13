INSERT INTO Folder (id, viewType_id, title, description, created) VALUES
	(2, 1, "Music", "Music collection", 0),
	(3, 1, "Video", "Video collection", 0),
	(4, 1, "Classics", "Classics of the genre", 0),
	(5, 1, "Hits", "Hit items in the category", 0);

INSERT INTO Tag (id, name) VALUES
	(1, "music"),
	(2, "interesting"),
	(3, "video"),
	(4, "classics");

INSERT INTO Folder_Tag (tag_id, folder_id) VALUES
	(1, 2), (2, 2),
	(2, 3), (3, 3),
	(4, 4);

INSERT INTO FolderShortcut(parent_id, folder_id)
	VALUES (1, 2), (1, 3), (4, 3), (1, 4), (1, 5);
	