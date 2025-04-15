<?php

return [
    /*Einstellungen für die Verbindung zur Marketplace-API*/

    'base_url' => env('WECLAPP_BASE_URL', 'https://hbtestmarketplace.weclapp.com/webapp/api/v1'),
    'api_token' => env('WECLAPP_API_TOKEN', ''),
    'rate_limit_per_minute' => env('WECLAPP_RATE_LIMIT_PER_MINUTE', 30),
];
