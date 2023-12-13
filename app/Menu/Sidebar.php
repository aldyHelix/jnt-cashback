<?php

use Modules\Cashbackpickup\Menus\CashbackMenu;
use Modules\Delivery\Menus\DeliveryMenu;
use Modules\Globalsetting\Menus\GlobalsettingsMenu;
use Modules\Ladmin\Menus\Account;
use Modules\Ladmin\Menus\Access;
use Modules\Ladmin\Menus\System;
use Modules\Ladmin\Menus\Home;
use Modules\Period\Menus\PeriodeMenu;
use Modules\Processwizard\Menus\WizardMenu;
use Modules\Ratesetting\Menus\MasterMenu;
use Modules\Uploadfile\Menus\UploadMenu;

/**
 * Declaration your top parent of sidebar menu
 */

return [
    Home::class,

    GlobalsettingsMenu::class,

    WizardMenu::class,

    UploadMenu::class,

    PeriodeMenu::class,

    MasterMenu::class,

    CashbackMenu::class,

    DeliveryMenu::class,

    Account::class,

    Access::class,

    System::class
];
