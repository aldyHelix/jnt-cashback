<?php

use Modules\CashbackPickup\Menus\CashbackMenu;
use Modules\Delivery\Menus\DeliveryMenu;
use Modules\Ladmin\Menus\Account;
use Modules\Ladmin\Menus\Access;
use Modules\Ladmin\Menus\System;
use Modules\Period\Menus\PeriodeMenu;
use Modules\RateSetting\Menus\MasterMenu;
use Modules\UploadFile\Menus\UploadMenu;

/**
 * Declaration your top parent of sidebar menu
 */

return [
    UploadMenu::class,

    PeriodeMenu::class,

    MasterMenu::class,

    CashbackMenu::class,

    DeliveryMenu::class,

    Account::class,

    Access::class,

    System::class
];
