<?php

use Modules\CashbackPickup\Menus\CashbackMenu;
use Modules\Ladmin\Menus\Account;
use Modules\Ladmin\Menus\Access;
use Modules\Ladmin\Menus\System;
use Modules\RateSetting\Menus\MasterMenu;
use Modules\UploadFile\Menus\UploadMenu;

/**
 * Declaration your top parent of sidebar menu
 */

return [
    UploadMenu::class,

    MasterMenu::class,

    CashbackMenu::class,

    Account::class,

    Access::class,

    System::class
];
