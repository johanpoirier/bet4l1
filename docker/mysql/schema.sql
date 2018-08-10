SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+02:00";

use bets;

-- --------------------------------------------------------

--
-- Structure de la table `bet4l1__instances`
--

CREATE TABLE IF NOT EXISTS `bet4l1__instances` (
  `id` int(6) NOT NULL,
  `name` varchar(255) collate utf8_general_ci NOT NULL,
  `ownerID` int(6) NOT NULL,
  `parentID` int(6) NOT NULL,
  `active` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

INSERT INTO `bet4l1__instances` (`id`, `name`, `ownerID`, `active`) VALUES (1, 'Pronostics', 1, 1);


--
-- Structure de la table `bet4l1__matchs`
--

CREATE TABLE IF NOT EXISTS `bet4l1__matchs` (
  `matchID` int(11) unsigned NOT NULL auto_increment,
  `teamA` int(11) NOT NULL default '0',
  `teamB` int(11) NOT NULL default '0',
  `scoreA` int(11) default NULL,
  `scoreB` int(11) default NULL,
  `pnyA` int(5) default NULL,
  `pnyB` int(5) default NULL,
  `bonusA` int(1) unsigned NOT NULL default '0',
  `bonusB` int(1) unsigned NOT NULL default '0',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `phaseID` int(10) unsigned NOT NULL default '1',
  `status` int(11) NOT NULL default '0',
  PRIMARY KEY  (`matchID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Structure de la table `bet4l1__phases`
--

CREATE TABLE IF NOT EXISTS `bet4l1__phases` (
  `phaseID` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(20) collate utf8_general_ci NOT NULL default '',
  `aller_retour` int(1) unsigned NOT NULL default '0',
  `nb_matchs` int(6) unsigned NOT NULL default '0',
  `nb_qualifies` int(3) NOT NULL default '1',
  `phasePrecedente` int(10) unsigned default NULL,
  `nbPointsRes` int(6) unsigned NOT NULL default '0',
  `nbPointsQualifie` int(6) unsigned NOT NULL default '0',
  `nbPointsScore` int(6) unsigned NOT NULL default '0',
  `multiplicateurMatchDuJour` int(6) unsigned NOT NULL default '1',
  `instanceID` int(6) NOT NULL default '1',
  PRIMARY KEY  (`phaseID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Structure de la table `bet4l1__pronos`
--

CREATE TABLE IF NOT EXISTS `bet4l1__pronos` (
  `userID` int(9) NOT NULL default '0',
  `matchID` int(9) NOT NULL default '0',
  `scoreA` int(2) default NULL,
  `scoreB` int(2) default NULL,
  `pnyA` int(5) default NULL,
  `pnyB` int(5) default NULL,
  `status` int(2) NOT NULL default '0',
  PRIMARY KEY  (`userID`,`matchID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `bet4l1__settings`
--

CREATE TABLE IF NOT EXISTS `bet4l1__settings` (
  `instanceID` int(9) NOT NULL default '0',
  `name` varchar(35) collate utf8_general_ci NOT NULL default '',
  `value` varchar(255) collate utf8_general_ci default NULL,
  `date` datetime default NULL,
  `status` int(2) NOT NULL default '0',
  PRIMARY KEY  (`name`,`instanceID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `bet4l1__stats_user`
--

CREATE TABLE IF NOT EXISTS `bet4l1__stats_user` (
  `userID` int(9) NOT NULL default '0',
  `label` varchar(30) collate utf8_general_ci NOT NULL,
  `rank` int(5) unsigned NOT NULL default '0',
  `points` int(9) unsigned NOT NULL default '0',
  `nbresults` int(5) unsigned NOT NULL default '0',
  `nbscores` int(5) unsigned NOT NULL default '0',
  `diff` int(5) NOT NULL default '0',
  KEY `userID` (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `bet4l1__tags`
--

CREATE TABLE IF NOT EXISTS `bet4l1__tags` (
  `tagID` int(5) NOT NULL auto_increment,
  `instanceID` int(6) NOT NULL default '1',
  `userID` int(5) NOT NULL default '0',
  `userTeamID` int(6) NOT NULL default '-1',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `tag` text collate utf8_general_ci NOT NULL,
  PRIMARY KEY  (`tagID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `bet4l1__teams`
--

CREATE TABLE IF NOT EXISTS `bet4l1__teams` (
  `teamID` int(11) NOT NULL auto_increment,
  `name` varchar(50) collate utf8_general_ci NOT NULL default '',
  `rssName` varchar(50) collate utf8_general_ci default NULL,
  `instanceID` int(6) unsigned NOT NULL default '1',
  `status` int(5) NOT NULL default '0',
  PRIMARY KEY  (`teamID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Structure de la table `bet4l1__users`
--

CREATE TABLE IF NOT EXISTS `bet4l1__users` (
  `userID` int(9) unsigned NOT NULL auto_increment,
  `instanceID` int(6) NOT NULL default '1',
  `name` varchar(40) collate utf8_general_ci NOT NULL default '',
  `login` varchar(30) collate utf8_general_ci NOT NULL default '',
  `password` varchar(32) collate utf8_general_ci NOT NULL,
  `email` varchar(255) collate utf8_general_ci default NULL,
  `email_preferences` varchar(8) collate utf8_general_ci NOT NULL default '11',
  `points` int(9) unsigned NOT NULL default '0',
  `nbresults` int(5) unsigned NOT NULL default '0',
  `nbscores` int(5) unsigned NOT NULL default '0',
  `bonus` int(9) unsigned NOT NULL default '0',
  `diff` int(5) NOT NULL default '0',
  `last_rank` int(3) unsigned NOT NULL default '1',
  `last_rank_lcp` int(9) unsigned NOT NULL default '1',
  `lcp_points` int(9) unsigned NOT NULL default '0',
  `lcp_bonus` int(9) unsigned NOT NULL default '0',
  `lcp_match` int(9) unsigned NOT NULL default '0',
  `userTeamID` int(11) unsigned default NULL,
  `status` int(9) NOT NULL default '0',
  PRIMARY KEY  (`userID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Structure de la table `bet4l1__user_teams`
--

CREATE TABLE IF NOT EXISTS `bet4l1__user_teams` (
  `userTeamID` int(10) unsigned NOT NULL auto_increment,
  `instanceID` varchar(6) collate utf8_general_ci NOT NULL default '1',
  `name` varchar(100) character set latin1 collate latin1_general_ci NOT NULL default '',
  `avgPoints` float unsigned NOT NULL default '0',
  `totalPoints` int(6) unsigned NOT NULL default '0',
  `maxPoints` int(6) unsigned NOT NULL default '0',
  `lastRank` int(6) unsigned NOT NULL default '1',
  PRIMARY KEY  (`userTeamID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;



--
-- Contenu pour les tables exportées
--

INSERT INTO `bet4l1__settings` (`instanceID`, `name`, `value`, `date`, `status`) VALUES
  (14, 'DATE_DEBUT', '', '2016-08-12 20:00:00', 0),
  (14, 'DATE_FIN', '', '2017-05-14 20:00:00', 0),
  (14, 'LAST_GENERATE', 'Après 10 matchs sur 10 de la 1ème journée', '2016-08-28 22:30:28', 0),
  (14, 'LAST_GENERATE_LCP', NULL, '2016-08-28 22:30:28', 0),
  (14, 'LAST_RESULT', '', '0000-00-00 00:00:00', 0),
  (14, 'NB_MATCHS_PLAYED', '0', '0000-00-00 00:00:00', 0),
  (14, 'NB_POINTS_NUL', '1', '0000-00-00 00:00:00', 0),
  (14, 'NB_POINTS_VICTOIRE', '3', '0000-00-00 00:00:00', 0);

INSERT INTO `bet4l1__users` (`userID`, `instanceID`, `name`, `login`, `email`, `password`, `status`) VALUES
  (1, 1, 'John Foo', 'admin', 'admin@bet4l1.fr', 'f71dbe52628a3f83a77ab494817525c6', 1),
  (2, 1, 'George Weah', 'weah', 'george@weah.com', 'f71dbe52628a3f83a77ab494817525c6', 0);

INSERT INTO `bet4l1__teams` (`teamID`, `name`, `rssName`, `instanceID`, `status`) VALUES
  (1, 'Olympique Lyonnais', 'OL', 1, 1),
  (2, 'Paris SG', 'PSG', 1, 1),
  (3, 'FC Lorient', 'Lorient', 1, 1),
  (4, 'FC Metz', 'Metz', 1, 1);

INSERT INTO `bet4l1__phases` (`phaseID`, `name`, `aller_retour`, `nb_matchs`, `nb_qualifies`, `phasePrecedente`, `nbPointsRes`, `nbPointsQualifie`, `nbPointsScore`, `multiplicateurMatchDuJour`, `instanceID`) VALUES
  (1, '1ere journee', 0, 10, 10, NULL, 1, 0, 1, 2, 1),
  (2, '2eme journee', 0, 10, 10, 1, 1, 0, 1, 2, 1),
  (3, '3eme journee', 0, 10, 10, 2, 1, 0, 1, 2, 1),
  (4, '4eme journee', 0, 10, 10, 3, 1, 0, 1, 2, 1);

INSERT INTO `bet4l1__matchs` (`matchID`, `teamA`, `teamB`, `scoreA`, `scoreB`, `pnyA`, `pnyB`, `bonusA`, `bonusB`, `date`, `phaseID`, `status`) VALUES
  (1, 1, 2, 2, 1, NULL, NULL, 0, 0, '2016-08-12 20:00:00', 1, 0),
  (2, 4, 3, 1, 4, NULL, NULL, 0, 0, '2016-08-12 20:30:00', 1, 0);

INSERT INTO `bet4l1__pronos` (`userID`, `matchID`, `scoreA`, `scoreB`, `pnyA`, `pnyB`, `status`) VALUES
  (2, 1, 2, 0, NULL, NULL, 0),
  (2, 2, 0, 3, NULL, NULL, 0);
