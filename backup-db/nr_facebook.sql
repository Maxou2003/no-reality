-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 09 avr. 2025 à 17:05
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

--
-- Déclencheurs `comments`
--
DELIMITER $$
CREATE TRIGGER `after_comment_deletion` AFTER DELETE ON `comments` FOR EACH ROW BEGIN
    UPDATE posts
    SET nb_comments = (
        SELECT COUNT(DISTINCT comment_id)
        FROM comments
        WHERE comments.post_id = OLD.post_id
    )
    WHERE post_id = OLD.post_id;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_comment_insertion` AFTER INSERT ON `comments` FOR EACH ROW BEGIN
    UPDATE posts
    SET nb_comments = (
        SELECT COUNT(DISTINCT comment_id)
        FROM comments
        WHERE comments.post_id = NEW.post_id
    )
    WHERE post_id = NEW.post_id;

END
$$
DELIMITER ;

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
-- Structure de la table `friends`
--

CREATE TABLE `friends` (
  `user_id_1` int(11) NOT NULL,
  `user_id_2` int(11) NOT NULL,
  `instance_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `friends`
--

INSERT INTO `friends` (`user_id_1`, `user_id_2`, `instance_id`, `created_at`) VALUES
(1, 2, 1, '2025-03-27 10:24:40'),
(1, 3, 1, '2025-03-27 10:25:35'),
(1, 4, 1, '2025-03-27 10:24:08'),
(1, 5, 1, '2025-03-27 10:24:20'),
(2, 3, 1, '2025-03-27 10:23:56'),
(2, 4, 1, '2025-03-27 10:24:14'),
(3, 4, 1, '2025-03-27 10:25:27'),
(3, 5, 1, '2025-03-27 10:24:01'),
(4, 5, 1, '2025-03-27 10:24:46');

--
-- Déclencheurs `friends`
--
DELIMITER $$
CREATE TRIGGER `order_friend_ids` BEFORE INSERT ON `friends` FOR EACH ROW BEGIN
    IF NEW.user_id_1 > NEW.user_id_2 THEN
        -- Swap the IDs
        SET @temp = NEW.user_id_1;
        SET NEW.user_id_1 = NEW.user_id_2;
        SET NEW.user_id_2 = @temp;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `prevent_self_friendship` BEFORE INSERT ON `friends` FOR EACH ROW BEGIN
    IF NEW.user_id_1 = NEW.user_id_2 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'A user cannot be friends with themselves.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `groups`
--

