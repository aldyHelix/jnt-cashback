<?php

namespace Modules\Globalsetting\Menus;

use Ladmin\Engine\Menus\Gate;
use Ladmin\Engine\Supports\BaseMenu;
use Modules\Category\Menus\CategoryKlienPengiriman;
use Modules\Droppointoutgoing\Menus\DropPointMenu;
use Modules\Ladmin\Menus\Submenus\Permission;
use Modules\Ladmin\Menus\Submenus\Role;

class GlobalsettingsMenu extends BaseMenu
{
    /**
     * Gate of default menu
     *
     * @var string
     */
    protected $gate = 'global-setting.index';

    /**
     * Menu title
     *
     * @var string
     */
    protected $name = 'Global Settings';

    /**
     * Menu Font icon
     *
     * @var string
     */
    protected $icon = 'fa-solid fa-globe'; // fontawesome

    /**
     * Menu Description
     *
     * @var string
     */
    protected $description = 'User can access menu global setting';

    /**
     * Status active menu
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
     * @return Array|null
     * @example ['route.name', ['uuid', 'foo' => 'bar']]
     */
    protected function route(): ?array
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
     * Submenu
     *
     * @return void
     */
    protected function submenus()
    {
        return [

            CategoryKlienPengiriman::class,

            DropPointMenu::class,

            SettingsMenu::class,

            SumberWaybillMenu::class,
        ];
    }
}
