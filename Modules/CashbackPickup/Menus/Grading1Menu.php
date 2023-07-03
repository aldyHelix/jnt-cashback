<?php

namespace Modules\CashbackPickup\Menus;

use Ladmin\Engine\Contracts\MenuDivider;
use Ladmin\Engine\Menus\Gate;
use Ladmin\Engine\Supports\BaseMenu;

class Grading1Menu extends BaseMenu
{

    /**
     * Gate name for accessing module
     *
     * @var string
     */
    protected $gate = 'cashback.pickup.grading1.index';

    /**
     * Name of menu
     *
     * @var string
     */
    protected $name = 'Grading 1';

    /**
     * Font icons
     *
     * @var string
     */
    protected $icon = 'fa fa-regular fa-square-check'; // fontawesome

    /**
     * Menu description
     *
     * @var string
     */
    protected $description = 'User can access Cashback Pickup Grading 1';

    /**
     * Inspecting The Request Path / Route active
     * https://laravel.com/docs/master/requests#inspecting-the-request-path
     *
     * @var string
     */
    protected $isActive = 'grading1*';

    /**
     * Menu ID
     *
     * @var string
     */
    protected $id = '';

    /**
     * Route name
     *
     * @return Array|string|null
     * @example ['route.name', ['uuid', 'foo' => 'bar']]
     */
    protected function route()
    {
        return ['ladmin.cashbackpickup.index', ['grade' => 1]];
    }

    /**
     * Other gates
     *
     * @return Array(Ladmin\Engine\Menus\Gate)
     */
    protected function gates()
    {
        return [
            // new Gate(gate: 'gate.menu.uniq', title: 'Gate Title', description: 'Description of gate'),
        ];
    }

    /**
     * Other menus
     *
     * @return void
     */
    protected function submenus()
    {
        return [
            // OtherMenu::class
        ];
    }
}
