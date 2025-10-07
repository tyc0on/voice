<?php
declare(strict_types=1);

require_once __DIR__ . '/../variables.php';
require_once __DIR__ . '/../include.php';
require_once __DIR__ . '/stripe/service.php';

$sessionId = isset($_GET['session_id']) ? trim((string) $_GET['session_id']) : '';
$stripeReady = true;

try {
    stripe_secret_key();
} catch (Throwable $e) {
    $stripeReady = false;
}

$planName = null;
$nextBillingDateFormatted = null;
$errorMessage = null;
$customerEmail = null;
$accountId = null;

if ($sessionId === '') {
    $errorMessage = 'We could not find your checkout session. If you completed a purchase, please sign in to your account to verify your subscription.';
} elseif (!$stripeReady) {
    $errorMessage = 'Stripe is not fully configured on this server. Please contact support so we can finish setting up your subscription.';
} else {
    try {
        $checkoutSession = stripe_client()->checkout->sessions->retrieve($sessionId);
        $customerEmail = $checkoutSession->customer_details->email ?? $checkoutSession->customer_details['email'] ?? null;

        $subscriptionObject = null;
        if (!empty($checkoutSession->subscription)) {
            if (is_string($checkoutSession->subscription)) {
                $subscriptionObject = stripe_client()->subscriptions->retrieve(
                    $checkoutSession->subscription,
                    ['expand' => ['items.data.price']]
                );
            } else {
                $subscriptionObject = $checkoutSession->subscription;
                // Ensure price metadata is available
                if (empty($subscriptionObject->items->data[0]->price->id ?? null)) {
                    $subscriptionObject = stripe_client()->subscriptions->retrieve(
                        $subscriptionObject->id,
                        ['expand' => ['items.data.price']]
                    );
                }
            }
        }

        $subscriptionData = $subscriptionObject ? $subscriptionObject->toArray() : null;

        if ($subscriptionData) {
            $priceId = $subscriptionData['items']['data'][0]['price']['id'] ?? '';
            $plan = stripe_lookup_plan($priceId);
            $planName = $plan['plan_name'] ?? 'your new plan';

            if (!empty($subscriptionData['current_period_end'])) {
                $nextBillingDate = (new DateTimeImmutable('@' . (int) $subscriptionData['current_period_end']))
                    ->setTimezone(new DateTimeZone(date_default_timezone_get()));
                $nextBillingDateFormatted = $nextBillingDate->format('F j, Y g:i A T');
            }

            $accountId = isset($checkoutSession->client_reference_id) ? (int) $checkoutSession->client_reference_id : null;
            if (($accountId === null || $accountId <= 0) && !empty($subscriptionData['metadata']['account_id'] ?? null)) {
                $accountId = (int) $subscriptionData['metadata']['account_id'];
            }

            try {
                $db = ensure_database_connection();
            } catch (Throwable $dbException) {
                $db = null;
                error_log('Checkout thank-you page could not connect to database: ' . $dbException->getMessage());
            }

            if ($db !== null && $accountId !== null && $accountId > 0) {
                try {
                    $account = stripe_fetch_account($db, $accountId);
                    if ($account !== null) {
                        $customerId = $subscriptionData['customer'] ?? ($checkoutSession->customer ?? null);
                        if (!empty($customerId)) {
                            stripe_save_customer(
                                $db,
                                $accountId,
                                $customerId,
                                $customerEmail ?? ($account['email'] ?? null),
                                $subscriptionData['default_payment_method'] ?? null
                            );
                        }

                        stripe_save_subscription($db, $subscriptionData, $accountId);
                    }
                } catch (Throwable $e) {
                    error_log('Checkout thank-you page failed to persist subscription: ' . $e->getMessage());
                }
            }
        }
    } catch (Throwable $e) {
        $errorMessage = 'We were unable to confirm your subscription details. Please contact support so we can make sure everything is set up correctly.';
        error_log('Checkout thank-you page error: ' . $e->getMessage());
    }
}

