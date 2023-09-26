<?php

namespace Modules\ProcessWizard\Http\Controllers;

use Illuminate\Http\Request;

class WizardController extends Controller
{
    public function index() {
        ladmin()->allows(['ladmin.processwizard.index']);

        return view('processwizard::index');
    }
}
