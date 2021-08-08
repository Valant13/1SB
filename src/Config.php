<?php

namespace App;

class Config
{
    const ALLOWED_MAXIMIZATION_PARAMS = [
        'credit',
        'box',
        'lightning',
        'shield',
        'gear'
    ];

    const HOME_CONTROLLER = 'App\Controller\IndexController::index';
}
