<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\UserCondition;
use Illuminate\Http\Request;

class UserConditionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function maptest(){
        $markerList = $this->getUserConditions_as_Marker();
        return view('maps.maptest', compact('markerList'));
    }
    
    public function getUserConditions_as_Marker(){
        $user_conditions = DB::connection('mysql_2')->table('user_info_sub')->get();
        $markerList = "";
        foreach($user_conditions as $user_condition){
            if ($markerList !== "") {
                $markerList .= sprintf(", \n{ pos: [%s, %s], name: \"%s\" }", $user_condition->user_lat, $user_condition->user_lng, $user_condition->user_name);
            } else {
                $markerList = sprintf(", \n{ pos: [%s, %s], name: \"%s\" }", $user_condition->user_lat, $user_condition->user_lng, $user_condition->user_name);
            }
        }
        return $markerList;
    }
    
    public function index()
    {
        $user_conditions = DB::connection('mysql_2')->table('user_info_sub')->paginate(10);
        foreach($user_conditions as $user_condition){
            printf("%s, %s<br>", $user_condition->user_lat, $user_condition->user_lng);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\UserCondition  $userCondition
     * @return \Illuminate\Http\Response
     */
    public function show(UserCondition $userCondition)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\UserCondition  $userCondition
     * @return \Illuminate\Http\Response
     */
    public function edit(UserCondition $userCondition)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UserCondition  $userCondition
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserCondition $userCondition)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UserCondition  $userCondition
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserCondition $userCondition)
    {
        //
    }
}
