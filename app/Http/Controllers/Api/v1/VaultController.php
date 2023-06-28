<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class VaultController extends Controller
{

    //create credential
    public function createVault(Request $request){
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'content' => 'required',
            'note' => 'nullable|string|max:255',
            'visibility' => 'nullable|boolean',
        ]);

        $content = $request->content;
        if($request->hasFile('content')){
            $uploadedFile = $request->file('content')->getRealPath();
            $content = $this->handleUpload($uploadedFile);
        }


        // Create a new team
        $vault = Vault::create([
            'user_id' => auth()->user()->id,
            'name' => $request->name,
            'type' => $request->type,
            'content' => $content,
            'note' => $request->note,
            'visibility' => $request->visibility??true,
        ]);

        //response
        return response()->json([
            'message' => 'Vault created successfully',
            'data' => $vault,
        ], 201);
    }


    public function handleUpload($uploadedFile){
        $result = Cloudinary::upload($uploadedFile, [
            'resource_type' => 'auto', // Auto-detect resource type (PDF in this case)
            'folder' => 'pdfs/', // Optional: Specify a folder in Cloudinary to store the PDF files
            'access_mode' => 'public', // Set the access mode to public
            'type' => 'upload', // Set the upload type to 'upload' for public access
            'is_public' => true, // Set the file permission to public
        ]);

        // Get the public URL of the uploaded file from the Cloudinary response
        $fileUrl = $result->getSecurePath();

        $value = ['docLink' => $fileUrl, JSON_PRETTY_PRINT];
        return $value;

    }


    //update vault
    public function updateVault(Request $request){
        // Validate the request data
        $data = $request->validate([
            'vault_id' => 'nullable',
            'name' => 'nullable',
            'type' => 'nullable',
            'content' => 'nullable',
            'note' => 'nullable',
            'visibility' => 'nullable',
        ]);

        $vault = Vault::find($request->vault_id);
        if(!$vault){
            return response()->json([
                'message' => 'not found',
            ], 404);
        }

        $vault->fill($data);
        $vault->save();

        //response
        return response()->json([
            'message' => 'Vault updated successfully',
            'data' => $vault,
        ], 200);
    }


    //delete credential
    public function deleteVault(Request $request){
        // Validate the request data
        $request->validate([
            'vault_id' => 'required|string|max:255',
        ]);

        $vault = Vault::find($request->vault_id);
        $vault->delete();

        //response
        return response()->json([
            'message' => 'Team deleted successfully',
        ], 200);
    }


    //add team to credential
    public function addTeam(Request $request){
        // Validate the request data
        $request->validate([
            'team_id' => 'required|string|max:255',
            'vault_id' => 'required|string|max:255',
        ]);

        // Create a new team
        $vault = Vault::find($request->vault_id);
        $team = Team::find($request->team_id);
        $vault->addTeam($team);

        //response
        return response()->json([
            'message' => 'Team added successfully',
            'data' => $vault,
        ], 200);
    }


    //remove team from credential
    public function removeTeam(Request $request){
        // Validate the request data
        $request->validate([
            'team_id' => 'required|string|max:255',
            'vault_id' => 'required|string|max:255',
        ]);

        // Create a new team
        $vault = Vault::find($request->vault_id);
        $team = Team::find($request->team_id);
        $vault->removeTeam($team);

        //response
        return response()->json([
            'message' => 'Team removed successfully',
            'data' => $vault,
        ], 200);
    }


    //get list of all credential available to a user team
    public function getTeamVaults(Request $request){
        // Validate the request data
        $request->validate([
            'team_id' => 'required|string|max:255',
        ]);

        // Create a new team
        $team = Team::find($request->team_id);
        $vaults = $team->vaults;

        //response
        return response()->json([
            'message' => 'Team vaults retrieved successfully',
            'data' => $vaults,
        ], 200);
    }


    //get individual credential if you're a member of attached team
    public function getVault(Request $request){
        // Validate the request data
        $request->validate([
            'vault_id' => 'required|string|max:255',
            'team_id' => 'required|string|max:255',
        ]);

        // Create a new team
        $vault = Vault::find($request->vault_id);

        //check if team is on vault
        $team = Team::find($request->team_id);
        $hasTeam = $vault->hasTeam($team);

        if(!$hasTeam){
            return response()->json([
                'message' => 'You are not a member of this team',
            ], 401);
        }

        //response
        return response()->json([
            'message' => 'Vault retrieved successfully',
            'data' => $vault,
        ], 200);
    }

}
