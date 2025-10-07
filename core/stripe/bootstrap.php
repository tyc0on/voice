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
     * Fetch the Stripe webhook signing secret from include.php variables.
     */
    function stripe_webhook_secret(): string
    {
        global $stripe_webhook_secret_snapshot;
        
        // Use the main webhook secret (snapshot) as default
        $secret = $stripe_webhook_secret_snapshot ?? getenv('STRIPE_WEBHOOK_SECRET') ?: '';
        if ($secret === '' && defined('STRIPE_WEBHOOK_SECRET')) {
            $secret = (string) constant('STRIPE_WEBHOOK_SECRET');
        }

        return $secret;
    }
}

if (!function_exists('stripe_webhook_secret_thin')) {
    /**
     * Fetch the thin webhook secret from include.php variables.
     */
    function stripe_webhook_secret_thin(): string
    {
        global $stripe_webhook_secret_thin;
        
        return $stripe_webhook_secret_thin ?? '';
    }
}

if (!function_exists('stripe_pricing_table_id_light')) {
    /**
     * Fetch the light mode pricing table ID.
     */
    function stripe_pricing_table_id_light(): string
    {
        global $stripe_pricing_table_id_light;
        
        return $stripe_pricing_table_id_light ?? stripe_pricing_table_id();
    }
}

if (!function_exists('stripe_pricing_table_id_dark')) {
    /**
     * Fetch the dark mode pricing table ID.
     */
    function stripe_pricing_table_id_dark(): string
    {
        global $stripe_pricing_table_id_dark;
        
        return $stripe_pricing_table_id_dark ?? stripe_pricing_table_id();
    }
}

if (!function_exists('stripe_pricing_table_id')) {
    /**
     * Fetch the pricing table identifier from include.php variables.
     */
    function stripe_pricing_table_id(): string
    {
        global $stripe_pricing_table_id;
        
        $tableId = $stripe_pricing_table_id ?? getenv('STRIPE_PRICING_TABLE_ID') ?: '';
        if ($tableId === '' && defined('STRIPE_PRICING_TABLE_ID')) {
            $tableId = (string) constant('STRIPE_PRICING_TABLE_ID');
        }

        if ($tableId === '') {
            // Default fallback
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
            global $stripe_api_version;
            
            $client = new StripeClient([
                'api_key' => stripe_secret_key(),
                'stripe_version' => $stripe_api_version ?? getenv('STRIPE_API_VERSION') ?: null,
            ]);
        }

        return $client;
    }
}
