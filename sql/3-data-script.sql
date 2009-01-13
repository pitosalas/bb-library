INSERT INTO ApplicationProperty (name, value) VALUES 
	('title', 'Sample Library'),
	('theme', 'two'),
	('login_prompt', 'to manage the library'),
	('news_box_title', 'Latest News'),
	('news_box_items', '5'),
	('partner_site_title', NULL),
	('partner_site_link', NULL),
	('contacts', 'Pito &lt;rps@blogbridge.com&gt; +1 781-646-5894'),
	('tla_box_title', 'Sponsored Links'),
	('tla_apikey', ''),
	('direct_feed_urls', '0'),
	('generate_tags_and_descriptions', '1');
	
# Licensing related properties
# type: NULL or 0 - licensed, 1 - trial (with expiration date)
INSERT INTO ApplicationProperty (name, value) VALUES 
	('app_type', NULL),
	('app_expiration_date', NULL);

INSERT INTO ViewType (id, name) VALUES 
	(1, "List"),
	(2, "Tree");
	
INSERT INTO AccountType (id, name, manage_preferences, manage_users, manage_organizations, manage_tags, edit_content, edit_others_content, do_backups_restores, manage_news, manage_tasks) VALUES
	(0, "Administrator", "Y", "Y", "Y", "Y", "Y", "Y", "Y", "Y", "Y"),
	(1, "Librarian",     "N", "N", "N", "Y", "Y", "Y", "Y", "Y", "N"),
	(2, "Author",        "N", "N", "N", "N", "Y", "N", "N", "N", "N"),
	(3, "Reader",        "N", "N", "N", "N", "N", "N", "N", "N", "N");

INSERT INTO ItemType (id, name) VALUES 
	(1, "Feed"),
	(2, "Podcast"),
	(3, "Outline"),
	(4, "Website");

INSERT INTO Person (id, organization_id, type_id, userName, fullName, email, passwd, description)
	VALUES (1, null, 0, "admin", "Admin", null, "admin", "Administrative account");
INSERT INTO Folder (id, viewType_id, title, description, created)
	VALUES (1, 2, "Home", "Home Folder", 0);
	
INSERT INTO TaskPeriod (id, name, seconds) VALUES 
	(1, 'Every Minute', 60),
	(2, 'Hourly', 3600),
	(3, 'Daily', 86400),
	(4, 'Weekly', 604800),
	(5, 'Monthly', 2592000),
	(6, 'Anually', 31536000);

INSERT INTO Task (name, title, description, min_period_id, period_id) VALUES
	('db_backup', 'Database Backup', 'Full database backup.', 3, 3),
	('opml_folder_check', 'OPML Folders Check', 'Check for OPML folders to update', 1, 1),
	('amazon_cache_clean', 'Amazon AST Cache Cleaning', 'Cleans the cache of Amazon AST', 2, 2),
	('metadata_update', 'Feed Metadata Update', 'Updates ranks and inlinks of feeds', 1, 1),
	('check_for_updates', 'Check for Updates', 'Check for the library software updates', 3, 3),
	('check_links', 'Check Item Links', 'Check item links for validity', 3, 3);
