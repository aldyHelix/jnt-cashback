<?php

namespace Modules\DropPointOutgoing\Menus;

use Ladmin\Engine\Contracts\MenuDivider;
use Ladmin\Engine\Menus\Gate;
use Ladmin\Engine\Supports\BaseMenu;

class DropPointMenu extends BaseMenu
{

    /**
     * Gate name for accessing module
     *
     * @var string
     */
    protected $gate = 'ladmin.droppointoutgoing.index';

    /**
     * Name of menu
     *
     * @var string
     */
    protected $name = 'Drop Point Outgoing';

    /**
     * Font icons
     *
     * @var string
     */
    protected $icon = 'fa fa-regular fa-pin-o'; // fontawesome

    /**
     * Menu description
     *
     * @var string
     */
    protected $description = 'User can access drop point outgoing';

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
        return ['ladmin.droppointoutgoing.index'];
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
            new Gate(gate: 'ladmin.droppointoutgoing.create', title: 'Create New drop point outgoing', description: 'User can create new drop point outgoing data'),
            new Gate(gate: 'ladmin.droppointoutgoing.update', title: 'Update drop point outgoing', description: 'User can update drop point outgoing'),
            new Gate(gate: 'ladmin.droppointoutgoing.delete', title: 'Delete drop point outgoing', description: 'User can update drop point outgoing'),
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
