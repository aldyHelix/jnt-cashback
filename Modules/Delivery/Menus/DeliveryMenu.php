<?php

namespace Modules\Delivery\Menus;

use Ladmin\Engine\Contracts\MenuDivider;
use Ladmin\Engine\Menus\Gate;
use Ladmin\Engine\Supports\BaseMenu;

class DeliveryMenu extends BaseMenu
{
    /**
     * Gate name for accessing module
     *
     * @var string
     */
    protected $gate = 'ladmin.delivery.index';

    /**
     * Name of menu
     *
     * @var string
     */
    protected $name = 'Delivery';

    /**
     * Font icons
     *
     * @var string
     */
    protected $icon = 'fa fa-ship'; // fontawesome

    /**
     * Menu description
     *
     * @var string
     */
    protected $description = 'User can access Delivery';

    /**
     * Inspecting The Request Path / Route active
     * https://laravel.com/docs/master/requests#inspecting-the-request-path
     *
     * @var string
     */
    protected $isActive = 'delivery*';

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
        return ['ladmin.delivery.index'];
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
            new Gate(gate: 'ladmin.delivery.denda', title: 'Create New Denda', description: 'User can create new data denda period'),
            new Gate(gate: 'ladmin.delivery.view', title: 'View Summary', description: 'User can view summary'),
            new Gate(gate: 'ladmin.delivery.process', title: 'Process Delivery Report', description: 'User can process delivery report'),
            new Gate(gate: 'ladmin.delivery.lock', title: 'Process lock Delivery', description: 'User can lock delivery'),
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
