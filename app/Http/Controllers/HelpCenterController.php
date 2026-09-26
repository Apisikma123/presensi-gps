<?php

namespace App\Http\Controllers;

use App\Models\ModuleFeature;
use Illuminate\Http\Request;

class HelpCenterController extends Controller
{
    /**
     * Display role-aware and module-aware help center
     */
    public function index(Request $request)
    {
        $enabledModules = ModuleFeature::where('is_enabled', true)->pluck('module_code')->toArray();
        $search = $request->search;

        return view('help.index', compact('enabledModules', 'search'));
    }

    public function drawerContent()
    {
        return view('layouts.help_drawer_content');
    }
}
