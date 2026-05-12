-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 12, 2026 at 09:25 PM
-- Server version: 8.0.27
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `logistics_tracking`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_details`
--

CREATE TABLE `admin_details` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_registration` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verified` tinyint(1) DEFAULT '0',
  `verified_by` bigint UNSIGNED DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `admin_code` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_details`
--

INSERT INTO `admin_details` (`id`, `user_id`, `company_name`, `company_registration`, `position`, `verified`, `verified_by`, `verified_at`, `created_at`, `updated_at`, `admin_code`) VALUES
(1, 6, 'FLEXY AND .CO', 'NIKE12345', 'logistic manager', 0, NULL, NULL, '2026-03-07 09:17:43', NULL, NULL),
(2, 7, 'FLEXY AND .CO', 'NIKE12345', 'logistic manager', 0, NULL, NULL, '2026-03-07 09:54:53', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `current_locations`
--

CREATE TABLE `current_locations` (
  `tracking_number` varchar(50) NOT NULL,
  `shipment_id` bigint UNSIGNED NOT NULL,
  `vehicle_id` bigint UNSIGNED DEFAULT NULL,
  `driver_id` bigint UNSIGNED DEFAULT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `speed_kmh` decimal(5,2) DEFAULT NULL,
  `heading` smallint DEFAULT NULL,
  `current_status` varchar(30) DEFAULT NULL,
  `last_update` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `current_locations`
--

INSERT INTO `current_locations` (`tracking_number`, `shipment_id`, `vehicle_id`, `driver_id`, `latitude`, `longitude`, `speed_kmh`, `heading`, `current_status`, `last_update`, `created_at`) VALUES
('LGS260307598', 1, 1, NULL, 41.80000000, -77.50000000, 72.00, 280, 'in_transit', '2026-03-02 11:24:11', '2026-03-02 11:24:11');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `driver_details`
--

CREATE TABLE `driver_details` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `license_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `experience_years` int DEFAULT '0',
  `vehicle_type_preference` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verified` tinyint(1) DEFAULT '0',
  `verified_by` bigint UNSIGNED DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `driver_details`
--

INSERT INTO `driver_details` (`id`, `user_id`, `license_number`, `experience_years`, `vehicle_type_preference`, `verified`, `verified_by`, `verified_at`, `created_at`, `updated_at`) VALUES
(1, 3, '123456789', 0, 'truck', 0, NULL, NULL, '2026-03-07 09:03:49', NULL),
(2, 9, '1234567', 3, 'motorcycle', 0, NULL, NULL, '2026-03-12 17:57:42', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `driver_locations`
--

CREATE TABLE `driver_locations` (
  `id` int NOT NULL,
  `driver_id` int NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `speed` float DEFAULT '0',
  `heading` float DEFAULT '0',
  `accuracy` float DEFAULT NULL,
  `status` enum('active','idle','offline') DEFAULT 'active',
  `recorded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `driver_locations`
--

INSERT INTO `driver_locations` (`id`, `driver_id`, `latitude`, `longitude`, `speed`, `heading`, `accuracy`, `status`, `recorded_at`, `created_at`) VALUES
(1, 3, 4.08164589, 9.72951469, 0, 0, 134, 'active', '2026-05-08 19:51:33', '2026-05-08 19:51:33'),
(2, 3, 4.08164589, 9.72951469, 0, 0, 134, 'active', '2026-05-08 19:51:33', '2026-05-08 19:51:33'),
(3, 3, 4.08164589, 9.72951469, 0, 0, 134, 'active', '2026-05-08 19:51:33', '2026-05-08 19:51:33'),
(4, 3, 4.08164589, 9.72951469, 0, 0, 134, 'active', '2026-05-08 19:51:39', '2026-05-08 19:51:39');

-- --------------------------------------------------------

--
-- Table structure for table `driver_ratings`
--

CREATE TABLE `driver_ratings` (
  `id` int NOT NULL,
  `driver_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `order_id` int DEFAULT NULL,
  `rating` decimal(2,1) NOT NULL COMMENT '1.0 to 5.0',
  `review` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shipments`
--

CREATE TABLE `shipments` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` char(36) DEFAULT (uuid()),
  `tracking_number` varchar(50) NOT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) DEFAULT NULL,
  `customer_phone` varchar(50) DEFAULT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_description` text,
  `item_category` varchar(100) DEFAULT NULL,
  `item_weight` decimal(10,2) DEFAULT NULL,
  `item_value` decimal(12,2) DEFAULT NULL,
  `is_fragile` tinyint(1) DEFAULT '0',
  `origin_warehouse_id` bigint UNSIGNED DEFAULT NULL,
  `origin_address` text NOT NULL,
  `origin_city` varchar(100) DEFAULT NULL,
  `origin_lat` decimal(10,8) DEFAULT NULL,
  `origin_lon` decimal(11,8) DEFAULT NULL,
  `destination_address` text NOT NULL,
  `destination_city` varchar(100) DEFAULT NULL,
  `destination_lat` decimal(10,8) DEFAULT NULL,
  `destination_lon` decimal(11,8) DEFAULT NULL,
  `assigned_vehicle_id` bigint UNSIGNED DEFAULT NULL,
  `assigned_driver_id` bigint UNSIGNED DEFAULT NULL,
  `status` enum('pending','picked_up','in_transit','out_for_delivery','delivered','failed_delivery','returned','cancelled') DEFAULT 'pending',
  `pickup_time` timestamp NULL DEFAULT NULL,
  `estimated_delivery_time` timestamp NULL DEFAULT NULL,
  `actual_delivery_time` timestamp NULL DEFAULT NULL,
  `notes` text,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `shipments`
--

INSERT INTO `shipments` (`id`, `uuid`, `tracking_number`, `customer_id`, `customer_name`, `customer_email`, `customer_phone`, `item_name`, `item_description`, `item_category`, `item_weight`, `item_value`, `is_fragile`, `origin_warehouse_id`, `origin_address`, `origin_city`, `origin_lat`, `origin_lon`, `destination_address`, `destination_city`, `destination_lat`, `destination_lon`, `assigned_vehicle_id`, `assigned_driver_id`, `status`, `pickup_time`, `estimated_delivery_time`, `actual_delivery_time`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, '5623d3a9-162a-11f1-9aaf-8470a7f5d448', 'LGS260307598', NULL, 'John Smith', 'john@example.com', '+1234567890', 'Electronics Package', NULL, NULL, 2.50, NULL, 0, NULL, '123 Industrial Blvd, Brooklyn, NY', 'New York', 40.71280000, -74.00600000, '456 Oak St, Chicago, IL', 'Chicago', 41.87810000, -87.62980000, 1, NULL, 'pending', NULL, '2026-03-05 11:24:11', NULL, NULL, NULL, '2026-03-02 11:24:11', '2026-03-02 11:24:11');

-- --------------------------------------------------------

--
-- Table structure for table `tracking_events`
--

CREATE TABLE `tracking_events` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` char(36) DEFAULT (uuid()),
  `tracking_number` varchar(50) NOT NULL,
  `shipment_id` bigint UNSIGNED NOT NULL,
  `vehicle_id` bigint UNSIGNED DEFAULT NULL,
  `driver_id` bigint UNSIGNED DEFAULT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `speed_kmh` decimal(5,2) DEFAULT NULL,
  `heading` smallint DEFAULT NULL,
  `event_type` varchar(50) DEFAULT 'location_update',
  `event_source` varchar(50) DEFAULT 'gps',
  `event_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tracking_events`
--

INSERT INTO `tracking_events` (`id`, `uuid`, `tracking_number`, `shipment_id`, `vehicle_id`, `driver_id`, `latitude`, `longitude`, `speed_kmh`, `heading`, `event_type`, `event_source`, `event_time`, `metadata`, `created_at`) VALUES
(1, '563562ea-162a-11f1-9aaf-8470a7f5d448', 'LGS260307598', 1, 1, NULL, 40.71280000, -74.00600000, 0.00, 0, 'location_update', 'gps', '2026-03-02 09:24:11', NULL, '2026-03-02 11:24:11'),
(2, '56357680-162a-11f1-9aaf-8470a7f5d448', 'LGS260307598', 1, 1, NULL, 40.80000000, -74.50000000, 65.00, 270, 'location_update', 'gps', '2026-03-02 10:24:11', NULL, '2026-03-02 11:24:11'),
(3, '56357a00-162a-11f1-9aaf-8470a7f5d448', 'LGS260307598', 1, 1, NULL, 41.20000000, -75.50000000, 70.00, 280, 'location_update', 'gps', '2026-03-02 10:54:11', NULL, '2026-03-02 11:24:11'),
(4, '56357e13-162a-11f1-9aaf-8470a7f5d448', 'LGS260307598', 1, 1, NULL, 41.50000000, -76.50000000, 68.00, 275, 'location_update', 'gps', '2026-03-02 11:09:11', NULL, '2026-03-02 11:24:11'),
(5, '5635817e-162a-11f1-9aaf-8470a7f5d448', 'LGS260307598', 1, 1, NULL, 41.80000000, -77.50000000, 72.00, 280, 'location_update', 'gps', '2026-03-02 11:24:11', NULL, '2026-03-02 11:24:11');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` char(36) DEFAULT (uuid()),
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `role` enum('admin','dispatcher','driver','customer') NOT NULL DEFAULT 'customer',
  `is_active` tinyint(1) DEFAULT '1',
  `approval_status` enum('pending','approved','rejected') DEFAULT 'approved',
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint DEFAULT NULL,
  `rejection_reason` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_login` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `uuid`, `username`, `email`, `password_hash`, `first_name`, `last_name`, `phone`, `role`, `is_active`, `approval_status`, `approved_at`, `approved_by`, `rejection_reason`, `created_at`, `updated_at`, `last_login`, `remember_token`) VALUES
(1, '5605ab97-162a-11f1-9aaf-8470a7f5d448', 'admin', 'admin@logistics.com', 'admin123', 'System', 'Admin', NULL, 'admin', 1, 'approved', NULL, NULL, NULL, '2026-03-02 11:24:11', '2026-03-02 11:24:11', NULL, NULL),
(3, '8e4c5621-1a04-11f1-888c-ac3e3449d13f', 'john534', 'john@gmail.com', '$2y$10$QYMXpC6lohR/I5YX8EPVk.V5Rri1PxKknji1fvgmu.3OqEm6AeKyC', 'Nike', 'John', '651697854', 'driver', 1, 'approved', NULL, NULL, NULL, '2026-03-07 09:03:49', '2026-05-08 19:17:09', '2026-05-08 19:17:09', NULL),
(4, 'a9b86a2c-1a04-11f1-888c-ac3e3449d13f', 'joh852', 'joh@gmail.com', '$2y$10$qIjpU.vsWo7ObpPcVi8ch.GB34aasubk98QvukC9Ui50ytOrx/1DC', 'Nike', 'John', '651697854', 'driver', 1, 'approved', NULL, NULL, NULL, '2026-03-07 09:04:35', '2026-03-23 19:09:41', '2026-03-23 19:09:41', NULL),
(6, '7faba1c2-1a06-11f1-888c-ac3e3449d13f', 'nike912', 'nike@gmail.com', '$2y$10$O8MTkWo6pQdxKAYD7ZYbo.GR93A8XCQhGpAeoU/MVQljGu8dWQRO2', 'Ningha', 'Nike', '651697854', 'admin', 1, 'pending', NULL, NULL, NULL, '2026-03-07 09:17:43', '2026-05-08 19:11:25', NULL, NULL),
(7, 'b0d18379-1a0b-11f1-888c-ac3e3449d13f', 'mike730', 'mike@gmail.com', '$2y$10$C3B/qW2rj2./hfT/BuvGj.Q3T8hg6bLZAVrppYb7xTfVl5n1uzimS', 'Ningha', 'Nike', '651697854', 'admin', 1, 'approved', NULL, NULL, NULL, '2026-03-07 09:54:53', '2026-03-23 19:04:47', '2026-03-23 19:04:47', NULL),
(8, 'dddbe239-1e3b-11f1-af86-ecf4bb323a3d', 'joelle919', 'joelle@gmail.com', '$2y$10$hF.Mag0Jpp/4dzrF/rTXGeCVv/E2O6MApL8Qu0/xjJuGqWnRrKeJi', 'Tchoffo', 'joelle', '6765625278', 'customer', 1, 'approved', '2026-03-23 19:09:05', 7, NULL, '2026-03-12 17:49:49', '2026-03-23 19:09:05', '2026-03-12 17:50:46', NULL),
(9, 'f7b51550-1e3c-11f1-af86-ecf4bb323a3d', 'like330', 'like@gmail.com', '$2y$10$otR5WZUh37CqGc56UF/vIOToyUvBHZv/78Vf36WEBpvhg146D3pPu', 'favor', 'nike', '673516978', 'driver', 1, 'rejected', NULL, NULL, 'personal', '2026-03-12 17:57:42', '2026-03-23 19:15:30', '2026-03-23 19:15:30', NULL),
(10, 'd87a6e7a-26ec-11f1-a98f-ecf4bb323a3d', 'carl237', 'carl@gmail.com', '$2y$10$exxue6gxbQbTI1KvkZ68Xuv4aJOc4SjJc1SkK6lKTOcmB/2T7tKAK', 'bob', 'carl', '12345678', 'customer', 1, 'pending', NULL, NULL, NULL, '2026-03-23 19:16:51', '2026-03-23 19:17:10', '2026-03-23 19:17:10', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` char(36) DEFAULT (uuid()),
  `vehicle_number` varchar(50) NOT NULL,
  `license_plate` varchar(20) NOT NULL,
  `type` enum('truck','van','motorcycle') DEFAULT 'truck',
  `capacity_kg` decimal(10,2) DEFAULT NULL,
  `capacity_m3` decimal(10,2) DEFAULT NULL,
  `status` enum('available','in_transit','maintenance','offline') DEFAULT 'available',
  `current_driver_id` bigint UNSIGNED DEFAULT NULL,
  `device_id` varchar(100) DEFAULT NULL,
  `device_model` varchar(100) DEFAULT NULL,
  `last_ping` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `uuid`, `vehicle_number`, `license_plate`, `type`, `capacity_kg`, `capacity_m3`, `status`, `current_driver_id`, `device_id`, `device_model`, `last_ping`, `created_at`, `updated_at`) VALUES
(1, '5619068d-162a-11f1-9aaf-8470a7f5d448', 'VH001', 'ABC-1234', 'truck', 5000.00, NULL, 'maintenance', NULL, NULL, NULL, NULL, '2026-03-02 11:24:11', '2026-03-07 11:26:51'),
(2, '5619ce97-162a-11f1-9aaf-8470a7f5d448', 'VH002', 'XYZ-5678', 'van', 1000.00, NULL, 'available', NULL, NULL, NULL, NULL, '2026-03-02 11:24:11', '2026-03-07 10:19:50'),
(3, '5619d314-162a-11f1-9aaf-8470a7f5d448', 'VH003', 'DEF-9012', 'motorcycle', 100.00, NULL, 'available', NULL, NULL, NULL, NULL, '2026-03-02 11:24:11', '2026-03-02 11:24:11');

-- --------------------------------------------------------

--
-- Table structure for table `warehouses`
--

CREATE TABLE `warehouses` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` char(36) DEFAULT (uuid()),
  `name` varchar(255) NOT NULL,
  `code` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `contact_name` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(50) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `warehouses`
--

INSERT INTO `warehouses` (`id`, `uuid`, `name`, `code`, `address`, `city`, `state`, `country`, `postal_code`, `latitude`, `longitude`, `contact_name`, `contact_phone`, `contact_email`, `created_at`, `updated_at`) VALUES
(1, '561021a7-162a-11f1-9aaf-8470a7f5d448', 'Main Warehouse NYC', 'NYC01', '123 Industrial Blvd, Brooklyn, NY 11201', 'New York', NULL, NULL, NULL, 40.71280000, -74.00600000, NULL, NULL, NULL, '2026-03-02 11:24:11', '2026-03-02 11:24:11'),
(2, '5610706c-162a-11f1-9aaf-8470a7f5d448', 'Chicago Distribution', 'CHI01', '456 Logistics Ave, Chicago, IL 60601', 'Chicago', NULL, NULL, NULL, 41.87810000, -87.62980000, NULL, NULL, NULL, '2026-03-02 11:24:11', '2026-03-02 11:24:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `admin_details`
--
ALTER TABLE `admin_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user` (`user_id`),
  ADD KEY `verified_by` (`verified_by`);

--
-- Indexes for table `current_locations`
--
ALTER TABLE `current_locations`
  ADD PRIMARY KEY (`tracking_number`),
  ADD KEY `shipment_id` (`shipment_id`),
  ADD KEY `driver_id` (`driver_id`),
  ADD KEY `idx_current_vehicle` (`vehicle_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `driver_details`
--
ALTER TABLE `driver_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user` (`user_id`),
  ADD KEY `verified_by` (`verified_by`);

--
-- Indexes for table `driver_locations`
--
ALTER TABLE `driver_locations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `driver_id` (`driver_id`);

--
-- Indexes for table `driver_ratings`
--
ALTER TABLE `driver_ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `driver_id` (`driver_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `shipments`
--
ALTER TABLE `shipments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tracking_number` (`tracking_number`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD KEY `origin_warehouse_id` (`origin_warehouse_id`),
  ADD KEY `assigned_vehicle_id` (`assigned_vehicle_id`),
  ADD KEY `assigned_driver_id` (`assigned_driver_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_shipments_tracking` (`tracking_number`),
  ADD KEY `idx_shipments_customer` (`customer_id`),
  ADD KEY `idx_shipments_status` (`status`),
  ADD KEY `idx_shipments_estimated_delivery` (`estimated_delivery_time`);

--
-- Indexes for table `tracking_events`
--
ALTER TABLE `tracking_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `driver_id` (`driver_id`),
  ADD KEY `idx_tracking_number` (`tracking_number`,`event_time`),
  ADD KEY `idx_tracking_shipment` (`shipment_id`,`event_time`),
  ADD KEY `idx_tracking_vehicle` (`vehicle_id`,`event_time`),
  ADD KEY `idx_tracking_time` (`event_time`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD KEY `idx_users_email` (`email`),
  ADD KEY `idx_users_role` (`role`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehicle_number` (`vehicle_number`),
  ADD UNIQUE KEY `license_plate` (`license_plate`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD UNIQUE KEY `device_id` (`device_id`),
  ADD KEY `current_driver_id` (`current_driver_id`),
  ADD KEY `idx_vehicles_vehicle_number` (`vehicle_number`),
  ADD KEY `idx_vehicles_status` (`status`);

--
-- Indexes for table `warehouses`
--
ALTER TABLE `warehouses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD KEY `idx_warehouses_code` (`code`),
  ADD KEY `idx_warehouses_city` (`city`),
  ADD KEY `idx_warehouses_location` (`latitude`,`longitude`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_details`
--
ALTER TABLE `admin_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `driver_details`
--
ALTER TABLE `driver_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `driver_locations`
--
ALTER TABLE `driver_locations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `driver_ratings`
--
ALTER TABLE `driver_ratings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shipments`
--
ALTER TABLE `shipments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tracking_events`
--
ALTER TABLE `tracking_events`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_details`
--
ALTER TABLE `admin_details`
  ADD CONSTRAINT `admin_details_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `admin_details_ibfk_2` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `current_locations`
--
ALTER TABLE `current_locations`
  ADD CONSTRAINT `current_locations_ibfk_1` FOREIGN KEY (`shipment_id`) REFERENCES `shipments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `current_locations_ibfk_2` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `current_locations_ibfk_3` FOREIGN KEY (`driver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `driver_details`
--
ALTER TABLE `driver_details`
  ADD CONSTRAINT `driver_details_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `driver_details_ibfk_2` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_driver_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shipments`
--
ALTER TABLE `shipments`
  ADD CONSTRAINT `shipments_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `shipments_ibfk_2` FOREIGN KEY (`origin_warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `shipments_ibfk_3` FOREIGN KEY (`assigned_vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `shipments_ibfk_4` FOREIGN KEY (`assigned_driver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `shipments_ibfk_5` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tracking_events`
--
ALTER TABLE `tracking_events`
  ADD CONSTRAINT `tracking_events_ibfk_1` FOREIGN KEY (`shipment_id`) REFERENCES `shipments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tracking_events_ibfk_2` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tracking_events_ibfk_3` FOREIGN KEY (`driver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `vehicles_ibfk_1` FOREIGN KEY (`current_driver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
