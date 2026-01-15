<?php

namespace App\Http\Controllers;

use App\Services\TransportService;
use App\Http\Requests\TransportStoreRequest;
use App\Http\Requests\TransportUpdateRequest;
use Illuminate\Http\Request;

class TransportController extends Controller
{
    protected $transportService;

    public function __construct(TransportService $transportService)
    {
        $this->transportService = $transportService;
    }

    public function store(TransportStoreRequest $request)
    {
        $vendorId = $request->header('vendor_id');

        $result = $this->transportService->storeTransport($request, $vendorId);

        return response()->json($result, $result['status']);
    }

    public function index(Request $request)
    {
        $vendorId = $request->header('vendor_id');

        $result = $this->transportService->indexTransports($vendorId);

        return response()->json($result);
    }

    public function update(TransportUpdateRequest $request, $id)
    {
        $vendorId = $request->header('vendor_id');

        $result = $this->transportService->updateTransport($request, $id, $vendorId);

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['status']);
        }

        return response()->json($result);
    }

    public function destroy(Request $request, $id)
    {
        $vendorId = $request->header('vendor_id');

        $result = $this->transportService->destroyTransport($id, $vendorId);

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['status']);
        }

        return response()->json($result);
    }
}