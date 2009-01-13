INSERT INTO ApplicationProperty (name, value) VALUES 
	('tla_box_title', 'Sponsored Links'),
	('tla_apikey', '');
ALTER TABLE Person
  ADD COLUMN no_ads BIT NOT NULL DEFAULT 0;
	