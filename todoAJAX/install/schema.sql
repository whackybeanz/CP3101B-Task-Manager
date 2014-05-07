CREATE TABLE APPUSER (
	USERNAME VARCHAR(20),
	PASSWORD VARCHAR(32),
	BIRTHDAY VARCHAR(12),
	EMAIL VARCHAR(50),
	LAST_TASK_ID INT
);

CREATE TABLE TASKS (
	ID INT,
	USERNAME VARCHAR(20),
	TASK_NAME VARCHAR(255),
	HOURS INT,
	MINS INT,
	UNITS INT,
	UNITS_DONE INT,
	DONE BOOLEAN DEFAULT FALSE,
	NOTES TEXT
);

--Recommended to use the following username and password:
--username: asdf
--password: asdfg
INSERT INTO APPUSER ( USERNAME, PASSWORD, BIRTHDAY, EMAIL, LAST_TASK_ID)
	VALUES
	('asdf', '040b7cf4a55014e185813e0644502ea9', '1991-12-02', 'asdfg@email.com', 18), 
	('test', '098f6bcd4621d373cade4e832627b4f6', '2000-06-08', 'test@gmail.com', 1),
	('good', '755f85c2723bb39381c7379a604160d8', '1996-07-11', 'good@good.com', 3)
;

INSERT INTO TASKS (ID, USERNAME, TASK_NAME, HOURS, MINS, UNITS, UNITS_DONE, DONE, NOTES)
	VALUES
	(1, 'asdf', 'Celebrate birthday @ Mike''s House with Gracie and Joseph', 12, 0, 24, 24, 't', 'Pick Gracie at her house then go grab Joseph'),
	(2, 'asdf', 'Cycling Trip', 10, 0, 12, 0, 'f', ''),
	(3, 'asdf', 'Read Comics', 23, 30, 47, 0, 'f', ''),
	(4, 'asdf', 'Sleep', 08, 0, 16, 0, 'f', ''),
	(5, 'asdf', 'Movie Marathon', 24, 0, 48, 0, 'f', 'Extremely\r\nLong\r\nMarathon\r\nHere'),
	(6, 'asdf', 'Go Swimming', 1, 0, 2, 0, 'f', ''),
	(7, 'asdf', 'Study for CS2105', 3, 30, 7, 0, 'f', ''),
	(8, 'asdf', 'Study for PL1101E Test', 06, 0, 12, 0, 'f', 'I''m so dead.'),
	(9, 'good', 'Concert', 07, 0, 14, 0, 'f', ''),
	(10, 'good', 'Placeholder 1', 08, 0, 16, 0, 'f', ''),
	(11, 'asdf', 'Placeholder 2', 02, 0, 4, 4, 't', 'test'),
	(12, 'asdf', 'Some Tasks', 11, 0, 22, 22, 't', ''),
	(13, 'asdf', 'More Tasks', 13, 0, 26, 26, 't', 'new notes'),
	(14, 'asdf', 'Even More Tasks', 12, 30, 25, 25, 't', ''),
	(15, 'asdf', 'Many More Tasks', 08, 0, 16, 16, 't', 'please\r\nwork'),
	(16, 'asdf', 'Final Two Tasks', 07, 30, 15, 15, 't', 'New notes too.'),
	(17, 'asdf', 'Last Task', 11, 0, 22, 22, 't', 'some notes\r\nhere')
;