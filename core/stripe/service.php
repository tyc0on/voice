<?php
declare(strict_types=1);

use Stripe\Event;

require_once __DIR__ . '/bootstrap.php';

/**
 * Map Stripe price identifiers to internal account tiers and human readable plan names.
 */
function stripe_price_catalog(): array
{
    return [
        'price_1SFQNx3ojSJuqvpDD4PDtMv0' => ['account_type' => 'BASIC', 'plan_name' => 'Plus – Yearly'],
        'price_1SFQOH3ojSJuqvpDCOWwU1rr' => ['account_type' => 'BASIC', 'plan_name' => 'Plus – Monthly'],
        'price_1SFQQ93ojSJuqvpD8y70kqj2' => ['account_type' => 'ADVANCED', 'plan_name' => 'Pro – Yearly'],
        'price_1SFQQc3ojSJuqvpDZR22HPyR' => ['account_type' => 'ADVANCED', 'plan_name' => 'Pro – Monthly'],
        'price_1SFQDW3ojSJuqvpDqacfFGyH' => ['account_type' => 'TRIAL', 'plan_name' => 'Free – Yearly'],
        'price_1SFQDj3ojSJuqvpDLJzHLZMh' => ['account_type' => 'TRIAL', 'plan_name' => 'Free – Monthly'],
    ];
}

function stripe_lookup_plan(string $priceId): array
{
    $catalog = stripe_price_catalog();
    return $catalog[$priceId] ?? ['account_type' => 'TRIAL', 'plan_name' => 'Free'];
}

function stripe_should_upgrade(string $status): bool
{
    return in_array(strtolower($status), ['trialing', 'active', 'past_due'], true);
}

function stripe_should_downgrade(string $status): bool
{
    return in_array(strtolower($status), ['canceled', 'unpaid', 'incomplete_expired'], true);
}

function ensure_database_connection(): mysqli
{
    if (!file_exists(__DIR__ . '/../../include.php')) {
        throw new RuntimeException('Database configuration include.php is missing.');
    }

    require __DIR__ . '/../../include.php';

    $con = new mysqli($sqlh, $sqlu, $sqlp, $sqld);
    if ($con->connect_errno) {
        throw new RuntimeException('Failed to connect to MySQL: ' . $con->connect_error);
    }

    $con->set_charset('utf8mb4');
    return $con;
}

function stripe_log_event(mysqli $db, string $eventId, string $type, string $payload, string $status = 'received', ?string $error = null): void
{
    $stmt = $db->prepare('INSERT INTO stripe_webhook_events (event_id, type, payload, status, error_message) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE type = VALUES(type), payload = VALUES(payload), status = VALUES(status), error_message = VALUES(error_message), processed_at = IF(VALUES(status) = "processed", NOW(), processed_at)');
    if ($stmt === false) {
        throw new RuntimeException('Failed to prepare webhook log statement: ' . $db->error);
    }

    $stmt->bind_param('sssss', $eventId, $type, $payload, $status, $error);
    $stmt->execute();
    $stmt->close();
}

function stripe_mark_event_status(mysqli $db, string $eventId, string $status, ?string $error = null): void
{
    $stmt = $db->prepare('UPDATE stripe_webhook_events SET status = ?, error_message = ?, processed_at = NOW() WHERE event_id = ?');
    if ($stmt === false) {
        throw new RuntimeException('Failed to prepare webhook status statement: ' . $db->error);
    }

    $stmt->bind_param('sss', $status, $error, $eventId);
    $stmt->execute();
    $stmt->close();
}

function stripe_fetch_account(mysqli $db, int $accountId): ?array
{
    $stmt = $db->prepare('SELECT id, email, fullname, accounttype FROM accounts WHERE id = ? LIMIT 1');
    if ($stmt === false) {
        throw new RuntimeException('Failed to prepare account lookup.');
    }

    $stmt->bind_param('i', $accountId);
    $stmt->execute();
    $result = $stmt->get_result();
    $account = $result->fetch_assoc() ?: null;
    $stmt->close();

    return $account;
}

function stripe_fetch_customer_by_account(mysqli $db, int $accountId): ?array
{
    $stmt = $db->prepare('SELECT * FROM stripe_customers WHERE account_id = ? LIMIT 1');
    if ($stmt === false) {
        throw new RuntimeException('Failed to prepare customer lookup.');
    }

    $stmt->bind_param('i', $accountId);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result->fetch_assoc() ?: null;
    $stmt->close();

    return $customer;
}

