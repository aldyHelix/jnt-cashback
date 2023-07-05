<?php

namespace Modules\UploadFile\Menus;

use Ladmin\Engine\Contracts\MenuDivider;
use Ladmin\Engine\Menus\Gate;
use Ladmin\Engine\Supports\BaseMenu;

class UploadMenu extends BaseMenu
{

    /**
     * Gate name for accessing module
     *
     * @var string
     */
    protected $gate = 'ladmin.uploadfile.index';

    /**
     * Name of menu
     *
     * @var string
     */
    protected $name = 'Upload';

    /**
     * Font icons
     *
     * @var string
     */
    protected $icon = 'fas fa-file-arrow-up'; // fontawesome

    /**
     * Menu description
     *
     * @var string
     */
    protected $description = 'User can access module upload file';

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
        return ['ladmin.uploadfile.welcome'];
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
