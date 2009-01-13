INSERT INTO Item (id, type_id, title, description, created, siteUrl, dataUrl) VALUES
	(1, 1, "Pito's Blog", "Some stuff I just figured out", 0, "http://www.salas.com/", "http://feeds.feedburner.com/PitosBlog"),
	(2, 1, "Noizzze", "What you see is whay you believe", 0, "http://blog.noizeramp.com/", "http://feeds.feedburner.com/noizZze");

INSERT INTO Item_Tag (item_id, tag_id) VALUES 
	(1, 1), (2, 2), (2, 3);

INSERT INTO Folder_Item (folder_id, item_id) VALUES 
	(1, 1), (2, 1), (3, 2), (4, 2);
