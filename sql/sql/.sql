ALTER DATABASE CHARACTER SET utf8 COLLATE utf8_unicode_ci;


DROP TABLE IF EXISTS user;
DROP TABLE IF EXISTS profile;
DROP TABLE IF EXISTS article;
DROP TABLE IF EXISTS comment;


CREATE TABLE user (
		userId BINARY(16) NOT NULL,
		userEmail VARCHAR(32) NOT NULL,
		userPassword Binary(16) NOT NULL,
		PRIMARY KEY(userId)
);

CREATE TABLE profile (
	profileId VARCHAR(32) NOT NULL,
	firstName VARCHAR(32),
	lastName VARCHAR(32) NOT NULL,
	PRIMARY KEY(profileId)
	);
CREATE TABLE article (
	articleProfileId VARCHAR(32) NOT NULL,
	articleText VARCHAR(32) NOT NULL,
	article DATETIME(6) NOT NULL,
	INDEX(articleProfileId),
	FOREIGN KEY (articleProfileId) REFERENCES profile (profileId),
	PRIMARY KEY(articleProfileId)
);
CREATE TABLE comment (
	commentUserId VARCHAR(32) NOT NULL,
	commentText VARCHAR(32) NOT NULL,
	commentDate DATETIME(6),
	INDEX(commentUserId),
	FOREIGN KEY(commentUserId) REFERENCES user(userId),
	PRIMARY KEY (commentUserId)
	);








