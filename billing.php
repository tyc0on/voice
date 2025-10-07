<?php
declare(strict_types=1);

ini_set('session.cookie_lifetime', (string) (60 * 60 * 24 * 365));

session_start();

if (!file_exists(__DIR__ . '/include.php')) {
    http_response_code(500);
    echo 'Application configuration is missing.';
    exit;
}

include __DIR__ . '/include.php';
include __DIR__ . '/variables.php';

$con = new mysqli($sqlh, $sqlu, $sqlp, $sqld);
if ($con->connect_errno) {
    http_response_code(500);
    echo 'Database connection failed.';
    exit;
}

$con->set_charset('utf8mb4');

$_SESSION['return_url'] = $_SERVER['REQUEST_URI'];
include __DIR__ . '/auth.php';

if (!isset($loggedin) || $loggedin !== 'true') {
    header('Location: /sign-in.php');
    exit;
}

require_once __DIR__ . '/core/stripe/service.php';

function billing_fetch_latest_subscription(mysqli $db, int $accountId): ?array
{
    $stmt = $db->prepare('SELECT * FROM stripe_subscriptions WHERE account_id = ? ORDER BY updated_at DESC LIMIT 1');
    if ($stmt === false) {
        return null;
    }

    $stmt->bind_param('i', $accountId);
    $stmt->execute();
    $result = $stmt->get_result();
    $subscription = $result->fetch_assoc() ?: null;
    $stmt->close();

    return $subscription;
}

function billing_parse_utc_datetime(?string $value): ?DateTimeImmutable
{
    if ($value === null || $value === '') {
        return null;
    }

    $date = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $value, new DateTimeZone('UTC'));
    if ($date instanceof DateTimeImmutable) {
        return $date;
    }

    return null;
}

$accountId = (int) ($_SESSION['id'] ?? 0);
if ($accountId <= 0) {
    header('Location: /sign-in.php');
    exit;
}

