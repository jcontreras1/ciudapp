<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIncidentCommentRequest;
use App\Http\Resources\IncidentResource;
use App\Models\Incident;
use App\Models\IncidentComment;
use App\Models\Institution;
use Illuminate\Http\Request;

class ApiIncidentCommentController extends Controller
{
    public function store(Institution $institution, Incident $incident, StoreIncidentCommentRequest $request)
    {
        IncidentComment::create([
            'comment' => $request->comment,
            'incident_id' => $incident->id,
            'user_id' => $request->user()->id,
        ]);
        
        return response(new IncidentResource($incident), 201);
    }

    public function destroy(Institution $institution, Incident $incident, IncidentComment $comment)
    {
        //Si el comentario no pertenece al incidente
        if($comment->incident_id !== $incident->id){
            return response(['error' => 'Comment not found'], 404);
        }
        //solo el dueño del comentario puede borrarlo
        if($comment->user_id !== auth()->id()){
            return response(['error' => 'Unauthorized'], 401);
        }
        if($incident->status->code === 'closed' || $incident->status->code === 'resolved'){
            return response(['error' => 'Incident closed'], 400);
        }
        if($incident->institution_id !== $institution->id){
            return response(['error' => 'Incident not found'], 404);
        }
        $comment->delete();
        return response(new IncidentResource($incident), 200);
    }

}
