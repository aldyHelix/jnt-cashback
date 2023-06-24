<?php

use Modules\Ladmin\Menus\Account;
use Modules\Ladmin\Menus\Access;
use Modules\Ladmin\Menus\System;
use Modules\UploadFile\Menus\UploadMenu;

/**
 * Declaration your top parent of sidebar menu
 */

return [
    UploadMenu::class,

    Account::class,

    Access::class,

    System::class
];
