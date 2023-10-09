<?php

namespace Modules\GlobalSetting\Menus;

use Ladmin\Engine\Contracts\MenuDivider;
use Ladmin\Engine\Menus\Gate;
use Ladmin\Engine\Supports\BaseMenu;

class SettingsMenu extends BaseMenu
{

    /**
     * Gate name for accessing module
     *
     * @var string
     */
    protected $gate = 'ladmin.globalsetting.setting.index';

    /**
     * Name of menu
     *
     * @var string
     */
    protected $name = 'General Setting';

    /**
     * Font icons
     *
     * @var string
     */
    protected $icon = 'fas fa-cogs'; // fontawesome

    /**
     * Menu description
     *
     * @var string
     */
    protected $description = 'User can access General Setting';

    /**
     * Inspecting The Request Path / Route active
     * https://laravel.com/docs/master/requests#inspecting-the-request-path
     *
     * @var string
     */
    protected $isActive = 'globalsetting/setting*';

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
        return ['ladmin.globalsetting.setting.index'];
    }

    /**
     * Other gates
     *
     * @return Array(Ladmin\Engine\Menus\Gate)
     */
    protected function gates()
    {
        return [
            new Gate(gate: 'ladmin.globalsetting.setting.create', title: 'Create New General Setting', description: 'User can create new general Setting'),
            new Gate(gate: 'ladmin.globalsetting.setting.update', title: 'Update General Setting', description: 'User can update general Setting'),
            new Gate(gate: 'ladmin.globalsetting.setting.delete', title: 'Delete General Setting', description: 'User can delete general Setting'),
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
