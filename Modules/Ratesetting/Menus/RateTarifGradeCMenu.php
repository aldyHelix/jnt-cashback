<?php

namespace Modules\Ratesetting\Menus;

use Ladmin\Engine\Contracts\MenuDivider;
use Ladmin\Engine\Menus\Gate;
use Ladmin\Engine\Supports\BaseMenu;

class RateTarifGradeCMenu extends BaseMenu
{
    /**
     * Gate name for accessing module
     *
     * @var string
     */
    protected $gate = 'ladmin.ratesetting.grade.c.index';

    /**
     * Name of menu
     *
     * @var string
     */
    protected $name = 'Rate Tarif Grade C';

    /**
     * Font icons
     *
     * @var string
     */
    protected $icon = 'fa fa-percent'; // fontawesome

    /**
     * Menu description
     *
     * @var string
     */
    protected $description = 'User can access Rate Tarif Grading C';

    /**
     * Inspecting The Request Path / Route active
     * https://laravel.com/docs/master/requests#inspecting-the-request-path
     *
     * @var string
     */
    protected $isActive = 'ratesetting/grade-c*';

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
        return ['ladmin.ratesetting.grade-c.index'];
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
            new Gate(gate: 'ladmin.ratesetting.grade.c.create', title: 'Create New Grade C', description: 'User can create new Grade C data'),
            new Gate(gate: 'ladmin.ratesetting.grade.c.update', title: 'Update Grade C', description: 'User can update Grade C'),
            new Gate(gate: 'ladmin.ratesetting.grade.a.destroy', title: 'Delete Grade C', description: 'User can update Grade C'),
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
