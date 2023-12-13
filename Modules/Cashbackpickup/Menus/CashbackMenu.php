<?php

namespace Modules\Cashbackpickup\Menus;

use Ladmin\Engine\Contracts\MenuDivider;
use Ladmin\Engine\Menus\Gate;
use Ladmin\Engine\Supports\BaseMenu;

class CashbackMenu extends BaseMenu
{
    /**
     * Gate name for accessing module
     *
     * @var string
     */
    protected $gate = 'ladmin.cashbackpickup.index';

    /**
     * Name of menu
     *
     * @var string
     */
    protected $name = 'Cashback Pickup';

    /**
     * Font icons
     *
     * @var string
     */
    protected $icon = 'fa fa-history'; // fontawesome

    /**
     * Menu description
     *
     * @var string
     */
    protected $description = 'User can access Cashback Pickup';

    /**
     * Inspecting The Request Path / Route active
     * https://laravel.com/docs/master/requests#inspecting-the-request-path
     *
     * @var string
     */
    protected $isActive = 'grading*';

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
        return null;
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
            Grading1Menu::class,
            Grading2Menu::class,
            Grading3Menu::class,
            DPFMenu::class,
        ];
    }
}