try {
    $account = stripe_fetch_account($con, $accountId);
    if ($account === null) {
        throw new RuntimeException('Unable to locate the current account.');
    }

    $stripeReady = true;
    try {
        stripe_secret_key();
    } catch (RuntimeException $e) {
        $stripeReady = false;
    }

    $subscription = billing_fetch_latest_subscription($con, $accountId);
    $subscriptionStatus = $subscription['status'] ?? null;
    $subscriptionPriceId = $subscription['price_id'] ?? null;
    $cancelAtPeriodEnd = !empty($subscription['cancel_at_period_end'] ?? 0);
    $renewalDate = billing_parse_utc_datetime($subscription['current_period_end'] ?? null);

    if ($stripeReady) {
        $customerRecord = stripe_fetch_customer_by_account($con, $accountId);

        if ($customerRecord === null && !empty($subscription['stripe_customer_id'] ?? null)) {
            try {
                $remoteCustomer = stripe_client()->customers->retrieve($subscription['stripe_customer_id']);
                stripe_save_customer(
                    $con,
                    $accountId,
                    $subscription['stripe_customer_id'],
                    $remoteCustomer->email ?? ($account['email'] ?? null),
                    $remoteCustomer->invoice_settings->default_payment_method ?? null
                );
                $customerRecord = stripe_fetch_customer_by_account($con, $accountId);
            } catch (Throwable $e) {
                error_log('Unable to backfill Stripe customer ' . $subscription['stripe_customer_id'] . ' for account ' . $accountId . ': ' . $e->getMessage());
            }
        }

        if ($customerRecord === null) {
            try {
                $customerRecord = stripe_ensure_customer($con, $accountId, $account);
            } catch (Throwable $e) {
                error_log('Unable to ensure Stripe customer for account ' . $accountId . ': ' . $e->getMessage());
                $customerRecord = null;
            }
        }
    } else {
        $customerRecord = stripe_fetch_customer_by_account($con, $accountId);
    }

    if (
        $stripeReady
        && ($subscription === null || $renewalDate === null)
    ) {
        try {
            $latestSubscription = null;

            if (!empty($subscription['stripe_subscription_id'] ?? null)) {
                $latestSubscription = stripe_client()->subscriptions->retrieve(
                    $subscription['stripe_subscription_id'],
                    ['expand' => ['items.data.price']]
                );
            } elseif (!empty($customerRecord['stripe_customer_id'] ?? null)) {
                $remoteSubscriptions = stripe_client()->subscriptions->all([
                    'customer' => $customerRecord['stripe_customer_id'],
                    'limit' => 1,
                    'expand' => ['data.items.data.price'],
                ]);

                if (!empty($remoteSubscriptions->data)) {
                    $latestSubscription = $remoteSubscriptions->data[0];
                }
            }

            if ($latestSubscription !== null) {
                $subscriptionArray = $latestSubscription->toArray();

                stripe_save_subscription($con, $subscriptionArray, $accountId);

                $subscription = billing_fetch_latest_subscription($con, $accountId);
                $subscriptionStatus = $subscription['status'] ?? $subscriptionStatus;
                $subscriptionPriceId = $subscription['price_id'] ?? $subscriptionPriceId;
                $cancelAtPeriodEnd = !empty($subscription['cancel_at_period_end'] ?? 0);
                $renewalDate = billing_parse_utc_datetime($subscription['current_period_end'] ?? null);
            }
        } catch (Throwable $e) {
            error_log('Unable to refresh subscription for account ' . $accountId . ': ' . $e->getMessage());
        }
    }

    $_SESSION['accounttype'] = strtoupper($account['accounttype'] ?? 'TRIAL');

    $currentPlan = 'Trial';
    $currentStatus = 'Not Subscribed';
    $accountType = $_SESSION['accounttype'];

    if ($subscription) {
        $plan = stripe_lookup_plan($subscriptionPriceId ?? '');
        $currentPlan = $plan['plan_name'];
        $currentStatus = $subscriptionStatus
            ? ucfirst(str_replace('_', ' ', strtolower($subscriptionStatus)))
            : 'Not Subscribed';
    }

    $publishableKey = stripe_publishable_key();
    $pricingTableId = stripe_pricing_table_id();
    $customerId = $customerRecord['stripe_customer_id'] ?? null;
    $clientReferenceId = (string) $accountId;
    $billingError = $_SESSION['billing_error'] ?? null;
    unset($_SESSION['billing_error']);
    $stripeSecretMissing = !$stripeReady;
} catch (Throwable $e) {
    http_response_code(500);
    echo 'Unable to load billing details. Please contact support.';
    error_log('Billing page error: ' . $e->getMessage());
    exit;
}

$title = $sitename . ' - Billing';
$bodysettings = 'data-kt-app-layout="dark-sidebar" data-kt-app-header-fixed="true" data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true" data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true" data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true"';
$modals = '';
$scripts = '';

