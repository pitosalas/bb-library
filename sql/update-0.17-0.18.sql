ALTER TABLE Item
  ADD checkLastTime BIGINT NOT NULL DEFAULT 0,
  ADD checkLastLetterTime BIGINT,
	ADD checkCode INT,
	ADD checkFailureTime BIGINT;

INSERT INTO Task (name, title, description, min_period_id, period_id) VALUES
	('check_links', 'Check Item Links', 'Check item links for validity', 3, 3);

ALTER TABLE NewsItem
  ADD folder_id INTEGER UNSIGNED;
