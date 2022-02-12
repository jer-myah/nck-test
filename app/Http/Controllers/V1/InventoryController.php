<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteInventoryRequest;
use App\Http\Requests\InventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;
use App\Models\Inventory;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    private $inventoryService;

    /**
     * InventoryController constructor.
     * @param InventoryService $inventoryService
     */
    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(User $user)
    {
        try {
            $inventories = $this->inventoryService->getInventories();
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'An error occurred...',
                'error' => $exception->getMessage()
            ], 500);
        }

        if (auth()->user()->role == $user->getAdminRole()) {
            return response()->json([
                'inventories' => $inventories
            ], 200);
        }

        return response()->json([
            'inventories' => $inventories->makeHidden(['created_at', 'updated_at'])
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(InventoryRequest $request)
    {
        try {
            $inventory = $this->inventoryService->storeInventory($request);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'An error occurred...',
                'error' => $exception->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Inventory successfully created!',
            'inventory' => $inventory->makeHidden('updated_at')
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @param \App\Models\Inventory $inventory
     * @return \Illuminate\Http\Response
     */
    public function show(User $user, $inventory)
    {
        try {
            $inventory = $this->inventoryService->getOneInventory($inventory);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'An error occurred...',
                'error' => $exception->getMessage()
            ], 500);
        }

        if (auth()->user()->role == $user->getAdminRole()) {
            return response()->json([
                'inventory' => $inventory
            ], 200);
        }

        return response()->json([
            'inventory' => $inventory->makeHidden(['created_at', 'updated_at'])
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param InventoryRequest $request
     * @param \App\Models\Inventory $inventory
     * @return void
     */
    public function update(UpdateInventoryRequest $request)
    {
        try {
            $inventory = $this->inventoryService->updateInventory($request);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'An error occurred...',
                'error' => $exception->getMessage()
            ], 500);
        }

        if ($inventory) {
            return response()->json([
                'message' => 'Inventory successfully updated!',
            ], 201);
        }

        return response()->json([
            'message' => 'Unable to update!',
        ], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DeleteInventoryRequest $request
     * @param \App\Models\Inventory $inventory
     * @return void
     */
    public function destroy(DeleteInventoryRequest $request)
    {
        try {
            $this->inventoryService->deleteOneInventory($request->id);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'An error occurred...',
                'error' => $exception->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Inventory successfully deleted!'
        ], 200);
    }
}
