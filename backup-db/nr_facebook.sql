-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 20 mars 2025 à 19:45
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `nr_facebook`
--
CREATE DATABASE IF NOT EXISTS `nr_facebook` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `nr_facebook`;

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `comment_text` varchar(255) NOT NULL,
  `time_stamp` datetime NOT NULL,
  `nb_responses` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `discussions`
--

CREATE TABLE `discussions` (
  `discussion_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `discussion_name` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `discussions_messages`
--

CREATE TABLE `discussions_messages` (
  `discussion_message_id` int(11) NOT NULL,
  `discussion_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` varchar(500) NOT NULL,
  `time_stamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `groups`
--

CREATE TABLE `groups` (
  `group_id` int(11) NOT NULL,
  `group_name` varchar(255) NOT NULL,
  `group_profile_picture_path` varchar(255) NOT NULL,
  `group_banner_picture_path` varchar(255) NOT NULL,
  `group_description` varchar(500) NOT NULL,
  `nb_members` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `group_members`
--

CREATE TABLE `group_members` (
  `group_member_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `instances`
--

CREATE TABLE `instances` (
  `instance_id` int(11) NOT NULL,
  `instance_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `likes`
--

CREATE TABLE `likes` (
  `like_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `posts`
--

CREATE TABLE `posts` (
  `post_id` int(11) NOT NULL,
  `instance_id` int(11) NOT NULL,
  `post_description` varchar(500) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_picture_path` varchar(255) NOT NULL,
  `time_stamp` datetime NOT NULL,
  `nb_comments` int(11) NOT NULL DEFAULT 0,
  `nb_likes` int(11) NOT NULL DEFAULT 0,
  `nb_shares` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `shares`
--

CREATE TABLE `shares` (
  `share_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `instance_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `userlinkinstance`
--

CREATE TABLE `userlinkinstance` (
  `link_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `instance_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_firstname` varchar(50) NOT NULL,
  `user_lastname` varchar(50) NOT NULL,
  `user_pp` varchar(255) NOT NULL,
  `user_description` varchar(500) NOT NULL,
  `user_location` varchar(255) NOT NULL,
  `user_work` varchar(255) NOT NULL,
  `user_school` varchar(255) NOT NULL,
  `user_banner_picture_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `FK_CommentsUserId` (`user_id`),
  ADD KEY `FK_CommentsPostId` (`post_id`);

--
-- Index pour la table `discussions`
--
ALTER TABLE `discussions`
  ADD PRIMARY KEY (`discussion_id`),
  ADD KEY `FK_DiscussionGroupID` (`group_id`);

--
-- Index pour la table `discussions_messages`
--
ALTER TABLE `discussions_messages`
  ADD PRIMARY KEY (`discussion_message_id`),
  ADD KEY `FK_DiscussionMessagesDiscussionId` (`discussion_id`),
  ADD KEY `FK_DiscussionsMessagesUserId` (`user_id`);

--
-- Index pour la table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`group_id`);

--
-- Index pour la table `group_members`
--
ALTER TABLE `group_members`
  ADD PRIMARY KEY (`group_member_id`),
  ADD KEY `FK_GroupMembersGroupId` (`group_id`),
  ADD KEY `FK_GroupMembersUserId` (`user_id`);

--
-- Index pour la table `instances`
--
ALTER TABLE `instances`
  ADD PRIMARY KEY (`instance_id`);

--
-- Index pour la table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`like_id`),
  ADD KEY `FK_LikesPostId` (`post_id`),
  ADD KEY `FK_LikesUserId` (`user_id`);

--
-- Index pour la table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `FK_PostsUserId` (`user_id`),
  ADD KEY `FK_PostsInstanceId` (`instance_id`);

--
-- Index pour la table `shares`
--
ALTER TABLE `shares`
  ADD PRIMARY KEY (`share_id`),
  ADD KEY `FK_SharesUserId` (`user_id`),
  ADD KEY `FK_SharesPostId` (`post_id`),
  ADD KEY `FK_SharesInstanceId` (`instance_id`);

--
-- Index pour la table `userlinkinstance`
--
ALTER TABLE `userlinkinstance`
  ADD KEY `FK_UserLinkInstanceUserId` (`user_id`),
  ADD KEY `FK_UserLinkInstanceInstanceId` (`instance_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `discussions`
--
ALTER TABLE `discussions`
  MODIFY `discussion_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `discussions_messages`
--
ALTER TABLE `discussions_messages`
  MODIFY `discussion_message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `groups`
--
ALTER TABLE `groups`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `group_members`
--
ALTER TABLE `group_members`
  MODIFY `group_member_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `instances`
--
ALTER TABLE `instances`
  MODIFY `instance_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `likes`
--
ALTER TABLE `likes`
  MODIFY `like_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `posts`
--
ALTER TABLE `posts`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `shares`
--
ALTER TABLE `shares`
  MODIFY `share_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `FK_CommentsPostId` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`),
  ADD CONSTRAINT `FK_CommentsUserId` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Contraintes pour la table `discussions`
--
ALTER TABLE `discussions`
  ADD CONSTRAINT `FK_DiscussionGroupID` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`);

--
-- Contraintes pour la table `discussions_messages`
--
ALTER TABLE `discussions_messages`
  ADD CONSTRAINT `FK_DiscussionMessagesDiscussionId` FOREIGN KEY (`discussion_id`) REFERENCES `discussions` (`discussion_id`),
  ADD CONSTRAINT `FK_DiscussionsMessagesUserId` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Contraintes pour la table `group_members`
--
ALTER TABLE `group_members`
  ADD CONSTRAINT `FK_GroupMembersGroupId` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`),
  ADD CONSTRAINT `FK_GroupMembersUserId` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Contraintes pour la table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `FK_LikesPostId` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`),
  ADD CONSTRAINT `FK_LikesUserId` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Contraintes pour la table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `FK_PostsInstanceId` FOREIGN KEY (`instance_id`) REFERENCES `instances` (`instance_id`),
  ADD CONSTRAINT `FK_PostsUserId` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Contraintes pour la table `shares`
--
ALTER TABLE `shares`
  ADD CONSTRAINT `FK_SharesInstanceId` FOREIGN KEY (`instance_id`) REFERENCES `instances` (`instance_id`),
  ADD CONSTRAINT `FK_SharesPostId` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`),
  ADD CONSTRAINT `FK_SharesUserId` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Contraintes pour la table `userlinkinstance`
--
ALTER TABLE `userlinkinstance`
  ADD CONSTRAINT `FK_UserLinkInstanceInstanceId` FOREIGN KEY (`instance_id`) REFERENCES `instances` (`instance_id`),
  ADD CONSTRAINT `FK_UserLinkInstanceUserId` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
