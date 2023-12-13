<?php

namespace Modules\Category\Menus;

use Ladmin\Engine\Contracts\MenuDivider;
use Ladmin\Engine\Menus\Gate;
use Ladmin\Engine\Supports\BaseMenu;

class CategoryKlienPengiriman extends BaseMenu
{
    /**
     * Gate name for accessing module
     *
     * @var string
     */
    protected $gate = 'ladmin.category.index';

    /**
     * Name of menu
     *
     * @var string
     */
    protected $name = 'Category Klien Pengiriman';

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
    protected $description = 'User can access module category klien pengiriman';

    /**
     * Inspecting The Request Path / Route active
     * https://laravel.com/docs/master/requests#inspecting-the-request-path
     *
     * @var string
     */
    protected $isActive = '';

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
        return ['ladmin.category.index'];
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
            new Gate(gate: 'ladmin.category.create', title: 'Create New Klien Pengiriman', description: 'User can create new klien pengiriman data'),
            new Gate(gate: 'ladmin.category.update', title: 'Update Klien Pengiriman', description: 'User can update klien pengiriman'),
            new Gate(gate: 'ladmin.category.delete', title: 'Delete Klien Pengiriman', description: 'User can update klien pengiriman'),
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