function stripe_fetch_customer_by_stripe_id(mysqli $db, string $customerId): ?array
{
    $stmt = $db->prepare('SELECT * FROM stripe_customers WHERE stripe_customer_id = ? LIMIT 1');
    if ($stmt === false) {
        throw new RuntimeException('Failed to prepare customer lookup by id.');
    }

    $stmt->bind_param('s', $customerId);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result->fetch_assoc() ?: null;
    $stmt->close();

    return $customer;
}

function stripe_ensure_customer(mysqli $db, int $accountId, array $account): array
{
    $existing = stripe_fetch_customer_by_account($db, $accountId);
    if ($existing !== null) {
        return $existing;
    }

    $customer = stripe_client()->customers->create([
        'email' => $account['email'] ?? null,
        'name' => $account['fullname'] ?? null,
        'metadata' => [
            'account_id' => (string) $accountId,
        ],
    ]);

    stripe_save_customer($db, $accountId, $customer->id, $customer->email, $customer->invoice_settings->default_payment_method ?? null);

    return stripe_fetch_customer_by_account($db, $accountId) ?? [
        'account_id' => $accountId,
        'stripe_customer_id' => $customer->id,
        'email' => $customer->email,
        'default_payment_method' => $customer->invoice_settings->default_payment_method ?? null,
    ];
}

function stripe_save_customer(mysqli $db, int $accountId, string $customerId, ?string $email = null, ?string $defaultPaymentMethod = null): void
{
    $stmt = $db->prepare('INSERT INTO stripe_customers (account_id, stripe_customer_id, email, default_payment_method) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE stripe_customer_id = VALUES(stripe_customer_id), email = VALUES(email), default_payment_method = VALUES(default_payment_method), updated_at = NOW()');
    if ($stmt === false) {
        throw new RuntimeException('Failed to prepare customer upsert.');
    }

    $stmt->bind_param('isss', $accountId, $customerId, $email, $defaultPaymentMethod);
    $stmt->execute();
    $stmt->close();
}

function stripe_save_subscription(mysqli $db, array $subscription, int $accountId): void
{
    $priceId = $subscription['items']['data'][0]['price']['id'] ?? '';
    $productId = $subscription['items']['data'][0]['price']['product'] ?? null;
    $status = $subscription['status'] ?? 'incomplete';
    $plan = stripe_lookup_plan($priceId);

    $currentPeriodStart = isset($subscription['current_period_start']) ? gmdate('Y-m-d H:i:s', (int) $subscription['current_period_start']) : null;
    $currentPeriodEnd = isset($subscription['current_period_end']) ? gmdate('Y-m-d H:i:s', (int) $subscription['current_period_end']) : null;
    $cancelAt = isset($subscription['cancel_at']) && $subscription['cancel_at'] ? gmdate('Y-m-d H:i:s', (int) $subscription['cancel_at']) : null;
    $cancelAtPeriodEnd = !empty($subscription['cancel_at_period_end']) ? 1 : 0;
    $metadata = !empty($subscription['metadata']) ? json_encode($subscription['metadata'], JSON_THROW_ON_ERROR) : null;

    $stmt = $db->prepare('INSERT INTO stripe_subscriptions (stripe_subscription_id, stripe_customer_id, account_id, price_id, product_id, status, current_period_start, current_period_end, cancel_at, cancel_at_period_end, metadata) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE price_id = VALUES(price_id), product_id = VALUES(product_id), status = VALUES(status), current_period_start = VALUES(current_period_start), current_period_end = VALUES(current_period_end), cancel_at = VALUES(cancel_at), cancel_at_period_end = VALUES(cancel_at_period_end), metadata = VALUES(metadata), updated_at = NOW()');
    if ($stmt === false) {
        throw new RuntimeException('Failed to prepare subscription upsert.');
    }

    $stripeSubscriptionId = $subscription['id'] ?? '';
    $stripeCustomerId = $subscription['customer'] ?? '';
    $stmt->bind_param(
        'ssissssssss',
        $stripeSubscriptionId,
        $stripeCustomerId,
        $accountId,
        $priceId,
        $productId,
        $status,
        $currentPeriodStart,
        $currentPeriodEnd,
        $cancelAt,
        $cancelAtPeriodEnd,
        $metadata
    );
    $stmt->execute();
    $stmt->close();

    if (stripe_should_upgrade($status)) {
        stripe_update_account_type($db, $accountId, $plan['account_type']);
    } elseif (stripe_should_downgrade($status)) {
        stripe_update_account_type($db, $accountId, 'TRIAL');
    }
}

