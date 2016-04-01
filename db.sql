SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- Structure de la table `bet4l1__instances`
--

CREATE TABLE IF NOT EXISTS `bet4l1__instances` (
  `id` int(6) NOT NULL,
  `name` varchar(255) collate utf8_bin NOT NULL,
  `ownerID` int(6) NOT NULL,
  `parentID` int(6) NOT NULL,
  `active` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1084 ;

-- --------------------------------------------------------

--
-- Structure de la table `bet4l1__phases`
--

CREATE TABLE IF NOT EXISTS `bet4l1__phases` (
  `phaseID` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(20) collate utf8_bin NOT NULL default '',
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=203 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `bet4l1__settings`
--

CREATE TABLE IF NOT EXISTS `bet4l1__settings` (
  `instanceID` int(9) NOT NULL default '0',
  `name` varchar(35) collate utf8_bin NOT NULL default '',
  `value` varchar(255) collate utf8_bin default NULL,
  `date` datetime default NULL,
  `status` int(2) NOT NULL default '0',
  PRIMARY KEY  (`name`,`instanceID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `bet4l1__stats_user`
--

CREATE TABLE IF NOT EXISTS `bet4l1__stats_user` (
  `userID` int(9) NOT NULL default '0',
  `label` varchar(30) collate utf8_bin NOT NULL,
  `rank` int(5) unsigned NOT NULL default '0',
  `points` int(9) unsigned NOT NULL default '0',
  `nbresults` int(5) unsigned NOT NULL default '0',
  `nbscores` int(5) unsigned NOT NULL default '0',
  `diff` int(5) NOT NULL default '0',
  KEY `userID` (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

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
  `tag` text collate utf8_bin NOT NULL,
  PRIMARY KEY  (`tagID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=439 ;

-- --------------------------------------------------------

--
-- Structure de la table `bet4l1__teams`
--

CREATE TABLE IF NOT EXISTS `bet4l1__teams` (
  `teamID` int(11) NOT NULL auto_increment,
  `name` varchar(50) collate utf8_bin NOT NULL default '',
  `rssName` varchar(50) collate utf8_bin default NULL,
  `instanceID` int(6) unsigned NOT NULL default '1',
  `status` int(5) NOT NULL default '0',
  PRIMARY KEY  (`teamID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=61 ;

-- --------------------------------------------------------

--
-- Structure de la table `bet4l1__users`
--

CREATE TABLE IF NOT EXISTS `bet4l1__users` (
  `userID` int(9) unsigned NOT NULL auto_increment,
  `instanceID` int(6) NOT NULL default '1',
  `name` varchar(40) collate utf8_bin NOT NULL default '',
  `login` varchar(30) collate utf8_bin NOT NULL default '',
  `password` varchar(32) collate utf8_bin NOT NULL,
  `email` varchar(255) collate utf8_bin default NULL,
  `email_preferences` varchar(8) collate utf8_bin NOT NULL default '11',
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=372 ;

-- --------------------------------------------------------

--
-- Structure de la table `bet4l1__user_teams`
--

CREATE TABLE IF NOT EXISTS `bet4l1__user_teams` (
  `userTeamID` int(10) unsigned NOT NULL auto_increment,
  `instanceID` varchar(6) collate utf8_bin NOT NULL default '1',
  `name` varchar(100) character set latin1 collate latin1_general_ci NOT NULL default '',
  `avgPoints` float unsigned NOT NULL default '0',
  `totalPoints` int(6) unsigned NOT NULL default '0',
  `maxPoints` int(6) unsigned NOT NULL default '0',
  `lastRank` int(6) unsigned NOT NULL default '1',
  PRIMARY KEY  (`userTeamID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=32 ;
