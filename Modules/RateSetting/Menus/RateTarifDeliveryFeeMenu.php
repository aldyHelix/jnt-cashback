<?php

namespace Modules\RateSetting\Menus;

use Ladmin\Engine\Contracts\MenuDivider;
use Ladmin\Engine\Menus\Gate;
use Ladmin\Engine\Supports\BaseMenu;

class RateTarifDeliveryFeeMenu extends BaseMenu
{

    /**
     * Gate name for accessing module
     *
     * @var string
     */
    protected $gate = 'ladmin.ratesetting.deliferyfee.index';

    /**
     * Name of menu
     *
     * @var string
     */
    protected $name = 'Delivery Fee';

    /**
     * Font icons
     *
     * @var string
     */
    protected $icon = 'fa fa-regular  fa-usd'; // fontawesome

    /**
     * Menu description
     *
     * @var string
     */
    protected $description = 'User can access delivery fee';

    /**
     * Inspecting The Request Path / Route active
     * https://laravel.com/docs/master/requests#inspecting-the-request-path
     *
     * @var string
     */
    protected $isActive = 'deliferyfee*';

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
        return ['ladmin.ratesetting.delivery.index'];
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
            new Gate(gate: 'ladmin.ratesetting.deliferyfee.create', title: 'Create New Delivery Fee', description: 'User can create new delivery fee'),
            new Gate(gate: 'ladmin.ratesetting.deliferyfee.update', title: 'Update Delivery fee', description: 'User can update delivery fee'),
            new Gate(gate: 'ladmin.ratesetting.deliferyfee.destory', title: 'Delete Delivery', description: 'User can delete delivery fee'),
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
