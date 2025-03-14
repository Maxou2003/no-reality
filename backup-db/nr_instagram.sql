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

INSERT INTO `comments` (`comment_id`, `user_id`, `post_id`, `comment_text`, `time_stamp`, `nb_responses`) VALUES
(1, 2, 1, 'vivcqvceqvqvcqk', '2025-02-05 14:59:13', 1),
(2, 3, 3, 'ça travaille dur en Finlande ?!', '2025-02-12 17:34:01', 1),
(3, 1, 3, 'Trop nul ! ', '2025-02-12 17:35:11', 1),
(4, 3, 3, 'Il est pas cool Maxime en vrai, je lui met 0 en OSINT.', '2025-02-12 17:36:28', 1),
(7, 2, 1, 'Ok.', '2025-02-13 11:42:06', 0),
(8, 3, 2, 'Sympa,c\'est où ?', '2025-02-13 11:43:01', 0),
(9, 2, 5, 'Pas cool man...', '2025-02-13 11:43:43', 0);

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

INSERT INTO `identification` (`identification_id`, `post_id`, `user_id`, `instance_id`) VALUES
(1, 1, 2, 1),
(2, 5, 2, 1),
(3, 6, 4, 2),
(4, 7, 5, 2);

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

INSERT INTO `instance` (`instance_id`, `average_age`, `gender_prop`, `population`, `instance_name`) VALUES
(1, 34, 1, 1, 'hates'),
(2, 34, 1, 1, 'test');

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

INSERT INTO `likes` (`like_id`, `user_id`, `post_id`) VALUES
(1, 1, 3),
(3, 3, 1),
(4, 3, 2),
(2, 3, 3),
(6, 3, 4),
(7, 3, 5),
(9, 4, 6),
(8, 5, 7);

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

INSERT INTO `posts` (`post_id`, `user_id`, `instance_id`, `nb_likes`, `nb_views`, `time_stamp`, `post_picture_path`, `post_description`, `post_location`, `nb_comments`) VALUES
(1, 1, 1, 1, 45, '2025-01-23 13:30:47', 'pexels-clement-proust-363898785-14606642.jpg', 'Un super semestre à l\'étranger !', 'Finlande, Helsinki', 2),
(2, 1, 1, 1, 65, '2025-01-29 17:33:30', 'pexels-filatova-1861817299-30427823.jpg', 'Petite photo de zinzin !', 'Finlande, Helsinki (non je dahek)', 1),
(3, 2, 1, 2, 0, '2025-01-29 17:55:52', 'finlande.jpg', 'mon voyage en Finlande', 'Finlande, Helsinki', 3),
(4, 2, 1, 1, 67, '2025-02-12 07:51:43', 'pexels-kaboompics-6256.jpg', 'Un evier...', 'France, Angers', 0),
(5, 1, 1, 1, 53, '2025-02-12 07:53:27', 'pexels-ps-photography-14694-67184.jpg', 'Ce robinet est meilleur !', 'Finlande, Helsinki', 1),
(6, 5, 2, 1, 0, '2025-02-26 16:26:00', 'japan_1_0.jpg', 'Mes vacances au Japon, trop une dingz ! ', 'Japon', 0),
(7, 4, 2, 1, 0, '2025-02-26 16:59:32', 'japan_1_4.jpg', 'I love Japan ! ', 'Japon', 0);

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

INSERT INTO `response` (`response_id`, `comment_id`, `user_id`, `content`, `time_stamp`) VALUES
(1, 2, 2, 'Oui !', '2025-02-12 17:34:37'),
(2, 3, 2, 'Aigri/20', '2025-02-12 17:35:41'),
(3, 4, 1, 'Oh non, je suis désolé, please pas 0.', '2025-02-12 17:36:50'),
(8, 1, 1, 'I beg you pardon ?', '2025-02-13 11:28:18');

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

INSERT INTO `subscriptions` (`subscription_id`, `follower_id`, `followed_id`, `instance_id`) VALUES
(1, 1, 2, 1),
(2, 4, 5, 2),
(3, 5, 4, 2);

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
(4, 4, 2),
(5, 5, 2);

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

INSERT INTO `users` (`user_id`, `user_username`, `user_firstname`, `user_lastname`, `user_pp_path`, `user_description`) VALUES
(1, 'MaxLambert', 'Maxime', 'Lambert', 'maxime_lambert.jpg', 'Hey, je suis Maxime Lambert !\nBienvenue sur mon Instagram !\nJ\'aime le handball, et les jeux de sociétés !'),
(2, 'Siphy666', 'Alexis', 'Paquereau--Gasnier', '1711984249368.jpg', 'Hey la team ! Comment va ?'),
(3, 'Algo', 'Alain', 'Godon', 'ID_alain_polytech_NB_max203x270.jpg', 'Otp leona plat sur la faille de l\'invocateur'),
(4, 'LeGoat', 'Martin', 'Mollat', '1727454408757.jpg', 'Théâtre d\'impro ce soir ouvert à tous !'),
(5, 'PlotG', 'Gaston', 'Plot', '1636488595977.jpg', 'J\'aime beaucoup les restaurants chinois à volonté ! ');

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
