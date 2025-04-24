-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 11 mars 2025 à 22:17
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
-- Base de données : `nr_instagram`
--
CREATE DATABASE IF NOT EXISTS `nr_instagram` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `nr_instagram`;

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `time_stamp` datetime NOT NULL DEFAULT current_timestamp(),
  `nb_responses` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `comments`
--


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
CREATE TRIGGER `after_comment_insert` AFTER INSERT ON `comments` FOR EACH ROW BEGIN
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
-- Structure de la table `identification`
--

CREATE TABLE `identification` (
  `identification_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `instance_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `identification`
--


-- --------------------------------------------------------

--
-- Structure de la table `instance`
--

CREATE TABLE `instance` (
  `instance_id` int(11) NOT NULL,
  `average_age` int(11) NOT NULL,
  `gender_prop` int(11) NOT NULL,
  `population` int(11) NOT NULL,
  `instance_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `instance`
--

-- --------------------------------------------------------

--
-- Structure de la table `likes`
--

CREATE TABLE `likes` (
  `like_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `likes`
--


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
CREATE TRIGGER `after_like_insert` AFTER INSERT ON `likes` FOR EACH ROW BEGIN
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
  `user_id` int(11) NOT NULL,
  `instance_id` int(11) NOT NULL,
  `nb_likes` int(11) NOT NULL DEFAULT 0,
  `nb_views` int(11) NOT NULL DEFAULT 0,
  `time_stamp` datetime NOT NULL,
  `post_picture_path` text NOT NULL,
  `post_description` varchar(500) NOT NULL,
  `post_location` varchar(50) NOT NULL,
  `nb_comments` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `posts`
--


-- --------------------------------------------------------

--
-- Structure de la table `response`
--

CREATE TABLE `response` (
  `response_id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `time_stamp` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `response`
--


--
-- Déclencheurs `response`
--
DELIMITER $$
CREATE TRIGGER `after_response_deletion` AFTER DELETE ON `response` FOR EACH ROW BEGIN
    UPDATE Comments
    SET nb_responses = (
        SELECT COUNT(DISTINCT response_id)
        FROM response
        WHERE response.comment_id = OLD.comment_id
    )
    WHERE comment_id = OLD.comment_id;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_response_insert` AFTER INSERT ON `response` FOR EACH ROW BEGIN
    -- Update the nb_responses column in the Comments table
    UPDATE comments
    SET nb_responses = (
        SELECT COUNT(DISTINCT response_id)
        FROM response
        WHERE response.comment_id = NEW.comment_id
    )
    WHERE comment_id = NEW.comment_id;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `subscription_id` int(11) NOT NULL,
  `follower_id` int(11) NOT NULL,
  `followed_id` int(11) NOT NULL,
  `instance_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `subscriptions`
--


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


-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_username` varchar(50) NOT NULL,
  `user_firstname` varchar(50) NOT NULL,
  `user_lastname` varchar(50) NOT NULL,
  `user_pp_path` text DEFAULT NULL,
  `user_description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--


--
-- Index pour les tables déchargées
--

--
-- Index pour la table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `FK_CommentsUserid` (`user_id`),
  ADD KEY `FK_Postid` (`post_id`);

--
-- Index pour la table `identification`
--
ALTER TABLE `identification`
  ADD PRIMARY KEY (`identification_id`),
  ADD KEY `userId_fk` (`user_id`),
  ADD KEY `postId_fk` (`post_id`),
  ADD KEY `instanceId_fk` (`instance_id`);

--
-- Index pour la table `instance`
--
ALTER TABLE `instance`
  ADD PRIMARY KEY (`instance_id`);

--
-- Index pour la table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`like_id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`post_id`),
  ADD KEY `FK_PostIdLikes` (`post_id`);

--
-- Index pour la table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `FK_Userid` (`user_id`),
  ADD KEY `FK_Instanceid` (`instance_id`);

--
-- Index pour la table `response`
--
ALTER TABLE `response`
  ADD PRIMARY KEY (`response_id`),
  ADD KEY `FK_Commentid` (`comment_id`),
  ADD KEY `FK_ResponseUserid` (`user_id`);

--
-- Index pour la table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`subscription_id`),
  ADD KEY `FK_followerid` (`follower_id`),
  ADD KEY `FK_followedid` (`followed_id`),
  ADD KEY `FK_SubscriptionInstance` (`instance_id`);

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
  ADD UNIQUE KEY `user_username` (`user_username`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `identification`
--
ALTER TABLE `identification`
  MODIFY `identification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `instance`
--
ALTER TABLE `instance`
  MODIFY `instance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `likes`
--
ALTER TABLE `likes`
  MODIFY `like_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `posts`
--
ALTER TABLE `posts`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `response`
--
ALTER TABLE `response`
  MODIFY `response_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `subscription_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  ADD CONSTRAINT `FK_CommentsUserid` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `FK_Postid` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`);

--
-- Contraintes pour la table `identification`
--
ALTER TABLE `identification`
  ADD CONSTRAINT `instanceId_fk` FOREIGN KEY (`instance_id`) REFERENCES `instance` (`instance_id`),
  ADD CONSTRAINT `postId_fk` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`),
  ADD CONSTRAINT `userId_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Contraintes pour la table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `FK_PostIdLikes` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`),
  ADD CONSTRAINT `FK_UserIdLikes` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Contraintes pour la table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `FK_Instanceid` FOREIGN KEY (`instance_id`) REFERENCES `instance` (`instance_id`),
  ADD CONSTRAINT `FK_Userid` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Contraintes pour la table `response`
--
ALTER TABLE `response`
  ADD CONSTRAINT `FK_Commentid` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`comment_id`),
  ADD CONSTRAINT `FK_ResponseUserid` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Contraintes pour la table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `FK_SubscriptionInstance` FOREIGN KEY (`instance_id`) REFERENCES `instance` (`instance_id`),
  ADD CONSTRAINT `FK_followedid` FOREIGN KEY (`followed_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `FK_followerid` FOREIGN KEY (`follower_id`) REFERENCES `users` (`user_id`);

--
-- Contraintes pour la table `userlinkinstance`
--
ALTER TABLE `userlinkinstance`
  ADD CONSTRAINT `FK_UserLinkInstanceInstanceId` FOREIGN KEY (`instance_id`) REFERENCES `instance` (`instance_id`),
  ADD CONSTRAINT `FK_UserLinkInstanceUserid` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