include __DIR__ . '/config.php';
$modals = '';
$scripts = '';
include __DIR__ . '/core/header.php';
?>
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl py-10">
                <div class="row g-5 g-xl-10">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header border-0 pt-6">
                                <div class="d-flex justify-content-between align-items-center w-100">
                                    <h2 class="card-title mb-0">Subscription Overview</h2>
                                    <form action="/stripe/portal.php" method="post" class="mb-0">
                                        <button type="submit" class="btn btn-sm btn-light-primary" <?php echo $customerId ? '' : 'disabled'; ?>>
                                            <i class="ki-duotone ki-credit-cart fs-2 me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Manage Billing Details
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="mb-5">
                                    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between">
                                        <div>
                                            <span class="fs-6 text-muted">Current Tier</span>
                                            <h3 class="fs-2 fw-bold mb-0"><?php echo htmlspecialchars($currentPlan); ?></h3>
                                        </div>
                                        <div class="mt-3 mt-sm-0 text-sm-end">
                                            <span class="fs-6 text-muted">Status</span>
                                            <div class="fw-semibold fs-5">
                                                <?php $statusBadge = $subscription ? (stripe_should_upgrade($subscription['status'] ?? '') ? 'success' : 'warning') : 'secondary'; ?>
                                                <span class="badge badge-light-<?php echo $statusBadge; ?>">
                                                    <?php echo htmlspecialchars($currentStatus); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="separator separator-dashed my-5"></div>
                                <div class="row gy-5">
                                    <div class="col-md-6">
                                        <span class="fs-6 text-muted d-block">Account Type</span>
                                        <span class="fw-semibold fs-5"><?php echo htmlspecialchars(ucfirst(strtolower($accountType ?? 'TRIAL'))); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="fs-6 text-muted d-block">Next Billing Date</span>
                                        <span class="fw-semibold fs-5">
                                            <?php echo $renewalDate ? $renewalDate->format('M j, Y H:i T') : '—'; ?>
                                        </span>
                                    </div>
                                </div>
                                <?php if (!empty($subscription['cancel_at_period_end'])): ?>
                                    <div class="alert alert-warning d-flex align-items-center mt-7" role="alert">
                                        <i class="ki-duotone ki-information-2 text-warning fs-2x me-3"></i>
                                        <div>
                                            <h5 class="alert-heading mb-1">Subscription set to cancel</h5>
                                            <span>Your subscription will remain active until <?php echo $renewalDate ? $renewalDate->format('M j, Y H:i T') : 'the end of the current period'; ?>.</span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if ($stripeSecretMissing): ?>
                                    <div class="alert alert-danger d-flex align-items-center mt-7" role="alert">
                                        <i class="ki-duotone ki-shield-cross text-danger fs-2x me-3"></i>
                                        <div>
                                            <h5 class="alert-heading mb-1">Stripe secret key required</h5>
                                            <span>Add STRIPE_SECRET_KEY to the environment so Easy AI Voice can create customer records and sync subscription status.</span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if ($publishableKey === ''): ?>
                                    <div class="alert alert-danger d-flex align-items-center mt-7" role="alert">
                                        <i class="ki-duotone ki-shield-cross text-danger fs-2x me-3"></i>
                                        <div>
                                            <h5 class="alert-heading mb-1">Stripe is not configured</h5>
                                            <span>Add STRIPE_PUBLISHABLE_KEY to the server environment to enable the hosted pricing table.</span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($billingError)): ?>
                                    <div class="alert alert-danger d-flex align-items-center mt-7" role="alert">
                                        <i class="ki-duotone ki-information-5 text-danger fs-2x me-3"></i>
                                        <div>
                                            <h5 class="alert-heading mb-1">Something went wrong</h5>
                                            <span><?php echo htmlspecialchars($billingError); ?></span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card mt-10">
                            <div class="card-header border-0 pt-6">
                                <h2 class="card-title">Manage Subscription</h2>
                            </div>
                            <div class="card-body pt-0">
                                <p class="text-muted">Update your plan using the secure Stripe checkout. Changes take effect immediately and your account access will update automatically.</p>
                                <?php if ($publishableKey !== ''): ?>
                                    <script async src="https://js.stripe.com/v3/pricing-table.js"></script>
                                    <div class="pricing-table-container" style="min-height: 600px; width: 100%;">
                                        <stripe-pricing-table
                                            pricing-table-id="<?php echo htmlspecialchars($pricingTableId); ?>"
                                            publishable-key="<?php echo htmlspecialchars($publishableKey); ?>"
                                            customer-id="<?php echo htmlspecialchars($customerId ?? ''); ?>"
                                            client-reference-id="<?php echo htmlspecialchars($clientReferenceId); ?>"
                                            customer-email="<?php echo htmlspecialchars($account['email'] ?? ''); ?>"
                                        >
                                        </stripe-pricing-table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-5 mt-5">
                    <div class="col-12 col-lg-6 offset-lg-3">
                        <div class="card border-dashed h-100">
                            <div class="card-header border-0 pt-6">
                                <h3 class="card-title">Need a hand?</h3>
                            </div>
                            <div class="card-body pt-0">
                                <p class="text-muted">We are here to help with billing changes, refunds, or general account questions.</p>
                                <ul class="list-unstyled">
                                    <li class="mb-4">
                                        <i class="ki-duotone ki-message-text-2 fs-2 me-3 text-primary"></i>
                                        <span>Email <a href="mailto:support@<?php echo htmlspecialchars($siteurl); ?>">support@<?php echo htmlspecialchars($siteurl); ?></a></span>
                                    </li>
                                    <li>
                                        <i class="ki-duotone ki-discord fs-2 me-3 text-primary"></i>
                                        <span>Join our <a href="https://discord.gg/czFGXsFJjh" target="_blank" rel="noreferrer">Discord community</a></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/core/footer.php'; ?>