function stripe_update_account_type(mysqli $db, int $accountId, string $accountType): void
{
    $normalized = strtoupper($accountType);
    $stmt = $db->prepare('UPDATE accounts SET accounttype = ? WHERE id = ?');
    if ($stmt === false) {
        throw new RuntimeException('Failed to prepare account type update.');
    }

    $stmt->bind_param('si', $normalized, $accountId);
    $stmt->execute();
    $stmt->close();
}

function stripe_handle_checkout_completed(mysqli $db, array $session): void
{
    if (($session['mode'] ?? '') !== 'subscription') {
        return;
    }

    $accountId = isset($session['client_reference_id']) ? (int) $session['client_reference_id'] : null;
    $customerId = $session['customer'] ?? null;

    if (!$customerId) {
        throw new RuntimeException('Checkout session completed without a customer identifier.');
    }

    if ($accountId === null || $accountId <= 0) {
        $customerRecord = stripe_fetch_customer_by_stripe_id($db, $customerId);
        $accountId = $customerRecord['account_id'] ?? null;
    }

    if ($accountId === null) {
        throw new RuntimeException('Unable to match checkout session to an account.');
    }

    $account = stripe_fetch_account($db, $accountId);
    if ($account === null) {
        throw new RuntimeException('Account not found for checkout session.');
    }

    stripe_save_customer(
        $db,
        $accountId,
        $customerId,
        $session['customer_details']['email'] ?? $account['email'] ?? null,
        $session['payment_method'] ?? null
    );

    if (!empty($session['subscription'])) {
        $subscription = stripe_client()->subscriptions->retrieve($session['subscription'], ['expand' => ['items.data.price']]);
        stripe_save_subscription($db, $subscription->toArray(), $accountId);
    }
}

function stripe_handle_subscription_event(mysqli $db, array $subscription): void
{
    $customerId = $subscription['customer'] ?? '';
    if ($customerId === '') {
        throw new RuntimeException('Subscription event missing customer id.');
    }

    $customer = stripe_fetch_customer_by_stripe_id($db, $customerId);
    $accountId = $customer['account_id'] ?? null;

    if ($accountId === null) {
        $metadataAccountId = isset($subscription['metadata']['account_id']) ? (int) $subscription['metadata']['account_id'] : null;
        if (!$metadataAccountId) {
            throw new RuntimeException('Unable to resolve account id for subscription ' . ($subscription['id'] ?? 'unknown'));
        }
        $accountId = $metadataAccountId;
    }

    $account = stripe_fetch_account($db, (int) $accountId);
    if ($account === null) {
        throw new RuntimeException('Account not found for subscription event.');
    }

    stripe_save_customer($db, (int) $accountId, $customerId, $account['email'] ?? null, $subscription['default_payment_method'] ?? null);
    stripe_save_subscription($db, $subscription, (int) $accountId);
}

function stripe_handle_invoice_failed(mysqli $db, array $invoice): void
{
    $subscriptionId = $invoice['subscription'] ?? null;
    if (!$subscriptionId) {
        return;
    }

    $subscription = stripe_client()->subscriptions->retrieve($subscriptionId, ['expand' => ['items.data.price']]);
    stripe_handle_subscription_event($db, $subscription->toArray());
}

function stripe_handle_event(mysqli $db, Event $event): void
{
    $payload = $event->toArray();

    switch ($event->type) {
        case 'checkout.session.completed':
            stripe_handle_checkout_completed($db, $payload['data']['object']);
            break;
        case 'customer.subscription.created':
        case 'customer.subscription.updated':
        case 'customer.subscription.deleted':
            stripe_handle_subscription_event($db, $payload['data']['object']);
            break;
        case 'invoice.payment_failed':
            stripe_handle_invoice_failed($db, $payload['data']['object']);
            break;
        default:
            // Other events are logged for observability but do not require action.
            break;
    }
}
