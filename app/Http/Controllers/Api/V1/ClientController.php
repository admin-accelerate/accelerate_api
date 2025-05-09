<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ClientResource;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;

/**
 * @OA\Schema(
 *     schema="Client",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="email", type="string"),
 *     @OA\Property(property="phone", type="string", nullable=true),
 *     @OA\Property(property="address", type="string", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time")
 * )
 */
class ClientController extends Controller
{

     /**
     * @OA\Get(
     *     path="/api/v1/clients",
     *     tags={"Clients"},
     *     summary="List all clients",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by name or email",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of clients",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Client")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = Client::query();
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }
        return ClientResource::collection($query->paginate(10));
    }


    /**
     * @OA\Post(
     *     path="/api/v1/clients",
     *     tags={"Clients"},
     *     summary="Create a new client",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="phone", type="string", example="1234567890", nullable=true),
     *             @OA\Property(property="address", type="string", example="123 Main St", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Client created",
     *         @OA\JsonContent(ref="#/components/schemas/Client")
     *     )
     * )
     */
    public function store(StoreClientRequest $request)
    {
        if (Auth::user()->role !== "admin") {
            return response()->json([
                'message' => 
                "Vous n'avez l'autorisation d'éffectuer cette action"], 
                403);
        }
        else {

            $client = Client::create($request->validated());

            return response()->json([
                'client' => new ClientResource($client),
                'message' => 'Client créé avec succès !'
            ], 201);
            
        }  
    }

    /**
     * @OA\Get(
     *     path="/api/v1/clients/{id}",
     *     tags={"Clients"},
     *     summary="Get a client by ID",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Client details",
     *         @OA\JsonContent(ref="#/components/schemas/Client")
     *     )
     * )
     */
    public function show(Client $client)
    {
        return new ClientResource($client);
        
    }

    /**
     * @OA\Put(
     *     path="/api/v1/clients/{id}",
     *     tags={"Clients"},
     *     summary="Update a client",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="phone", type="string", example="1234567890", nullable=true),
     *             @OA\Property(property="address", type="string", example="123 Main St", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Client updated",
     *         @OA\JsonContent(ref="#/components/schemas/Client")
     *     )
     * )
     */
 
    public function update(UpdateClientRequest $request, Client $client)
    {

        if (Auth::user()->role !== "admin") {
            return response()->json([
                'message' => 
                "Vous n'avez l'autorisation d'éffectuer cette action"], 
                403);
        }
      

        $client->update($request->validated());

        return response()->json([
                'client' =>  new ClientResource($client),
                'message' => 'Client mise à jour avec succès !'
        ], 201);
        
    }

    /**
    * @OA\Delete(
    *     path="/api/v1/clients/{id}",
    *     summary="Delete a client",
    *     tags={"Clients"},
    *     security={{"sanctum":{}}},
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         description="Client ID",
    *         required=true,
    *         @OA\Schema(type="integer")
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Client deleted successfully"
    *     ),
    *     @OA\Response(
    *         response=403,
    *         description="Not authorized"
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="Client not found"
    *     )
    * )
    */

    public function destroy(Client $client)
    {
         $user = auth()->user();
        // Check if the authenticated user is an admin
        if (!$user || $user->role !== 'admin') {
            return response()->json([
            'message' => 
            "Vous n'avez l'autorisation d'éffectuer cette action"], 
            403);
        }
       // delete the client
        $client->delete();
        return response()->json(['message' => 'Client supprimé avec succès']);
        
    }
}
