<?php

namespace Modules\Globalsetting\Menus;

use Ladmin\Engine\Contracts\MenuDivider;
use Ladmin\Engine\Menus\Gate;
use Ladmin\Engine\Supports\BaseMenu;

class DeliverySettingMenu extends BaseMenu
{
    /**
     * Gate name for accessing module
     *
     * @var string
     */
    protected $gate = 'ladmin.globalsetting.delivery.index';

    /**
     * Name of menu
     *
     * @var string
     */
    protected $name = 'Delivery Setting';

    /**
     * Font icons
     *
     * @var string
     */
    protected $icon = 'fa fa-shipping'; // fontawesome

    /**
     * Menu description
     *
     * @var string
     */
    protected $description = 'User can access delivery setting';

    /**
     * Inspecting The Request Path / Route active
     * https://laravel.com/docs/master/requests#inspecting-the-request-path
     *
     * @var string
     */
    protected $isActive = 'globalsetting/delivery*';

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
        return ['ladmin.globalsetting.delivery.index'];
    }

    /**
     * Other gates
     *
     * @return Array(Ladmin\Engine\Menus\Gate)
     */
    protected function gates()
    {
        return [
            new Gate(gate: 'ladmin.globalsetting.delivery.create', title: 'Create New Delivery Setting', description: 'User can create new delivery setting'),
            new Gate(gate: 'ladmin.globalsetting.delivery.update', title: 'Update Delivery Setting', description: 'User can update delivery setting'),
            new Gate(gate: 'ladmin.globalsetting.delivery.delete', title: 'Delete Delivery Setting', description: 'User can delete delivery setting'),
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
