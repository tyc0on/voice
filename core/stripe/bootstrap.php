<?php
declare(strict_types=1);

use Stripe\StripeClient;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';
require_once dirname(__DIR__, 2) . '/include.php';

if (!function_exists('stripe_secret_key')) {
    /**
     * Fetch the Stripe secret key from include.php variables.
     */
    function stripe_secret_key(): string
    {
        global $stripe_sk;
        
        // Fallback to environment variables if global not available
        $key = $stripe_sk ?? getenv('STRIPE_SECRET_KEY') ?: '';
        if ($key === '' && defined('STRIPE_SECRET_KEY')) {
            $key = (string) constant('STRIPE_SECRET_KEY');
        }

        if ($key === '') {
            throw new RuntimeException('Stripe secret key is not configured. Set $stripe_sk in include.php or STRIPE_SECRET_KEY in the environment.');
        }

        return $key;
    }
}

if (!function_exists('stripe_publishable_key')) {
    /**
     * Fetch the Stripe publishable key from include.php variables.
     */
    function stripe_publishable_key(): string
    {
        global $stripe_pk;
        
        // Fallback to environment variables if global not available
        $key = $stripe_pk ?? getenv('STRIPE_PUBLISHABLE_KEY') ?: '';
        if ($key === '' && defined('STRIPE_PUBLISHABLE_KEY')) {
            $key = (string) constant('STRIPE_PUBLISHABLE_KEY');
        }

        return $key;
    }
}

if (!function_exists('stripe_webhook_secret')) {
    /**
     * Fetch the Stripe webhook signing secret.
     */
    function stripe_webhook_secret(): string
    {
        $secret = getenv('STRIPE_WEBHOOK_SECRET') ?: '';
        if ($secret === '' && defined('STRIPE_WEBHOOK_SECRET')) {
            $secret = (string) constant('STRIPE_WEBHOOK_SECRET');
        }

        return $secret;
    }
}

if (!function_exists('stripe_pricing_table_id')) {
    /**
     * Fetch the pricing table identifier used for the hosted pricing table embed.
     */
    function stripe_pricing_table_id(): string
    {
        $tableId = getenv('STRIPE_PRICING_TABLE_ID') ?: '';
        if ($tableId === '' && defined('STRIPE_PRICING_TABLE_ID')) {
            $tableId = (string) constant('STRIPE_PRICING_TABLE_ID');
        }

        if ($tableId === '') {
            // Default to the sandbox table provided during development so the UI can render.
            $tableId = 'prctbl_1SFQSf3ojSJuqvpDBMsDTQAh';
        }

        return $tableId;
    }
}

if (!function_exists('stripe_client')) {
    /**
     * Lazily build a singleton Stripe client instance.
     */
    function stripe_client(): StripeClient
    {
        static $client = null;

        if ($client === null) {
            $client = new StripeClient([
                'api_key' => stripe_secret_key(),
                'stripe_version' => getenv('STRIPE_API_VERSION') ?: null,
            ]);
        }

        return $client;
    }
}
