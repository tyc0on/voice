<?php
declare(strict_types=1);

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    echo "This script can only be executed from the command line." . PHP_EOL;
    exit(1);
}

require_once __DIR__ . '/../core/stripe/service.php';

try {
    $db = ensure_database_connection();
} catch (Throwable $e) {
    fwrite(STDERR, 'Database connection failed: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}

// Make mysqli throw exceptions so we get a clear failure if any statement breaks.
if (function_exists('mysqli_report')) {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
}

$queries = [
    // 1) Customers
    // NOTE: account_id is a **signed** INT to match accounts.id
    'CREATE TABLE IF NOT EXISTS stripe_customers (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        account_id INT NOT NULL,
        stripe_customer_id VARCHAR(255) NOT NULL,
        email VARCHAR(255) DEFAULT NULL,
        default_payment_method VARCHAR(255) DEFAULT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_customer (stripe_customer_id),
        UNIQUE KEY uniq_account (account_id),
        CONSTRAINT fk_stripe_customers_account
            FOREIGN KEY (account_id) REFERENCES accounts(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',

    // 2) Subscriptions
    // NOTE: account_id is a **signed** INT to match accounts.id
    'CREATE TABLE IF NOT EXISTS stripe_subscriptions (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        stripe_subscription_id VARCHAR(255) NOT NULL,
        stripe_customer_id VARCHAR(255) NOT NULL,
        account_id INT NOT NULL,
        price_id VARCHAR(255) DEFAULT NULL,
        product_id VARCHAR(255) DEFAULT NULL,
        status VARCHAR(64) NOT NULL,
        current_period_start DATETIME DEFAULT NULL,
        current_period_end DATETIME DEFAULT NULL,
        cancel_at DATETIME DEFAULT NULL,
        cancel_at_period_end TINYINT(1) NOT NULL DEFAULT 0,
        metadata LONGTEXT DEFAULT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_subscription (stripe_subscription_id),
        KEY idx_customer (stripe_customer_id),
        KEY idx_account (account_id),
        CONSTRAINT fk_stripe_subscriptions_account
            FOREIGN KEY (account_id) REFERENCES accounts(id) ON DELETE CASCADE,
        CONSTRAINT fk_stripe_subscriptions_customer
            FOREIGN KEY (stripe_customer_id) REFERENCES stripe_customers(stripe_customer_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',

    // 3) Webhook events
    'CREATE TABLE IF NOT EXISTS stripe_webhook_events (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        event_id VARCHAR(255) NOT NULL,
        type VARCHAR(255) NOT NULL,
        payload LONGTEXT NOT NULL,
        status VARCHAR(64) NOT NULL,
        error_message TEXT DEFAULT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        processed_at DATETIME DEFAULT NULL,
        UNIQUE KEY uniq_event (event_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
];

try {
    foreach ($queries as $query) {
        $db->query($query);
    }
    echo "Stripe tables are up to date." . PHP_EOL;
} catch (Throwable $e) {
    fwrite(STDERR, 'Migration failed: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
