<?php

namespace App\Http\Controllers;

use App\Services\DoctorReceiptService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;

class DoctorReceiptController extends Controller
{
    public function __construct(private DoctorReceiptService $doctorReceiptService)
    {
    }

    public function createReceipt(Request $request)
    {
        try {
            $receipt = $this->doctorReceiptService->createReceipt($request);

            return response()->json([
                'success' => true,
                'message' => 'Receipt uploaded successfully',
                'data' => $receipt
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getAllReceipts()
    {
        try {
            $receipts = $this->doctorReceiptService->getAllReceipts();

            return response()->json([
                'success' => true,
                'message' => 'Receipts fetched successfully',
                'data' => $receipts
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getReceiptById(int $id)
    {
        try {
            $receipt = $this->doctorReceiptService->getReceiptById($id);

            return response()->json([
                'success' => true,
                'message' => 'Receipt fetched successfully',
                'data' => $receipt
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Receipt not found'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateReceipt(Request $request, int $id)
    {
        try {
            $receipt = $this->doctorReceiptService->updateReceipt($request, $id);

            return response()->json([
                'success' => true,
                'message' => 'Receipt updated successfully',
                'data' => $receipt
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Receipt not found'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteReceipt(int $id)
    {
        try {
            $this->doctorReceiptService->deleteReceipt($id);

            return response()->json([
                'success' => true,
                'message' => 'Receipt deleted successfully'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Receipt not found'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
