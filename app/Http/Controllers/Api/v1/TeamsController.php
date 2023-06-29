<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class TeamsController extends Controller
{
    public function createTeam(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Create a new team
        $team = Team::create([
            'name' => $request->name,
        ]);

        //now make the user that created the team a member and leader
        $team->addLeader($request->user());
        // $team->addUser($request->user());

        return response()->json([
                'message' => 'Team created successfully',
                'data' => $team,
            ], 201);
    }


    public function addTeamMember(Request $request)
    {
        // Validate the request data
        $request->validate([
            'team_id' => 'required|integer',
            'user_id' => 'required|integer',
        ]);

        // Check if team exists
        $team = Team::find($request->team_id);
        if (!$team) {
            return response()->json([
                'message' => 'Team not found',
            ], 404);
        }

        // Check if user exists
        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        // Check if user is already a member of the team
        $isMember = $team->hasUser($user);
        if ($isMember) {
            return response()->json([
                'message' => 'User is already a member of the team',
            ], 400);
        }

        // Add user to team
        $team->addUser($user);

        //response
        return response()->json([
            'message' => 'User added to team successfully',
            'team' => $team,
        ], 201);
    }


    // Remove user from team
    public function removeTeamMember(Request $request)
    {
        // Validate the request data
        $request->validate([
            'team_id' => 'required|integer',
            'user_id' => 'required|integer',
        ]);

        // Check if team exists
        $team = Team::find($request->team_id);
        if (!$team) {
            return response()->json([
                'message' => 'Team not found',
            ], 404);
        }

        // Check if user exists
        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        // Check if user is a member of the team
        $isMember = $team->hasUser($user);
        if (!$isMember) {
            return response()->json([
                'message' => 'User is not a member of the team',
            ], 400);
        }

        // Remove user from team
        $team->removeUser($user);

        //response
        return response()->json([
            'message' => 'User removed from team successfully',
            'team' => $team,
        ], 201);
    }


    // Get all teams a user belongs to
    public function getUserTeams(Request $request)
    {
        // Validate the request data
        $request->validate([
            'user_id' => 'required|integer',
        ]);

        // Check if user exists
        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        // Get all teams the user belongs to
        $teams = $user->teams;

        //response
        return response()->json([
            'message' => 'User teams retrieved successfully',
            'teams' => $teams,
        ], 200);
    }


    //make team leader
    public function makeTeamLeader(Request $request)
    {
        // Validate the request data
        $request->validate([
            'team_id' => 'required|integer',
            'user_id' => 'required|integer',
        ]);

        // Check if team exists
        $team = Team::find($request->team_id);
        if (!$team) {
            return response()->json([
                'message' => 'Team not found',
            ], 404);
        }

        // Check if user exists
        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        // Check if user is a member of the team
        $isMember = $team->hasUser($user);
        if (!$isMember) {
            return response()->json([
                'message' => 'User is not a member of the team',
            ], 400);
        }

        // Make user a team leader
        $team->addLeader($user);

        //response
        return response()->json([
            'message' => 'User is now a team leader',
            'team' => $team,
        ], 201);
    }


    //removeTeamLeader
    public function removeTeamLeader(Request $request)
    {
        // Validate the request data
        $request->validate([
            'team_id' => 'required|integer',
            'user_id' => 'required|integer',
        ]);

        // Check if team exists
        $team = Team::find($request->team_id);
        if (!$team) {
            return response()->json([
                'message' => 'Team not found',
            ], 404);
        }

        // Check if user exists
        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        // Check if user is a member of the team
        $isMember = $team->hasUser($user);
        if (!$isMember) {
            return response()->json([
                'message' => 'User is not a member of the team',
            ], 400);
        }

        //check if user_role_id is 1
        if ($user->user_role_id == 1) {
            return response()->json([
                'message' => 'User is a Super Admin',
            ], 400);
        }

        // Remove user as team leader
        $team->removeLeader($user);

        //response
        return response()->json([
            'message' => 'User is no longer a team leader',
            'team' => $team,
        ], 201);
    }


    // Get all team leaders
    public function getTeamLeaders(Request $request)
    {
        // Validate the request data
        $request->validate([
            'team_id' => 'required|integer',
        ]);

        // Check if team exists
        $team = Team::find($request->team_id);
        if (!$team) {
            return response()->json([
                'message' => 'Team not found',
            ], 404);
        }

        // Get all team leaders
        $leaders = $team->members()->where('is_leader', true)->get();

        //response
        return response()->json([
            'message' => 'Team leaders retrieved successfully',
            'leaders' => $leaders,
        ], 200);
    }


    //get list showing teams and their member or leaders roles
    public function getTeamMembersWithRoles(Request $request)
    {
        // Validate the request data
        $request->validate([
            'team_id' => 'required|integer',
        ]);

        // Check if team exists
        $team = Team::find($request->team_id);
        if (!$team) {
            return response()->json([
                'message' => 'Team not found',
            ], 404);
        }

        // Get all team members with their roles
        $members = $team->members()->withPivot('is_leader')->get();

        //response
        return response()->json([
            'message' => 'Team members retrieved successfully',
            'members' => $members,
        ], 200);
    }


    //update team name by only team leader
    public function updateTeamName(Request $request)
    {
        // Validate the request data
        $request->validate([
            'team_id' => 'required|integer',
            'name' => 'required|string|max:255',
        ]);

        // Check if team exists
        $team = Team::find($request->team_id);
        if (!$team) {
            return response()->json([
                'message' => 'Team not found',
            ], 404);
        }
        // Check if user is a team leader
        $isLeader = $team->members()->where('user_id', $request->user()->id)->where('is_leader', true)->exists();
        if (!$isLeader) {
            return response()->json([
                'message' => 'You are not a team leader',
            ], 400);
        }

        // Update team name
        $team->update([
            'name' => $request->name,
        ]);

        //response
        return response()->json([
            'message' => 'Team name updated successfully',
            'team' => $team,
        ], 201);
    }


    //team leader can delete team
    public function deleteTeam(Request $request)
    {
        // Validate the request data
        $request->validate([
            'team_id' => 'required|integer',
        ]);

        // Check if team exists
        $team = Team::find($request->team_id);
        if (!$team) {
            return response()->json([
                'message' => 'Team not found',
            ], 404);
        }
        // Check if user is a team leader
        $isLeader = $team->members()->where('user_id', $request->user()->id)->where('is_leader', true)->exists();
        if (!$isLeader) {
            return response()->json([
                'message' => 'You are not a team leader',
            ], 400);
        }

        // Delete team
        $team->delete();

        //response
        return response()->json([
            'message' => 'Team deleted successfully',
        ], 201);
    }


    //team leader add user to his team
    public function addTeamMemberByLeader(Request $request)
    {
        // Validate the request data
        $request->validate([
            'team_id' => 'required|integer',
            'user_id' => 'required|integer',
        ]);

        // Check if team exists
        $team = Team::find($request->team_id);
        if (!$team) {
            return response()->json([
                'message' => 'Team not found',
            ], 404);
        }
        // Check if user is a team leader
        $isLeader = $team->members()->where('user_id', $request->user()->id)->where('is_leader', true)->exists();
        if (!$isLeader) {
            return response()->json([
                'message' => 'You are not a team leader',
            ], 400);
        }

        // Check if user exists
        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        // Check if user is already a member of the team
        $isMember = $team->hasUser($user);
        if ($isMember) {
            return response()->json([
                'message' => 'User is already a member of the team',
            ], 400);
        }

        // Add user to team
        $team->addUser($user);

        //response
        return response()->json([
            'message' => 'User added to team successfully',
            'team' => $team,
        ], 201);
    }
}
