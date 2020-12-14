-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Wersja serwera:               10.4.16-MariaDB - mariadb.org binary distribution
-- Serwer OS:                    Win64
-- HeidiSQL Wersja:              10.3.0.5771
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Zrzut struktury tabela fb-clone.fileManager
CREATE TABLE IF NOT EXISTS `fileManager` (
  `idFileManager` int(11) NOT NULL AUTO_INCREMENT,
  `fileName` longtext NOT NULL,
  `extension` longtext NOT NULL,
  `path` longtext NOT NULL,
  `createDate` longtext NOT NULL,
  `parent` int(11) NOT NULL DEFAULT 0,
  `type` longtext NOT NULL,
  `isMinified` int(11) NOT NULL DEFAULT 0,
  `miniPath` longtext DEFAULT NULL,
  PRIMARY KEY (`idFileManager`)
) ENGINE=InnoDB AUTO_INCREMENT=1672 DEFAULT CHARSET=utf8mb4;

-- Zrzucanie danych dla tabeli fb-clone.fileManager: ~0 rows (około)
DELETE FROM `fileManager`;
/*!40000 ALTER TABLE `fileManager` DISABLE KEYS */;
/*!40000 ALTER TABLE `fileManager` ENABLE KEYS */;

-- Zrzut struktury tabela fb-clone.friendList
CREATE TABLE IF NOT EXISTS `friendList` (
  `idUser` int(11) DEFAULT NULL,
  `idFriend` int(11) DEFAULT NULL,
  `friendStatus` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Zrzucanie danych dla tabeli fb-clone.friendList: ~0 rows (około)
DELETE FROM `friendList`;
/*!40000 ALTER TABLE `friendList` DISABLE KEYS */;
/*!40000 ALTER TABLE `friendList` ENABLE KEYS */;

-- Zrzut struktury tabela fb-clone.friendStatus
CREATE TABLE IF NOT EXISTS `friendStatus` (
  `idFriendStatus` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL DEFAULT 0,
  `statusName` varchar(50) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idFriendStatus`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

-- Zrzucanie danych dla tabeli fb-clone.friendStatus: ~3 rows (około)
DELETE FROM `friendStatus`;
/*!40000 ALTER TABLE `friendStatus` DISABLE KEYS */;
INSERT INTO `friendStatus` (`idFriendStatus`, `status`, `statusName`) VALUES
	(1, 0, 'Pending'),
	(2, 1, 'Accepted'),
	(3, 3, 'Blocked');
/*!40000 ALTER TABLE `friendStatus` ENABLE KEYS */;

-- Zrzut struktury tabela fb-clone.postComments
CREATE TABLE IF NOT EXISTS `postComments` (
  `idComment` int(11) NOT NULL AUTO_INCREMENT,
  `idCommentOwner` int(11) DEFAULT NULL,
  `comment` longtext DEFAULT NULL,
  `likes` longtext DEFAULT NULL,
  PRIMARY KEY (`idComment`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Zrzucanie danych dla tabeli fb-clone.postComments: ~0 rows (około)
DELETE FROM `postComments`;
/*!40000 ALTER TABLE `postComments` DISABLE KEYS */;
/*!40000 ALTER TABLE `postComments` ENABLE KEYS */;

-- Zrzut struktury tabela fb-clone.posts
CREATE TABLE IF NOT EXISTS `posts` (
  `idPost` int(11) NOT NULL AUTO_INCREMENT,
  `idPostOwner` int(11) DEFAULT NULL,
  `post` longtext DEFAULT NULL,
  `likes` longtext DEFAULT NULL,
  PRIMARY KEY (`idPost`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Zrzucanie danych dla tabeli fb-clone.posts: ~0 rows (około)
DELETE FROM `posts`;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;

-- Zrzut struktury tabela fb-clone.privacyLevel
CREATE TABLE IF NOT EXISTS `privacyLevel` (
  `idLevel` int(11) NOT NULL AUTO_INCREMENT,
  `level` int(11) NOT NULL,
  `levelName` varchar(25) NOT NULL DEFAULT '',
  PRIMARY KEY (`idLevel`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

-- Zrzucanie danych dla tabeli fb-clone.privacyLevel: ~3 rows (około)
DELETE FROM `privacyLevel`;
/*!40000 ALTER TABLE `privacyLevel` DISABLE KEYS */;
INSERT INTO `privacyLevel` (`idLevel`, `level`, `levelName`) VALUES
	(1, 0, 'Private'),
	(2, 1, 'For friends'),
	(3, 2, 'Public');
/*!40000 ALTER TABLE `privacyLevel` ENABLE KEYS */;

-- Zrzut struktury tabela fb-clone.users
CREATE TABLE IF NOT EXISTS `users` (
  `idUser` int(11) NOT NULL AUTO_INCREMENT,
  `idPrivacy` int(11) NOT NULL,
  `email` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `registerDate` int(11) NOT NULL DEFAULT 0,
  `idUserData` int(11) NOT NULL DEFAULT 0,
  `token` varchar(255) NOT NULL DEFAULT '0',
  `tokenValid` int(11) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`idUser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Zrzucanie danych dla tabeli fb-clone.users: ~0 rows (około)
DELETE FROM `users`;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

-- Zrzut struktury tabela fb-clone.usersData
CREATE TABLE IF NOT EXISTS `usersData` (
  `idUserData` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `surname` varchar(50) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `zip-code` varchar(50) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`idUserData`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Zrzucanie danych dla tabeli fb-clone.usersData: ~0 rows (około)
DELETE FROM `usersData`;
/*!40000 ALTER TABLE `usersData` DISABLE KEYS */;
/*!40000 ALTER TABLE `usersData` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
