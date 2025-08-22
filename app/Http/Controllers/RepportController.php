<?php

namespace App\Http\Controllers;

use App\Models\Projects;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RepportController extends Controller
{
    private function getFilterData()
    {
        return [
            'projects' => Projects::select('id', 'name')->get(),
        ];
    }
    public function index(){
        return Inertia::render('Reppport/Index');
    }
    public function projects(){
        return Inertia::render('Reppport/Projects/Index',[
            'filter' => $this->getFilterData()
        ]);
    }
}
