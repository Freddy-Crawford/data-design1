ALTER DATABASE fcrawford CHARACTER SET utf8 COLLATE utf8_unicode_ci;


DROP TABLE IF EXISTS comment;
DROP TABLE IF EXISTS article;
DROP TABLE IF EXISTS profile;
DROP TABLE IF EXISTS user;


CREATE TABLE user (
		userId BINARY(16) NOT NULL,
		userEmail VARCHAR(32) NOT NULL,
		userPassword Binary(16) NOT NULL,
		PRIMARY KEY(userId)
);

CREATE TABLE profile (
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
	commentUserId BINARY(16) NOT NULL,
	commentContent VARCHAR(32) NOT NULL,
	commentDate DATETIME(6),
	INDEX(commentId),
	FOREIGN KEY(commentUserId) REFERENCES user(userId),
	PRIMARY KEY (commentId)
	);







