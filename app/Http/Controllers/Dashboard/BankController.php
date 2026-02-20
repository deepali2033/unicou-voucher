<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\AdminPaymentMethod;
use Illuminate\Support\Facades\Storage;

class BankController extends Controller
{
    public function index()
    {
        $methods = AdminPaymentMethod::all();
        return view('dashboard.banks.manage', compact('methods'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'method_type' => 'required|in:bank,upi,qr',
            'bank_name' => 'nullable|string',
            'account_holder_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'bank_code_value' => 'nullable|string',
            'bank_code_type' => 'nullable|string',
            'upi_id' => 'nullable|string',
            'qr_code' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'notes' => 'nullable|string',
        ]);

        $data = $request->all();

        if ($request->hasFile('qr_code')) {
            $data['qr_code'] = $request->file('qr_code')->store('payment_methods', 'public');
        }

        AdminPaymentMethod::create($data);

        return redirect()->back()->with('success', 'Payment method added successfully.');
    }

    public function update(Request $request, $id)
    {
        $method = AdminPaymentMethod::findOrFail($id);

        $request->validate([
            'method_type' => 'required|in:bank,upi,qr',
            'bank_name' => 'nullable|string',
            'account_holder_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'ifsc_code' => 'nullable|string',
            'upi_id' => 'nullable|string',
            'qr_code' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'notes' => 'nullable|string',
        ]);

        $data = $request->all();

        if ($request->hasFile('qr_code')) {
            if ($method->qr_code) {
                Storage::disk('public')->delete($method->qr_code);
            }
            $data['qr_code'] = $request->file('qr_code')->store('payment_methods', 'public');
        }

        $method->update($data);

        return redirect()->back()->with('success', 'Payment method updated successfully.');
    }

    public function destroy($id)
    {
        $method = AdminPaymentMethod::findOrFail($id);
        if ($method->qr_code) {
            Storage::disk('public')->delete($method->qr_code);
        }
        $method->delete();

        return redirect()->back()->with('success', 'Payment method deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $method = AdminPaymentMethod::findOrFail($id);
        $method->status = !$method->status;
        $method->save();

        return response()->json(['success' => true, 'status' => $method->status]);
    }

    public function bankLink()
    {
        return view('dashboard.banks.link');
    }

    public function storeBank(Request $request)
    {
        return $this->store($request);
    }
    public function bankreport()
    {
        $methods = AdminPaymentMethod::all();
        return view('dashboard.banks.bank-table', compact('methods'));
    }

    public function exportBankReport()
    {
        $records = AdminPaymentMethod::all();

        $filename = "bank_report_" . date('Ymd_His') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'Sr. No.',
            'Bank ID',
            'Currency',
            'Country/Region',
            'Vouchers Sold',
            'Sale Value in Local Currency',
            'Refunds',
            'Disputes',
            'Currency Conversion @',
            'Sale Value in Buying Currency',
            'FX Gain/Loss',
            'Created At'
        ];

        $callback = function () use ($records, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($records as $index => $record) {
                fputcsv($file, [
                    $index + 1,
                    $record->bank_name ?? 'BNK-' . ($index + 100),
                    'USD',
                    $record->country ?? 'N/A',
                    0,
                    0.00,
                    0.00,
                    0.00,
                    '1.00',
                    0.00,
                    '0.00',
                    $record->created_at->format('Y-m-d')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