CREATE TABLE `groups` (
  `group_id` int(11) NOT NULL,
  `group_name` varchar(255) NOT NULL,
  `time_stamp` datetime NOT NULL,
  `group_banner_picture_path` varchar(255) NOT NULL,
  `group_description` varchar(500) NOT NULL,
  `nb_members` int(11) NOT NULL DEFAULT 0,
  `instance_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `groups`
--

INSERT INTO `groups` (`group_id`, `group_name`, `time_stamp`, `group_banner_picture_path`, `group_description`, `nb_members`, `instance_id`) VALUES
(1, 'LoveIsLife', '2025-04-02 16:32:47', 'art_1_1.jpg', 'Love is life so live as you love !', 2, 1),
(2, 'LesTueLamour', '2025-04-09 10:18:28', 'Quels-sont-les-pires-tue-l-amour-selon-les-celibataires.jpg', 'On partage tous les pires tue-l\'amour pour t\'éviter les soucis ! Rejoins nous vite ! <3', 1, 1),
(3, 'LesCupidons', '2025-04-09 16:46:22', 'pexels-asadphoto-1024975.jpg', 'On est les anges de l\'amour ! ', 0, 1),
(5, 'LesCharrots', '2025-04-09 16:48:31', 'pexels-gabriel-bastelli-865174-1759823.jpg', 'On pécho dure ici ! ', 0, 1),
(6, 'LesAbandonnésDeLamour', '2025-04-09 16:49:10', 'pexels-pengwhan-1767434.jpg', 'Nous on est un groupe de célibataires assumés ! ', 0, 1);

-- --------------------------------------------------------

--
-- Structure de la table `group_members`
--

CREATE TABLE `group_members` (
  `group_member_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `group_members`
--

INSERT INTO `group_members` (`group_member_id`, `group_id`, `user_id`) VALUES
(1, 1, 1),
(3, 2, 1),
(4, 1, 3);

--
-- Déclencheurs `group_members`
--
DELIMITER $$
CREATE TRIGGER `after_group_member_deletion` AFTER DELETE ON `group_members` FOR EACH ROW BEGIN
    UPDATE groups
    SET nb_members = (
        SELECT COUNT(DISTINCT user_id)
        FROM group_members
        WHERE group_members.group_id = OLD.group_id
    )
    WHERE group_id = OLD.group_id;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_group_member_insertion` AFTER INSERT ON `group_members` FOR EACH ROW BEGIN
    UPDATE groups
    SET nb_members = (
        SELECT COUNT(DISTINCT user_id)
        FROM group_members
        WHERE group_members.group_id = NEW.group_id
    )
    WHERE group_id = NEW.group_id;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `group_posts`
--

CREATE TABLE `group_posts` (
  `instance_id` int(11) NOT NULL,
  `nb_comments` int(11) NOT NULL,
  `nb_likes` int(11) NOT NULL,
  `post_content` varchar(500) NOT NULL,
  `post_id` int(11) NOT NULL,
  `post_picture_path` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `time_stamp` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `group_posts`
--

INSERT INTO `group_posts` (`instance_id`, `nb_comments`, `nb_likes`, `post_content`, `post_id`, `post_picture_path`, `time_stamp`, `user_id`, `group_id`) VALUES
(1, 0, 0, 'Just spend love', 1, '', '2025-04-02 16:36:41', 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `identifications`
--

CREATE TABLE `identifications` (
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `instance_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `identifications`
--

INSERT INTO `identifications` (`user_id`, `post_id`, `instance_id`) VALUES
(1, 2, 1);

-- --------------------------------------------------------

--
-- Structure de la table `instances`
--

CREATE TABLE `instances` (
  `instance_id` int(11) NOT NULL,
  `instance_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `instances`
--

INSERT INTO `instances` (`instance_id`, `instance_name`) VALUES
(1, 'love');

-- --------------------------------------------------------

--
-- Structure de la table `likes`
--

CREATE TABLE `likes` (
  `like_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déclencheurs `likes`
--
DELIMITER $$
CREATE TRIGGER `after_like_deletion` AFTER DELETE ON `likes` FOR EACH ROW BEGIN
    UPDATE posts
    SET nb_likes = (
        SELECT COUNT(DISTINCT user_id)
        FROM likes
        WHERE likes.post_id = OLD.post_id
    )
    WHERE post_id = OLD.post_id;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_like_insertion` AFTER INSERT ON `likes` FOR EACH ROW BEGIN
    UPDATE posts
    SET nb_likes = (
        SELECT COUNT(DISTINCT user_id)
        FROM likes
        WHERE likes.post_id = NEW.post_id
    )
    WHERE post_id = NEW.post_id;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `posts`
--

CREATE TABLE `posts` (
  `post_id` int(11) NOT NULL,
  `instance_id` int(11) NOT NULL,
  `post_content` varchar(500) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_picture_path` varchar(255) NOT NULL,
  `time_stamp` datetime NOT NULL,
  `nb_comments` int(11) NOT NULL DEFAULT 0,
  `nb_likes` int(11) NOT NULL DEFAULT 0,
  `nb_shares` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `posts`
--

INSERT INTO `posts` (`post_id`, `instance_id`, `post_content`, `user_id`, `post_picture_path`, `time_stamp`, `nb_comments`, `nb_likes`, `nb_shares`) VALUES
(1, 1, 'Trop de love tue le love', 1, 'art_1_1.jpg', '2025-03-26 11:40:12', 0, 0, 0),
(2, 1, 'L\'amour ça se provoque !', 3, 'pexels-vjapratama-935789.jpg', '2025-03-26 16:25:34', 0, 0, 0),
(3, 1, 'L\'amour quand ça vous prend...', 5, 'pexels-gabriel-bastelli-865174-1759823.jpg', '2025-03-26 16:27:40', 0, 0, 0),
(4, 1, 'I hate nothing about you, if you go to my representation on the 5th of April ! ', 4, 'pexels-designecologist-887353.jpg', '2025-03-26 16:28:21', 0, 0, 0),
(6, 1, 'Weekend en amoureux !', 2, 'pexels-asadphoto-1024975.jpg', '2025-03-26 16:29:15', 0, 0, 0),
(7, 1, 'Une lune de miel qui se passe bien ! ', 5, 'pexels-nurseryart-348520.jpg', '2025-03-26 16:55:59', 0, 0, 0),
(8, 1, 'Parfois les désaccords sont l\'occasion d\'en apprendre plus sur l\'autre !', 3, 'pexels-pengwhan-1767434.jpg', '2025-03-26 16:57:53', 0, 0, 0);

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

--
-- Déclencheurs `shares`
--
DELIMITER $$
CREATE TRIGGER `after_share_deletion` AFTER DELETE ON `shares` FOR EACH ROW BEGIN
    UPDATE posts
    SET nb_likes = (
        SELECT COUNT(DISTINCT user_id)
        FROM likes
        WHERE likes.post_id = OLD.post_id
    )
    WHERE post_id = OLD.post_id;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_share_insertion` AFTER INSERT ON `shares` FOR EACH ROW BEGIN
    UPDATE posts
    SET nb_likes = (
        SELECT COUNT(DISTINCT user_id)
        FROM likes
        WHERE likes.post_id = NEW.post_id
    )
    WHERE post_id = NEW.post_id;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `userlinkinstance`
--

CREATE TABLE `userlinkinstance` (
  `link_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `instance_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `userlinkinstance`
--

INSERT INTO `userlinkinstance` (`link_id`, `user_id`, `instance_id`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 1),
(4, 4, 1),
(5, 5, 1);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_firstname` varchar(50) NOT NULL,
  `user_lastname` varchar(50) NOT NULL,
  `user_slug` varchar(255) NOT NULL,
  `user_pp_path` varchar(255) NOT NULL,
  `user_description` varchar(500) NOT NULL,
  `user_location` varchar(255) NOT NULL,
  `user_work` varchar(255) NOT NULL,
  `user_school` varchar(255) NOT NULL,
  `user_banner_picture_path` varchar(255) NOT NULL,
  `year_of_birth` int(11) NOT NULL,
  `gender` tinyint(1) NOT NULL,
  `website` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`user_id`, `user_firstname`, `user_lastname`, `user_slug`, `user_pp_path`, `user_description`, `user_location`, `user_work`, `user_school`, `user_banner_picture_path`, `year_of_birth`, `gender`, `website`) VALUES
(1, 'Maxime', 'Lambert', 'maxime.lambert.1', 'maxime_lambert.jpg', 'Je suis trop un lover ❤️', 'Angers', 'none', 'Polytech Angers', '', 0, 0, ''),
(2, 'Alain', 'Godon', 'alain.godon.2', 'ID_alain_polytech_NB_max203x270.jpg', 'Je suis les règles de Crocker ! ', 'Angers', 'Polytech Angers', '', 'none', 0, 0, ''),
(3, 'Alexis', 'Paquereau', 'alexis.paquereau.3', '1711984249368.jpg', 'Le rizzler originel...', 'Ton coeur ', 'none', 'Polytech Angers', 'none', 0, 0, ''),
(4, 'Martin', 'Mollat', 'martin.mollat.4', '1727454408757.jpg', 'Le 5 avril je présente un spectacle de théâtre d\'improvisation chez moi, venez nombreux !', 'Angers', 'none', 'Polytech Angers', 'none', 0, 0, ''),
(5, 'Gaston', 'Plot', 'gaston.plot.5', '1636488595977.jpg', 'Work life balance 🎶', 'Angers', 'none', 'Polytech Angers', 'none', 0, 0, '');

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
-- Index pour la table `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`user_id_1`,`user_id_2`,`instance_id`) USING BTREE,
  ADD KEY `user_id_2` (`user_id_2`),
  ADD KEY `FK_FriendsInstanceID` (`instance_id`);

--
-- Index pour la table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`group_id`),
  ADD UNIQUE KEY `group_name` (`group_name`),
  ADD KEY `FK_GroupsInstanceId` (`instance_id`);

--
-- Index pour la table `group_members`
--
ALTER TABLE `group_members`
  ADD PRIMARY KEY (`group_member_id`),
  ADD KEY `FK_GroupMembersGroupId` (`group_id`),
  ADD KEY `FK_GroupMembersUserId` (`user_id`);

--
-- Index pour la table `group_posts`
--
ALTER TABLE `group_posts`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `FK_GroupPostsInstanceId` (`instance_id`),
  ADD KEY `FK_GroupPostsUserId` (`user_id`),
  ADD KEY `FK_GroupPostsGroupId` (`group_id`);

--
-- Index pour la table `identifications`
--
ALTER TABLE `identifications`
  ADD PRIMARY KEY (`user_id`,`post_id`,`instance_id`),
  ADD KEY `FK_IdentificationsPostId` (`post_id`),
  ADD KEY `FK_IdentificationsInstanceId` (`instance_id`);

--
-- Index pour la table `instances`
--
ALTER TABLE `instances`
  ADD PRIMARY KEY (`instance_id`),
  ADD UNIQUE KEY `instance_name` (`instance_name`);

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
  ADD PRIMARY KEY (`link_id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`instance_id`),
  ADD KEY `FK_UserLinkInstanceInstanceId` (`instance_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_slug` (`user_slug`);

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
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `group_members`
--
ALTER TABLE `group_members`
  MODIFY `group_member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `group_posts`
--
ALTER TABLE `group_posts`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `instances`
--
ALTER TABLE `instances`
  MODIFY `instance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `likes`
--
ALTER TABLE `likes`
  MODIFY `like_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `posts`
--
ALTER TABLE `posts`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `shares`
--
ALTER TABLE `shares`
  MODIFY `share_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `userlinkinstance`
--
ALTER TABLE `userlinkinstance`
  MODIFY `link_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
-- Contraintes pour la table `friends`
--
ALTER TABLE `friends`
  ADD CONSTRAINT `FK_FriendsInstanceID` FOREIGN KEY (`instance_id`) REFERENCES `instances` (`instance_id`),
  ADD CONSTRAINT `friends_ibfk_1` FOREIGN KEY (`user_id_1`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `friends_ibfk_2` FOREIGN KEY (`user_id_2`) REFERENCES `users` (`user_id`);

--
-- Contraintes pour la table `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `FK_GroupsInstanceId` FOREIGN KEY (`instance_id`) REFERENCES `instances` (`instance_id`);

--
-- Contraintes pour la table `group_members`
--
ALTER TABLE `group_members`
  ADD CONSTRAINT `FK_GroupMembersGroupId` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`),
  ADD CONSTRAINT `FK_GroupMembersUserId` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Contraintes pour la table `group_posts`
--
ALTER TABLE `group_posts`
  ADD CONSTRAINT `FK_GroupPostsGroupId` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`),
  ADD CONSTRAINT `FK_GroupPostsInstanceId` FOREIGN KEY (`instance_id`) REFERENCES `instances` (`instance_id`),
  ADD CONSTRAINT `FK_GroupPostsUserId` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Contraintes pour la table `identifications`
--
ALTER TABLE `identifications`
  ADD CONSTRAINT `FK_IdentificationsInstanceId` FOREIGN KEY (`instance_id`) REFERENCES `instances` (`instance_id`),
  ADD CONSTRAINT `FK_IdentificationsPostId` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`),
  ADD CONSTRAINT `FK_IdentificationsUserId` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

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
