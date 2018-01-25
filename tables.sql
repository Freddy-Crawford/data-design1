ALTER DATABASE fcrawford CHARACTER SET utf8 COLLATE utf8_unicode_ci;


DROP TABLE IF EXISTS comment;
DROP TABLE IF EXISTS article;
DROP TABLE IF EXISTS user;
DROP TABLE IF EXISTS profile;


CREATE TABLE profile (
		profileId BINARY(16) NOT NULL,
		Email VARCHAR(32) NOT NULL,
		Password Binary(16) NOT NULL,
		PRIMARY KEY(profileId)
);

CREATE TABLE user (
	profileId BINARY(16) NOT NULL,
	firstName VARCHAR(32),
	lastName VARCHAR(32) NOT NULL,
	PRIMARY KEY(profileId)
	);
CREATE TABLE article (
	articleId BINARY(16) NOT NULL,
	articleContent VARCHAR(32) NOT NULL,
	article DATETIME(6) NOT NULL,
	INDEX(articleId),
	FOREIGN KEY (articleProfileId) REFERENCES profile (profileId),
	PRIMARY KEY(articleId)
);
CREATE TABLE comment (
	commentId BINARY(16) NOT NULL,
	commentprofileId BINARY(16) NOT NULL,
	commentContent VARCHAR(32) NOT NULL,
	commentDate DATETIME(6),
	INDEX(commentId),
	FOREIGN KEY(commentProfileId) REFERENCES profile(profileId),
	PRIMARY KEY (commentId)
	);