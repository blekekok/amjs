CREATE DATABASE `metaforums` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

use `metaforums`;

CREATE TABLE `MFUsers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(64) NOT NULL,
  `verification_token` varchar(64) DEFAULT NULL,
  `verification_timestamp` datetime DEFAULT NULL,
  `resetpassword_token` varchar(64) DEFAULT NULL,
  `resetpassword_timestamp` datetime DEFAULT NULL,
  `role` varchar(16) NOT NULL DEFAULT 'user',
  `verified` tinyint NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL,
  `lastactivity` datetime DEFAULT NULL,
  `lastlogin` datetime DEFAULT NULL,
  `avatar` varchar(128) DEFAULT '',
  `about` text,
  `email_visibility` tinyint DEFAULT '1',
  `last_username_change` datetime DEFAULT NULL,
  `deleted` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_UNIQUE` (`username`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `email_UNIQUE` (`email`),
  UNIQUE KEY `password_hash_UNIQUE` (`password_hash`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `MFAccChange` (
  `userid` int NOT NULL,
  `token` varchar(64) NOT NULL,
  `new_password` varchar(64) DEFAULT '',
  `new_email` varchar(255) DEFAULT '',
  `delete_account` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `MFGroups` (
  `id` int NOT NULL AUTO_INCREMENT,
  `displayname` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `MFCategories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `displayname` varchar(64) NOT NULL,
  `groupid` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `groupid_idx` (`groupid`),
  CONSTRAINT `groupid` FOREIGN KEY (`groupid`) REFERENCES `MFGroups` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `MFModStatus` (
  `userid` int NOT NULL,
  `categoryid` int NOT NULL,
  `expirydate` datetime NOT NULL,
  `level` int NOT NULL,
  PRIMARY KEY (`userid`,`categoryid`),
  KEY `silencecategoryid_idx` (`categoryid`),
  CONSTRAINT `silencecategoryid` FOREIGN KEY (`categoryid`) REFERENCES `MFCategories` (`id`),
  CONSTRAINT `silenceuserid` FOREIGN KEY (`userid`) REFERENCES `MFUsers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `MFThreads` (
  `id` int NOT NULL AUTO_INCREMENT,
  `categoryid` int NOT NULL,
  `authorid` int NOT NULL,
  `creation_date` datetime NOT NULL,
  `lastactivity` datetime NOT NULL,
  `title` varchar(128) NOT NULL,
  `body` text NOT NULL,
  `locked` tinyint NOT NULL DEFAULT '0',
  `lock_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categoryid_idx` (`categoryid`),
  KEY `author_idx` (`authorid`),
  CONSTRAINT `author` FOREIGN KEY (`authorid`) REFERENCES `MFUsers` (`id`),
  CONSTRAINT `categoryid` FOREIGN KEY (`categoryid`) REFERENCES `MFCategories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `MFThreadLikes` (
  `threadid` int NOT NULL,
  `userid` int NOT NULL,
  `liked_date` datetime DEFAULT NULL,
  PRIMARY KEY (`threadid`,`userid`),
  KEY `threadid_idx` (`threadid`),
  KEY `userid_idx` (`userid`),
  CONSTRAINT `threadlikeid` FOREIGN KEY (`threadid`) REFERENCES `MFThreads` (`id`),
  CONSTRAINT `threadlikeuser` FOREIGN KEY (`userid`) REFERENCES `MFUsers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `MFPosts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `threadid` int NOT NULL,
  `authorid` int NOT NULL,
  `creation_date` datetime NOT NULL,
  `body` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `threadid_idx` (`threadid`),
  KEY `userid_idx` (`authorid`),
  CONSTRAINT `authorid` FOREIGN KEY (`authorid`) REFERENCES `MFUsers` (`id`),
  CONSTRAINT `threadid` FOREIGN KEY (`threadid`) REFERENCES `MFThreads` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `MFPostLikes` (
  `postid` int NOT NULL,
  `userid` int NOT NULL,
  `liked_date` datetime NOT NULL,
  PRIMARY KEY (`postid`,`userid`),
  KEY `postid_idx` (`postid`),
  KEY `userid_idx` (`userid`),
  CONSTRAINT `postid` FOREIGN KEY (`postid`) REFERENCES `MFPosts` (`id`),
  CONSTRAINT `userid` FOREIGN KEY (`userid`) REFERENCES `MFUsers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `MFViews` (
  `threadid` int NOT NULL,
  `userid` int NOT NULL,
  PRIMARY KEY (`threadid`,`userid`),
  KEY `viewthreadid_idx` (`threadid`),
  KEY `viewuserid_idx` (`userid`),
  CONSTRAINT `viewthreadid` FOREIGN KEY (`threadid`) REFERENCES `MFThreads` (`id`),
  CONSTRAINT `viewuserid` FOREIGN KEY (`userid`) REFERENCES `MFUsers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;