<?php

namespace Modules\Ratesetting\Menus;

use Ladmin\Engine\Contracts\MenuDivider;
use Ladmin\Engine\Menus\Gate;
use Ladmin\Engine\Supports\BaseMenu;
use Modules\Category\Menus\CategoryKlienPengiriman;
use Modules\Collectionpoint\Menus\CollectionpointMenu;
use Modules\Droppointoutgoing\Menus\DropPointMenu;

class MasterMenu extends BaseMenu
{
    /**
     * Gate name for accessing module
     *
     * @var string
     */
    protected $gate = 'master.index';

    /**
     * Name of menu
     *
     * @var string
     */
    protected $name = 'Master data';

    /**
     * Font icons
     *
     * @var string
     */
    protected $icon = 'fa fa-book'; // fontawesome

    /**
     * Menu description
     *
     * @var string
     */
    protected $description = 'User can access master data';

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
            // CategoryKlienPengiriman::class,
            // DropPointMenu::class,
            CollectionpointMenu::class,
            RateTarifGradeAMenu::class,
            RateTarifGradeBMenu::class,
            RateTarifGradeCMenu::class,
            RateTarifDeliveryFeeMenu::class
        ];
    }
}
