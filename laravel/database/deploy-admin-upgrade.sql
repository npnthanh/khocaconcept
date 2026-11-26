-- Nâng cấp khu vực quản trị: chạy một lần trên database đang vận hành.
-- File này giả định database đã chạy deploy-smart-search.sql ở phần trước.
-- Hãy backup database trước khi chạy.

ALTER TABLE `books`
    ADD COLUMN `is_featured` tinyint(1) NOT NULL DEFAULT 0 AFTER `is_published`,
    ADD COLUMN `featured_sort_order` int(10) unsigned NOT NULL DEFAULT 0 AFTER `is_featured`,
    ADD COLUMN `meta_title` varchar(255) DEFAULT NULL AFTER `featured_sort_order`,
    ADD COLUMN `meta_description` text DEFAULT NULL AFTER `meta_title`,
    ADD COLUMN `og_image` varchar(255) DEFAULT NULL AFTER `meta_description`,
    ADD INDEX `books_is_featured_index` (`is_featured`);

ALTER TABLE `search_queries`
    ADD COLUMN `result_count` int(10) unsigned DEFAULT NULL AFTER `hits`,
    ADD COLUMN `no_result_hits` int(10) unsigned NOT NULL DEFAULT 0 AFTER `result_count`;

CREATE TABLE IF NOT EXISTS `collections` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `slug` varchar(255) NOT NULL,
    `subtitle` varchar(255) DEFAULT NULL,
    `description` text DEFAULT NULL,
    `cover_image` varchar(255) DEFAULT NULL,
    `is_featured` tinyint(1) NOT NULL DEFAULT 0,
    `is_published` tinyint(1) NOT NULL DEFAULT 1,
    `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
    `meta_title` varchar(255) DEFAULT NULL,
    `meta_description` text DEFAULT NULL,
    `og_image` varchar(255) DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `collections_slug_unique` (`slug`),
    KEY `collections_is_featured_index` (`is_featured`),
    KEY `collections_is_published_index` (`is_published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `collection_book` (
    `collection_id` bigint(20) unsigned NOT NULL,
    `book_id` bigint(20) unsigned NOT NULL,
    `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`collection_id`, `book_id`),
    CONSTRAINT `collection_book_collection_id_foreign` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`id`) ON DELETE CASCADE,
    CONSTRAINT `collection_book_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `banners` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `eyebrow` varchar(255) DEFAULT NULL,
    `description` text DEFAULT NULL,
    `image_path` varchar(255) DEFAULT NULL,
    `link_url` varchar(255) DEFAULT NULL,
    `link_label` varchar(255) DEFAULT NULL,
    `placement` varchar(40) NOT NULL DEFAULT 'home',
    `starts_at` timestamp NULL DEFAULT NULL,
    `ends_at` timestamp NULL DEFAULT NULL,
    `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `banners_is_active_index` (`is_active`),
    KEY `banners_placement_starts_at_ends_at_index` (`placement`, `starts_at`, `ends_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `analytics_events` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `event_type` varchar(40) NOT NULL,
    `book_id` bigint(20) unsigned DEFAULT NULL,
    `metadata` longtext DEFAULT NULL,
    `ip_address` varchar(45) DEFAULT NULL,
    `user_agent` text DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `analytics_events_event_type_created_at_index` (`event_type`, `created_at`),
    KEY `analytics_events_book_id_event_type_index` (`book_id`, `event_type`),
    CONSTRAINT `analytics_events_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_08_18_000005_add_seo_and_search_metrics', 5
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `migrations` WHERE `migration` = '2026_08_18_000005_add_seo_and_search_metrics');
INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_08_18_000006_create_collections_table', 5
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `migrations` WHERE `migration` = '2026_08_18_000006_create_collections_table');
INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_08_18_000007_create_banners_table', 5
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `migrations` WHERE `migration` = '2026_08_18_000007_create_banners_table');
INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_08_18_000008_create_analytics_events_table', 5
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `migrations` WHERE `migration` = '2026_08_18_000008_create_analytics_events_table');
