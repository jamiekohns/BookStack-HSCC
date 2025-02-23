<?php

namespace BookStack\App;

use BookStack\Activity\ActivityQueries;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Queries\EntityQueries;
use BookStack\Entities\Queries\QueryRecentlyViewed;
use BookStack\Entities\Queries\QueryTopFavourites;
use BookStack\Entities\Tools\PageContent;
use BookStack\Http\Controller;
use BookStack\Util\SimpleListOptions;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        dd('AttendanceController:index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Playground $playground)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Playground $playground)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Playground $playground)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Playground $playground)
    {
        //
    }
}
