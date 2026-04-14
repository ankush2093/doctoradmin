<?php

namespace App\Services;

use App\Models\DoctorReceipt;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DoctorReceiptService
{
    private const STORAGE_DIR = 'uploads/receipt';
    private const PUBLIC_PATH_PREFIX = '/storage/uploads/receipt/';

    public function createReceipt(Request $request): DoctorReceipt
    {
        $validator = Validator::make($request->all(), [
            'receipt' => 'required|file|mimes:jpeg,png,jpg,pdf',
            'receipt_no' => 'nullable|string|max:255',
            'created_by' => 'nullable|integer|min:1',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        $fileName = $this->storeReceiptFile($request->file('receipt'));
        $createdBy = $request->filled('created_by')
            ? (int) $request->input('created_by')
            : optional($request->user())->id;

        $receipt = DoctorReceipt::create([
            'receipt' => $fileName,
            'receipt_url' => $this->buildReceiptUrl($fileName),
            'receipt_no' => $request->input('receipt_no'),
            'created_by' => $createdBy,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
        ]);

        return $this->formatReceipt($receipt);
    }

    public function getAllReceipts()
    {
        return DoctorReceipt::where('is_active', true)
            ->latest()
            ->get()
            ->map(function (DoctorReceipt $receipt) {
                return $this->formatReceipt($receipt);
            });
    }

    public function getReceiptById(int $id): DoctorReceipt
    {
        $receipt = DoctorReceipt::where('id', $id)
            ->where('is_active', true)
            ->first();

        if (!$receipt) {
            throw (new ModelNotFoundException())->setModel(DoctorReceipt::class, [$id]);
        }

        return $this->formatReceipt($receipt);
    }

    public function updateReceipt(Request $request, int $id): DoctorReceipt
    {
        $receipt = DoctorReceipt::find($id);

        if (!$receipt || !$receipt->is_active) {
            throw (new ModelNotFoundException())->setModel(DoctorReceipt::class, [$id]);
        }

        $validator = Validator::make($request->all(), [
            'receipt' => 'sometimes|file|mimes:jpeg,png,jpg,pdf',
            'receipt_no' => 'sometimes|nullable|string|max:255',
            'created_by' => 'sometimes|nullable|integer|min:1',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        if ($request->hasFile('receipt')) {
            $this->deleteReceiptFile($receipt->receipt);
            $receipt->receipt = $this->storeReceiptFile($request->file('receipt'));
            $receipt->receipt_url = $this->buildReceiptUrl($receipt->receipt);
        } elseif (!$receipt->receipt_url && $receipt->receipt) {
            $receipt->receipt_url = $this->buildReceiptUrl($receipt->receipt);
        }

        if ($request->exists('receipt_no')) {
            $receipt->receipt_no = $request->input('receipt_no');
        }

        if ($request->exists('created_by')) {
            $receipt->created_by = $request->filled('created_by')
                ? (int) $request->input('created_by')
                : null;
        }

        if ($request->has('is_active')) {
            $receipt->is_active = $request->boolean('is_active');
        }

        $receipt->save();

        return $this->formatReceipt($receipt);
    }

    public function deleteReceipt(int $id): void
    {
        $receipt = DoctorReceipt::find($id);

        if (!$receipt) {
            throw (new ModelNotFoundException())->setModel(DoctorReceipt::class, [$id]);
        }

        $this->deleteReceiptFile($receipt->receipt);
        $receipt->update(['is_active' => false]);
    }

    private function storeReceiptFile($file): string
    {
        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $sanitizedBaseName = preg_replace('/\s+/', '_', $name);
        $currentDateTime = now()->format('Y-m-d_H-i-s');
        $extension = strtolower($file->getClientOriginalExtension());
        $filename = $sanitizedBaseName . '_' . $currentDateTime . '.' . $extension;

        $file->storeAs(self::STORAGE_DIR, $filename, 'public');

        return $filename;
    }

    private function deleteReceiptFile(?string $fileName): void
    {
        if (!$fileName) {
            return;
        }

        $filePath = self::STORAGE_DIR . '/' . $fileName;

        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
    }

    private function formatReceipt(DoctorReceipt $receipt): DoctorReceipt
    {
        if (!$receipt->receipt_url && $receipt->receipt) {
            $receipt->receipt_url = $this->buildReceiptUrl($receipt->receipt);
        }

        return $receipt;
    }

    private function buildReceiptUrl(string $fileName): string
    {
        return url(self::PUBLIC_PATH_PREFIX . $fileName);
    }
}
