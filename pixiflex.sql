-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 10, 2017 at 12:17 PM
-- Server version: 10.1.16-MariaDB
-- PHP Version: 7.0.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pixiflex`
--

-- --------------------------------------------------------

--
-- Table structure for table `app_payments`
--

CREATE TABLE `app_payments` (
  `id` int(11) NOT NULL,
  `store_id` bigint(20) NOT NULL,
  `store_name` varchar(255) NOT NULL,
  `pay_id` varchar(50) NOT NULL,
  `plan_name` varchar(255) NOT NULL,
  `price` varchar(20) NOT NULL,
  `trial_days` bigint(20) NOT NULL,
  `trial_ends_on` varchar(50) NOT NULL DEFAULT '0',
  `status` varchar(20) NOT NULL,
  `created_at` varchar(70) NOT NULL,
  `updated_at` varchar(70) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_facebook_pixel_accounts`
--

CREATE TABLE `tbl_facebook_pixel_accounts` (
  `id` bigint(21) NOT NULL,
  `store_id` int(11) NOT NULL,
  `facebook_pixel_id` varchar(255) NOT NULL,
  `created_at` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_usersettings`
--

CREATE TABLE `tbl_usersettings` (
  `id` int(11) NOT NULL,
  `access_token` text NOT NULL,
  `store_name` varchar(300) NOT NULL,
  `settings` varchar(900) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_webhooks`
--

CREATE TABLE `tbl_webhooks` (
  `id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `webhook_id` varchar(50) NOT NULL,
  `hook_url` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `webhook_response`
--

CREATE TABLE `webhook_response` (
  `id` bigint(20) NOT NULL,
  `storeid` bigint(20) NOT NULL,
  `hook_type` enum('order','account','uninstall') NOT NULL,
  `hook_response` text NOT NULL,
  `hook_read` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `app_payments`
--
ALTER TABLE `app_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_facebook_pixel_accounts`
--
ALTER TABLE `tbl_facebook_pixel_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_usersettings`
--
ALTER TABLE `tbl_usersettings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_webhooks`
--
ALTER TABLE `tbl_webhooks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `webhook_response`
--
ALTER TABLE `webhook_response`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `app_payments`
--
ALTER TABLE `app_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tbl_facebook_pixel_accounts`
--
ALTER TABLE `tbl_facebook_pixel_accounts`
  MODIFY `id` bigint(21) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tbl_usersettings`
--
ALTER TABLE `tbl_usersettings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tbl_webhooks`
--
ALTER TABLE `tbl_webhooks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `webhook_response`
--
ALTER TABLE `webhook_response`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
