<?php

namespace Modules\Cashbackpickup\Menus;

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
    protected $gate = 'ladmin.cashbackpickup.grading.1.index';

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
    protected $icon = 'fa fa-bar-chart'; // fontawesome

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
    protected $isActive = 'cashbackpickup/1*';

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
            new Gate(gate: 'ladmin.cashbackpickup.grading.1.denda', title: 'Create New Denda', description: 'User can create new data denda period'),
            new Gate(gate: 'ladmin.cashbackpickup.grading.1.view', title: 'View Summary', description: 'User can view summary'),
            new Gate(gate: 'ladmin.cashbackpickup.grading.1.process', title: 'Process Grading Report', description: 'User can process grading report'),
            new Gate(gate: 'ladmin.cashbackpickup.grading.1.lock', title: 'Process lock grading', description: 'User can lock grading'),
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
