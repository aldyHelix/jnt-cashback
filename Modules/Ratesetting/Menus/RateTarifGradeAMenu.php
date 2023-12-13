<?php

namespace Modules\Ratesetting\Menus;

use Ladmin\Engine\Contracts\MenuDivider;
use Ladmin\Engine\Menus\Gate;
use Ladmin\Engine\Supports\BaseMenu;

class RateTarifGradeAMenu extends BaseMenu
{
    /**
     * Gate name for accessing module
     *
     * @var string
     */
    protected $gate = 'ladmin.ratesetting.grade.a.index';

    /**
     * Name of menu
     *
     * @var string
     */
    protected $name = 'Rate Tarif Grade A';

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
    protected $description = 'User can access Rate Tarif Grading 1';

    /**
     * Inspecting The Request Path / Route active
     * https://laravel.com/docs/master/requests#inspecting-the-request-path
     *
     * @var string
     */
    protected $isActive = 'ratesetting/grade-a*';

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
        return ['ladmin.ratesetting.grade-a.index'];
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
            new Gate(gate: 'ladmin.ratesetting.grade.a.create', title: 'Create New Grade A', description: 'User can create new Grade A data'),
            new Gate(gate: 'ladmin.ratesetting.grade.a.update', title: 'Update Grade A', description: 'User can update Grade A'),
            new Gate(gate: 'ladmin.ratesetting.grade.a.delete', title: 'Delete Grade A', description: 'User can update Grade A'),
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
