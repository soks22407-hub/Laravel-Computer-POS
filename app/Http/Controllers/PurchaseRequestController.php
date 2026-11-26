<?php

namespace App\Http\Controllers;

use App\Exports\PurchaseRequestExport;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseRequestController extends Controller
{
    // ========================================
    // SHARED - Create Form (Employee & Admin)
    // ========================================

    public function create()
    {
        $lowStockProducts = Product::where('quantity', '<=', 5)
            ->whereDoesntHave('variants')
            ->with(['category', 'brand'])
            ->get();

        $lowStockVariants = ProductVariant::where('quantity', '<=', 5)
            ->with('product')
            ->get();

        $role = Auth::user()->utype;

        return view('purchase-requests.create', compact('lowStockProducts', 'lowStockVariants', 'role'));
    }

    // ========================================
    // SHARED - Store (Employee & Admin)
    // ========================================

    public function store(Request $request)
    {
        $request->validate([
            'note'  => 'nullable|string',
            'items' => 'required|array|min:1',
        ]);

        // Check at least one item selected
        $hasSelected = collect($request->items)->contains(fn($item) => !empty($item['selected']));
        if (!$hasSelected) {
            return back()->with('error', 'Please select at least one product to request.')->withInput();
        }

        $pr = PurchaseRequest::create([
            'employee_id' => Auth::id(),
            'note'        => $request->note,
            'status'      => 'pending',
        ]);

        foreach ($request->items as $item) {
            if (!empty($item['selected'])) {
                $pr->items()->create([
                    'product_id'         => $item['product_id'] ?? null,
                    'product_name'       => $item['product_name'],
                    'variant'            => $item['variant'] ?? null,
                    'current_quantity'   => $item['current_quantity'],
                    'requested_quantity' => $item['requested_quantity'],
                    'reason'             => $item['reason'] ?? null,
                ]);
            }
        }

        if (Auth::user()->utype === 'ADM') {
            return redirect()->route('admin.purchase-requests.index')
                ->with('success', 'Purchase request submitted successfully!');
        }

        return redirect()->route('employee.purchase-requests.index')
            ->with('success', 'Purchase request submitted successfully!');
    }

    // ========================================
    // SHARED - Approve (Admin & CEO)
    // ========================================

    public function approve($id)
    {
        $pr = PurchaseRequest::findOrFail($id);

        if ($pr->status !== 'pending') {
            return back()->with('error', 'This request has already been reviewed.');
        }

        $pr->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Purchase request approved successfully!');
    }

    // ========================================
    // SHARED - Reject (Admin & CEO)
    // ========================================

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reject_reason' => 'required|string|max:500'
        ]);

        $pr = PurchaseRequest::findOrFail($id);

        if ($pr->status !== 'pending') {
            return back()->with('error', 'This request has already been reviewed.');
        }

        $pr->update([
            'status'        => 'rejected',
            'reject_reason' => $request->reject_reason,
            'approved_by'   => Auth::id(),
            'approved_at'   => now(),
        ]);

        return back()->with('success', 'Purchase request rejected.');
    }

    // ========================================
    // SHARED - Export Excel (Admin & CEO)
    // ========================================

    public function exportExcel($id)
    {
        $pr = PurchaseRequest::findOrFail($id);
        $filename = 'purchase-request-' . $pr->id . '-' . now()->format('Ymd') . '.xlsx';
        return Excel::download(new PurchaseRequestExport($id), $filename);
    }

    // ========================================
    // SHARED - Export PDF (Admin & CEO)
    // ========================================

    public function exportPdf($id)
    {
        $pr = PurchaseRequest::with(['employee', 'items', 'approvedBy'])->findOrFail($id);
        $pdf = Pdf::loadView('purchase-requests.pdf', compact('pr'));
        return $pdf->download('purchase-request-' . $pr->id . '-' . now()->format('Ymd') . '.pdf');
    }

    // ========================================
    // EMPLOYEE
    // ========================================

    // Employee: List own requests
    public function employeeIndex()
    {
        $requests = PurchaseRequest::where('employee_id', Auth::id())
            ->with('items')
            ->latest()
            ->paginate(10);

        return view('employee.purchase-requests.index', compact('requests'));
    }

    // Employee: Show own request
    public function employeeShow($id)
    {
        $pr = PurchaseRequest::with(['items', 'approvedBy'])
            ->where('employee_id', Auth::id())
            ->findOrFail($id);

        return view('employee.purchase-requests.show', compact('pr'));
    }

    // ========================================
    // ADMIN
    // ========================================

    // Admin: List all requests
    public function adminIndex()
    {
        $requests = PurchaseRequest::with(['employee', 'items'])
            ->latest()
            ->paginate(10);

        // Count by status
        $pendingCount  = PurchaseRequest::where('status', 'pending')->count();
        $approvedCount = PurchaseRequest::where('status', 'approved')->count();
        $rejectedCount = PurchaseRequest::where('status', 'rejected')->count();

        return view('admin.purchase-requests.index', compact(
            'requests',
            'pendingCount',
            'approvedCount',
            'rejectedCount'
        ));
    }

    // Admin: Show detail
    public function adminShow($id)
    {
        $pr = PurchaseRequest::with(['employee', 'items', 'approvedBy'])
            ->findOrFail($id);

        return view('admin.purchase-requests.show', compact('pr'));
    }

    // ========================================
    // CEO
    // ========================================

    // CEO: List all requests
    public function ceoIndex()
    {
        $requests = PurchaseRequest::with(['employee', 'items'])
            ->latest()
            ->paginate(10);

        // Count by status
        $pendingCount  = PurchaseRequest::where('status', 'pending')->count();
        $approvedCount = PurchaseRequest::where('status', 'approved')->count();
        $rejectedCount = PurchaseRequest::where('status', 'rejected')->count();

        return view('ceo.purchase-requests.index', compact(
            'requests',
            'pendingCount',
            'approvedCount',
            'rejectedCount'
        ));
    }

    // CEO: Show detail
    public function ceoShow($id)
    {
        $pr = PurchaseRequest::with(['employee', 'items', 'approvedBy'])
            ->findOrFail($id);

        return view('ceo.purchase-requests.show', compact('pr'));
    }
}
