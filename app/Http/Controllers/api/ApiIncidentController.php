<?php

namespace App\Http\Controllers\api;

use App\Events\NewIncidentEvent;
use App\Events\NewTraceIncidentEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddPostsToIncidentRequest;
use App\Http\Requests\StoreIncidentRequest;
use App\Http\Requests\StoreIncidentTraceRequest;
use App\Http\Resources\IncidentResource;
use App\Models\Incident;
use App\Models\IncidentStatus;
use App\Models\Institution;
use App\Models\Post;
use Illuminate\Http\Request;
use Response;

class ApiIncidentController extends Controller
{
    public function index(Institution $institution)
    {
        return response(IncidentResource::collection($institution->incidents),200);
    }
    
    public function show(Institution $institution, Incident $incident)
    {
        return response(new IncidentResource($incident),200);
    }

    public function nearby(Institution $institution, Incident $incident, Post $post)
    {
       return response(postCercanos($post, $institution), 200);
    }
    
    public function store(StoreIncidentRequest $request, Institution $institution)
    {
        $status = IncidentStatus::where('code', 'open')->firstOrFail()->id;
        $incident = Incident::create(
            array_merge(
                $request->validated(),
                [
                    'institution_id' => $institution->id,
                    'user_id' => auth()->id(),
                    'status_id' => $status,
                ],            
            ));
            
            NewIncidentEvent::dispatch($incident);
            return response(IncidentResource::collection($institution->incidents),201);
        }
        
        public function update(Institution $institution, Incident $incident, StoreIncidentRequest $request)
        {
            $incident->update($request->validated());
            return response(new IncidentResource($incident),200);
        }
        
        public function destroy(Institution $institution, Incident $incident)
        {
            //eliminar comentarios, historial, y actualizar posts si los tiene
            $postsAsociados = $incident->posts;
            foreach ($postsAsociados as $post) {
                $post->incident_id = null;
                $post->save();
            }
            
            $incident->comments()->delete();
            $incident->history()->delete();
            
            $incident->delete();
            return response(IncidentResource::collection($institution->incidents),200);
        }
        
        /**
        * Cambiar de estado de un incidente
        */
        public function changeStatus(Institution $institution, Incident $incident, StoreIncidentTraceRequest $request){

            if($incident->status_id == $request->status_id){
                return response('El estado del incidente no puede ser igual al estado anterior', 400);
            }
            $status = IncidentStatus::find($request->status_id);
            $incident->status_id = $status->id;
            $incident->save();

            //restaurar posts
            if($status->code == 'canceled'){
                $incident->posts()->update(['incident_id' => null]);
            }

            NewTraceIncidentEvent::dispatch($incident, $request->description);
            return response(new IncidentResource($incident),201);
        }
        
        
        /**
        * Crear un incidente a partir de un post
        * Primero crea un incidente, y agrega este post por el cual viene
        * De paso analiza que otros posibles posts pueden ser parte del incidente
        * El helper para resolverlo se encuentra en Helpers/Geo
        * @param \App\Models\Post $post
        * @param \App\Models\Institution $institution
        * @return void
        */
        public function makeIncidentFromPost(Institution $institution, Post $post, StoreIncidentRequest $request): \Illuminate\Http\Response{
            
            if($post->incident_id){
                return response(['error' => 'El post ya es parte de un incidente'], 400);
            }
            
            $status = IncidentStatus::where('code', 'open')->firstOrFail()->id;
            
            $incident = Incident::create(
                array_merge(
                    $request->validated(), 
                    [
                        'institution_id' => $institution->id,
                        'user_id' => auth()->id(),
                        'status_id' => $status,
                        'post_original_id' => $post->id,
                    ],            
                    )
                );
                //actualizar el post
                $post->incident_id = $incident->id;
                $post->save();
                NewIncidentEvent::dispatch($incident);
                //buscar posts cercanos para agregarlos al incidente
                // $postsCercanos = postCercanos($post, $institution);
                return response($incident, 200);
            }
            
            public function addPostsToIncident(Institution $institution, Incident $incident, AddPostsToIncidentRequest $request){
                $request->validate([
                    'posts' => 'required|array',
                    'posts.*' => 'required|integer|exists:post,id',
                ]);
                $posts = $request->posts;
                foreach ($posts as $post) {
                    $post = Post::find($post);
                    $post->incident_id = $incident->id;
                    $post->save();
                }
                return response([
                    'incident' => new IncidentResource($incident),
                    'postsRelacionados' => postCercanos($incident->postOriginal, $institution),

                ], 200);
            }
            
        }
        