<?php

namespace Modules\CashbackPickup\Menus;

use Ladmin\Engine\Contracts\MenuDivider;
use Ladmin\Engine\Menus\Gate;
use Ladmin\Engine\Supports\BaseMenu;

class Grading2Menu extends BaseMenu
{

    /**
     * Gate name for accessing module
     *
     * @var string
     */
    protected $gate = 'ladmin.cashbackpickup.grading.1.index';

    /**
     * Name of menu
     *
     * @var string
     */
    protected $name = 'Grading 2';

    /**
     * Font icons
     *
     * @var string
     */
    protected $icon = 'fa fa-bar-chart'; // fontawesome

    /**
     * Menu description
     *
     * @var string
     */
    protected $description = 'User can access cashback pickup grading 2';

    /**
     * Inspecting The Request Path / Route active
     * https://laravel.com/docs/master/requests#inspecting-the-request-path
     *
     * @var string
     */
    protected $isActive = 'grading2*';

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
        return ['ladmin.cashbackpickup.index', ['grade' => 2]];
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
            new Gate(gate: 'ladmin.cashbackpickup.grading.2.denda', title: 'Create New Denda', description: 'User can create new data denda period'),
            new Gate(gate: 'ladmin.cashbackpickup.grading.2.view', title: 'View Summary', description: 'User can view summary'),
            new Gate(gate: 'ladmin.cashbackpickup.grading.2.process', title: 'Process Delivery Report', description: 'User can process delivery report'),
            new Gate(gate: 'ladmin.cashbackpickup.grading.2.lock', title: 'Process lock Delivery', description: 'User can lock delivery'),
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
