<?php

namespace Modules\GlobalSetting\Menus;

use Ladmin\Engine\Contracts\MenuDivider;
use Ladmin\Engine\Menus\Gate;
use Ladmin\Engine\Supports\BaseMenu;

class RekapMenu extends BaseMenu
{

    /**
     * Gate name for accessing module
     *
     * @var string
     */
    protected $gate = 'ladmin.globalsetting.rekap.index';

    /**
     * Name of menu
     *
     * @var string
     */
    protected $name = 'Rekap Total Biaya Kirim';

    /**
     * Font icons
     *
     * @var string
     */
    protected $icon = 'fa fa-regular fa-calculator'; // fontawesome

    /**
     * Menu description
     *
     * @var string
     */
    protected $description = 'User can access rekap total biaya kirim';

    /**
     * Inspecting The Request Path / Route active
     * https://laravel.com/docs/master/requests#inspecting-the-request-path
     *
     * @var string
     */
    protected $isActive = 'globalsetting/rekap*';

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
        return ['ladmin.globalsetting.rekap.index'];
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
        ];
    }
}
