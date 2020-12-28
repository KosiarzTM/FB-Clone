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
  `friendStatus` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Zrzucanie danych dla tabeli fb-clone.friendList: ~10 rows (około)
DELETE FROM `friendList`;
/*!40000 ALTER TABLE `friendList` DISABLE KEYS */;
INSERT INTO `friendList` (`idUser`, `idFriend`, `friendStatus`) VALUES
	(1, 2, 1),
	(1, 4, 1),
	(1, 5, 1),
	(1, 6, 1),
	(6, 1, 1),
	(16, 5, 1),
	(16, 7, 0),
	(16, 8, 0),
	(5, 16, 1),
	(16, 4, 0);
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
	(3, 2, 'Blocked');
/*!40000 ALTER TABLE `friendStatus` ENABLE KEYS */;

-- Zrzut struktury tabela fb-clone.postComments
CREATE TABLE IF NOT EXISTS `postComments` (
  `idComment` int(11) NOT NULL AUTO_INCREMENT,
  `idCommentOwner` int(11) DEFAULT NULL,
  `idPost` int(11) DEFAULT NULL,
  `comment` longtext DEFAULT NULL,
  `likes` longtext DEFAULT NULL,
  `date` int(11) DEFAULT NULL,
  PRIMARY KEY (`idComment`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;

-- Zrzucanie danych dla tabeli fb-clone.postComments: ~6 rows (około)
DELETE FROM `postComments`;
/*!40000 ALTER TABLE `postComments` DISABLE KEYS */;
INSERT INTO `postComments` (`idComment`, `idCommentOwner`, `idPost`, `comment`, `likes`, `date`) VALUES
	(3, 16, 3, 'qweOdp12312312', '', NULL),
	(4, 16, 3, 'qweOdp12312312', NULL, NULL),
	(5, 16, 3, 'qweOdp12312312', NULL, NULL),
	(6, 16, 3, 'qweOdp12312312', NULL, NULL),
	(7, 16, 3, 'qweOdp12312312', NULL, NULL),
	(8, 16, 3, 'qweOdp12312312', NULL, NULL);
/*!40000 ALTER TABLE `postComments` ENABLE KEYS */;

-- Zrzut struktury tabela fb-clone.posts
CREATE TABLE IF NOT EXISTS `posts` (
  `idPost` int(11) NOT NULL AUTO_INCREMENT,
  `idPostOwner` int(11) DEFAULT NULL,
  `post` longtext DEFAULT NULL,
  `likes` longtext DEFAULT NULL,
  `date` int(11) DEFAULT NULL,
  PRIMARY KEY (`idPost`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;

-- Zrzucanie danych dla tabeli fb-clone.posts: ~4 rows (około)
DELETE FROM `posts`;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` (`idPost`, `idPostOwner`, `post`, `likes`, `date`) VALUES
	(1, 0, '', NULL, NULL),
	(3, 16, 'qwe', NULL, NULL),
	(5, 16, 'qweqeeqe', '12', NULL),
	(6, 16, 'qweqweqweqweqe', '16', 1608819339);
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
  `registerDate` int(11) NOT NULL,
  `token` varchar(255) NOT NULL DEFAULT '0',
  `tokenValid` int(11) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`idUser`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4;

-- Zrzucanie danych dla tabeli fb-clone.users: ~17 rows (około)
DELETE FROM `users`;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`idUser`, `idPrivacy`, `email`, `password`, `registerDate`, `token`, `tokenValid`, `active`) VALUES
	(1, 2, 'test1@test.com', '$2y$10$QCrry0HRfCNzRICB3cpDBuXl6HWMxZz3qILnU1Kz1A913zphuSHjC', 1608464945, '0', 0, 0),
	(2, 2, 'test2@test.com', '$2y$10$j8AQ2/a.aRlCB5qSy6vWyOu36WhOY.XlRKl2AhCE9yTdqvUyDZNuG', 1608464981, '0', 0, 0),
	(3, 2, 'test3@test.com', '$2y$10$c0Cvyu8udwCsBzDSCP6c8eFiNaLCGgpQePDMhObzt9WqS9lSz4uU2', 1608465121, '0', 0, 0),
	(4, 0, 'test4@test.com', '$2y$10$XplqEybaXTUVEOTIvejRGu.QyRBeFiCThXKyuv0LvQpyTTszVwV4G', 1608465126, '0', 0, 0),
	(5, 2, 'test5@test.com', '$2y$10$IT88amc8tCtFPcs3nPrUUuCYI2gD6fM89Qj0mnWZUZL.BdpqsRmGy', 1608465130, '0', 0, 0),
	(6, 1, 'test6@test.com', '$2y$10$WuRrHqOAfidQArz5Pg.xEOcQAUu.AQlQQjiinKQGMAll6Q59DcfUC', 1608465132, '0', 0, 0),
	(7, 1, 'test7@test.com', '$2y$10$OnSowIeCjUTzJ2NvWuYSfe1aQpgVBlTpLglqnxipeZSc1UIfQzgYK', 1608465136, '0', 0, 0),
	(8, 1, 'test8@test.com', '$2y$10$dtBXN05IYDr6CDbzG6ttH.Pqrox77XiphoVQtGVip23chN/BxOCfC', 1608465143, '0', 0, 0),
	(9, 1, 'test9@test.com', '$2y$10$Q.7F.o3jGXNQlNH8VugiPuZ2kwCczUkorXECdPe73AuMyunHM4HRa', 1608465147, '0', 0, 0),
	(10, 1, 'test10@test.com', '$2y$10$SclU4X1zGhAKlXK9KnOpaurolRZrfEd1Mw1Xyo1dE1QzyhQhT9j06', 1608465150, '0', 0, 0),
	(11, 0, 'test11@test.com', '$2y$10$URv1Xfgz6cGHJ7rpUTBS5etvQWShVZxed8P/34GdptN09jPt2sTrq', 1608465153, '0', 0, 0),
	(12, 0, 'test12@test.com', '$2y$10$05VwQJ7QNWxy5CqcC8PUa./EN74FKKqJZ3wG2CbySYTlA88S6jLKC', 1608465157, '0', 0, 0),
	(13, 0, 'test13@test.com', '$2y$10$4yPeJoZ4rO1ZUF0RE8ZT4e1XaUrfbVV0OfEdj4A4rHa8A9JsWv5Eu', 1608465159, '0', 0, 0),
	(14, 0, 'test14@test.com', '$2y$10$V9v1KzgW3j0HeAegy9Q.I.MCeqW/dbcpj6v3c6GEU8obhcHLuqJ0e', 1608465163, '0', 0, 0),
	(15, 0, 'test15@test.com', '$2y$10$o.xikqNNUlVx0g1yhkFreOUfibfr62lhqUGa.CH4VpWobxHTyk6jW', 1608465166, '0', 0, 0),
	(16, 0, 'email@mail.com', '$2y$10$h9av6t2aWy5WgflRXnlWOOcHxDlZw4YyfCPE7fVkaidh/lO8iMyBO', 0, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6ImVtYWlsQG1haWwuY29tIiwiaWF0IjoxNjA5MDcwODM1LCJleHAiOjE2MDkwNzQ0MzV9.juOCJJq82urOUolK27obHQMv0-sSD8nzq8vS4NWkUog', 0, 0),
	(18, 0, '', '', 0, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6ImF1dGhAbWFpbC5jb20iLCJpYXQiOjE2MDg4MTM0NTYsImV4cCI6MTYwODgxNzA1Nn0.LCFr5Otd00GOHyo7ns0cBvF81bLJoijocjg89tx9adg', 0, 0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

-- Zrzut struktury tabela fb-clone.usersData
CREATE TABLE IF NOT EXISTS `usersData` (
  `idUserData` int(11) NOT NULL AUTO_INCREMENT,
  `idUser` int(11) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `surname` varchar(50) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `zipCode` varchar(50) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`idUserData`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4;

-- Zrzucanie danych dla tabeli fb-clone.usersData: ~16 rows (około)
DELETE FROM `usersData`;
/*!40000 ALTER TABLE `usersData` DISABLE KEYS */;
INSERT INTO `usersData` (`idUserData`, `idUser`, `name`, `surname`, `phone`, `address`, `zipCode`, `city`, `country`) VALUES
	(1, 1, 'Imie', 'Nazwisko', '000000000', 'Address', '00-000', 'Miasto', 'Polska'),
	(2, 2, 'Imie2', 'Nazwisko2', '111111111', 'Address2', '00-001', 'Miasto2', 'Polska2'),
	(3, 3, 'Imie3', 'Nazwisko3', '333333333', 'Address3', '00-003', 'City3', 'PL3'),
	(4, 4, 'Imie4', 'Nazwisko4', '333333333', 'Address4', '00-004', 'City4', 'PL4'),
	(5, 5, 'Imie5', 'Nazwisko5', '555555555', 'Address5', '00-005', 'City5', 'PL5'),
	(6, 6, 'Imie6', 'Nazwisko6', '666666666', 'Address6', '00-006', 'City6', 'PL6'),
	(7, 7, 'Imie7', 'Nazwisko7', '777777777', 'Address7', '00-007', 'City7', 'PL7'),
	(8, 8, 'Imie8', 'Nazwisko8', '888888888', 'Address8', '00-008', 'City8', 'PL8'),
	(9, 9, 'Imie9', 'Nazwisko9', '999999999', 'Address9', '00-009', 'City9', 'PL9'),
	(10, 10, 'Imie10', 'Nazwisko10', '100100100', 'Address10', '00-0010', 'City10', 'PL10'),
	(11, 11, 'Imie11', 'Nazwisko11', '110110110', 'Address11', '00-0011', 'City11', 'PL11'),
	(12, 12, 'Imie12', 'Nazwisko12', '120120120', 'Address12', '00-0012', 'City12', 'PL12'),
	(13, 13, 'Imie13', 'Nazwisko13', '130130130', 'Address13', '00-0013', 'City15', 'PL13'),
	(14, 14, 'Imie14', 'Nazwisko14', '140140140', 'Address14', '00-0014', 'City15', 'PL14'),
	(15, 15, 'Imie15', 'Nazwisko15', '150150150', 'Address15', '00-0015', 'City15', 'PL15'),
	(17, 16, 'Jan', 'Now123ak', '666666666', 'Janówek', '00-000', 'Janowo', 'Januszowo');
/*!40000 ALTER TABLE `usersData` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
