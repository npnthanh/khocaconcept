-- Chạy một lần trên database đang vận hành để bật tính năng tìm kiếm nâng cao.
-- Dành cho MariaDB/MySQL; hãy sao lưu database trước khi chạy.

ALTER TABLE `books`
    ADD COLUMN `language` varchar(100) DEFAULT NULL AFTER `publisher`,
    ADD COLUMN `book_condition` varchar(24) NOT NULL DEFAULT 'unknown' AFTER `language`,
    ADD COLUMN `availability_status` varchar(24) NOT NULL DEFAULT 'available' AFTER `book_condition`,
    ADD COLUMN `search_text` text DEFAULT NULL AFTER `additional_information`,
    ADD INDEX `books_language_book_condition_availability_status_index` (`language`, `book_condition`, `availability_status`);

ALTER TABLE `categories`
    ADD COLUMN `search_name` varchar(255) DEFAULT NULL AFTER `name`;

CREATE TABLE IF NOT EXISTS `search_queries` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `term` varchar(160) NOT NULL,
    `normalized_term` varchar(160) NOT NULL,
    `hits` int(10) unsigned NOT NULL DEFAULT 0,
    `last_searched_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `search_queries_normalized_term_unique` (`normalized_term`),
    KEY `search_queries_last_searched_at_index` (`last_searched_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu cũ vẫn tìm kiếm không dấu nhờ collation utf8mb4_unicode_ci.
-- search_text/search_name là bản sao hỗ trợ tìm kiếm nhanh cho dữ liệu mới.
UPDATE `books`
SET `search_text` = LOWER(CONCAT_WS(' ', `title`, `author`, `publisher`, `summary`, `additional_information`, `language`))
WHERE `search_text` IS NULL;

UPDATE `categories`
SET `search_name` = LOWER(`name`)
WHERE `search_name` IS NULL;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_08_17_000003_add_smart_search_fields', 4
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations`
    WHERE `migration` = '2026_08_17_000003_add_smart_search_fields'
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_08_17_000004_create_search_queries_table', 4
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations`
    WHERE `migration` = '2026_08_17_000004_create_search_queries_table'
);
