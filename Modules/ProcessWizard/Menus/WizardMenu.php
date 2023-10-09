<?php

namespace Modules\ProcessWizard\Menus;

use Ladmin\Engine\Contracts\MenuDivider;
use Ladmin\Engine\Menus\Gate;
use Ladmin\Engine\Supports\BaseMenu;

class WizardMenu extends BaseMenu
{

    /**
     * Gate name for accessing module
     *
     * @var string
     */
    protected $gate = 'ladmin.processwizard.index';

    /**
     * Name of menu
     *
     * @var string
     */
    protected $name = 'Wizard';

    /**
     * Font icons
     *
     * @var string
     */
    protected $icon = 'fa fa-magic'; // fontawesome

    /**
     * Menu description
     *
     * @var string
     */
    protected $description = 'User can access module name';

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
        return ['ladmin.processwizard.index'];
    }

    /**
     * Other gates
     *
     * @return Array(Ladmin\Engine\Menus\Gate)
     */
    protected function gates()
    {
        return [
            new Gate(gate: 'ladmin.processwizard.create', title: 'Create New process wizard', description: 'User can create new process wizard data'),
            new Gate(gate: 'ladmin.processwizard.update', title: 'Update process wizard', description: 'User can update process wizard'),
            new Gate(gate: 'ladmin.processwizard.delete', title: 'Delete process wizard', description: 'User can update process wizard'),
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
