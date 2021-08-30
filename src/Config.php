<?php

namespace App;

class Config
{
    const DEVICE_LIMIT = 10000;
    const USER_LIMIT = 10000;

    const IMAGE_COLUMN_WIDTH = 85;
    const NAME_COLUMN_WIDTH = 150;
    const PRICE_COLUMN_WIDTH = 75;
    const MODIFICATION_COLUMN_WIDTH = 95;
    const EDIT_COLUMN_WIDTH = 62;
    const FIELD_COLUMN_WIDTH = 110;
    const CHECKBOX_COLUMN_WIDTH = 110;

    const INVENTORY_DEALS_LIMIT = 10;
    const MINING_DEALS_LIMIT = 10;
    const TRADE_DEALS_LIMIT = 10;

    const AUCTION_PRICES_API_URL = 'https://atherdev.nl/sb/api/v2';

    // Since there is no permission to specify timezone for console PHP on the server,
    // the timezone is hardcoded here to avoid time differences during the import.
    //
    // Use for console commands only!
    const CONSOLE_TIMEZONE = 'Europe/Kiev';
}
