<?php

namespace Modules\CollectionPoint\Menus;

use Ladmin\Engine\Contracts\MenuDivider;
use Ladmin\Engine\Menus\Gate;
use Ladmin\Engine\Supports\BaseMenu;

class CollectionPointMenu extends BaseMenu
{

    /**
     * Gate name for accessing module
     *
     * @var string
     */
    protected $gate = 'ladmin.collectionpoint.index';

    /**
     * Name of menu
     *
     * @var string
     */
    protected $name = 'Master CP';

    /**
     * Font icons
     *
     * @var string
     */
    protected $icon = 'fa fa-map-pin'; // fontawesome

    /**
     * Menu description
     *
     * @var string
     */
    protected $description = 'User can master data collection point';

    /**
     * Inspecting The Request Path / Route active
     * https://laravel.com/docs/master/requests#inspecting-the-request-path
     *
     * @var string
     */
    protected $isActive = 'collectionpoint*';

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
        return ['ladmin.collectionpoint.index'];
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
            new Gate(gate: 'ladmin.collectionpoint.create', title: 'Create New Collection Point', description: 'User can create new collection point data'),
            new Gate(gate: 'ladmin.collectionpoint.update', title: 'Update Collection Point', description: 'User can update collection point'),
            new Gate(gate: 'ladmin.collectionpoint.delete', title: 'Delete Collection Point', description: 'User can update collection point'),
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
