<?php

$sqlksajdlajsd = <<<EOD

CREATE TABLE remember_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES accounts(id) ON DELETE CASCADE
);


CREATE TABLE `analytics` (
    `id` int AUTO_INCREMENT PRIMARY KEY,
    `event_type` varchar(255) NULL,
    `page` varchar(255) NULL,
    `user_id` int DEFAULT NULL,
    `ip_address` varchar(45) NOT NULL,
    `ip_address2` varchar(45) DEFAULT NULL,
    `element_id` varchar(255) DEFAULT NULL,
    `monitor_data` varchar(255) DEFAULT NULL,
    `event_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) 

EOD;