$planName ??= 'your new plan';
$title = $sitename . ' - Thank You';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <base href="/" />
    <title><?php echo htmlspecialchars($title); ?></title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Thank you for subscribing to <?php echo htmlspecialchars($sitename); ?>." />
    <meta property="og:title" content="<?php echo htmlspecialchars($sitename); ?> - Thank You" />
    <meta property="og:url" content="https://<?php echo htmlspecialchars($siteurl); ?>/thank-you.php" />
    <meta property="og:site_name" content="<?php echo htmlspecialchars($sitename); ?>" />
    <link rel="canonical" href="https://<?php echo htmlspecialchars($siteurl); ?>/thank-you.php" />
    <link rel="apple-touch-icon" sizes="180x180" href="assets/media/logos/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/media/logos/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/media/logos/favicon-16x16.png">
    <link rel="manifest" href="assets/media/logos/site.webmanifest">
    <link rel="mask-icon" href="assets/media/logos/safari-pinned-tab.svg" color="#5bbad5">
    <link rel="shortcut icon" href="assets/media/logos/favicon.ico">
    <meta name="theme-color" content="#111827">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <?php include __DIR__ . '/../_head.php'; ?>
</head>

<body id="kt_body" class="app-blank">
    <div class="d-flex flex-column flex-root">
        <div class="d-flex flex-column flex-center p-10 min-vh-100">
            <a href="/" class="mb-8">
                <img alt="<?php echo htmlspecialchars($sitename); ?> logo" src="assets/media/logos/logo-dark.png" class="h-45px" />
            </a>

            <div class="card card-flush w-lg-650px shadow-sm">
                <div class="card-body p-10 p-lg-20 text-center">
                    <div class="mb-6">
                        <span class="symbol symbol-70px symbol-circle bg-success">
                            <span class="symbol-label">
                                <i class="ki-duotone ki-badge text-white fs-1"></i>
                            </span>
                        </span>
                    </div>
                    <h1 class="fw-bold text-gray-900 mb-5">Thank you for subscribing!</h1>
                    <p class="fs-5 text-muted mb-5">
                        Your payment was successful and your access to <?php echo htmlspecialchars($sitename); ?> has been upgraded to
                        <span class="fw-semibold text-gray-900"><?php echo htmlspecialchars($planName); ?></span>.
                    </p>

                    <?php if ($nextBillingDateFormatted !== null): ?>
                        <div class="alert alert-success d-flex align-items-center justify-content-center mb-8" role="alert">
                            <i class="ki-duotone ki-calendar text-success fs-2x me-3"></i>
                            <div class="text-start">
                                <h4 class="mb-1">Next billing date</h4>
                                <span><?php echo htmlspecialchars($nextBillingDateFormatted); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($customerEmail !== null): ?>
                        <p class="fs-6 text-muted mb-7">A receipt has been sent to <span class="fw-semibold text-gray-900"><?php echo htmlspecialchars($customerEmail); ?></span>.</p>
                    <?php endif; ?>

                    <?php if ($errorMessage !== null): ?>
                        <div class="alert alert-danger d-flex align-items-center justify-content-center mb-8" role="alert">
                            <i class="ki-duotone ki-information-5 text-danger fs-2x me-3"></i>
                            <div class="text-start">
                                <h4 class="mb-1">We hit a snag</h4>
                                <span><?php echo htmlspecialchars($errorMessage); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex flex-column flex-md-row gap-3 justify-content-center">
                        <a href="<?php echo htmlspecialchars($siteapp); ?>" class="btn btn-primary px-8 py-3">
                            Go to the app
                        </a>
                        <a href="/billing.php" class="btn btn-light-primary px-8 py-3">
                            Manage subscription
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-10 text-center text-gray-500">
                <p class="mb-2">Need a hand? Email <a href="mailto:support@<?php echo htmlspecialchars($siteurl); ?>" class="text-primary">support@<?php echo htmlspecialchars($siteurl); ?></a></p>
                <p class="mb-0">Prefer chat? Join our <a href="https://discord.gg/czFGXsFJjh" class="text-primary" target="_blank" rel="noreferrer">Discord community</a>.</p>
            </div>
        </div>
    </div>

    <script>var defaultThemeMode = "dark"; document.documentElement.setAttribute("data-bs-theme", defaultThemeMode);</script>
    <script src="assets/plugins/global/plugins.bundle.js"></script>
    <script src="assets/js/scripts.bundle.js"></script>
</body>

</html>
