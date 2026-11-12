<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(): View
    {
        $suppliers = Supplier::all();

        return view('suppliers.index', ['suppliers' => $suppliers]);
    }

    public function searchSupplierByName(Request $request): View {
        Validator::make($request->all(),
        [
            'supplier_name' => 'required|string',
        ],
        [
            'supplier_name.required' => 'The supplier name is required.'
        ])->validate();

        $suppliers = Supplier::where('supplier_name', 'LIKE', "%$request->supplier_name%")
                            ->get();

        return view('suppliers.index', ['suppliers' => $suppliers]);
    }

    public function create(): View
    {
        return view('suppliers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Validator::make($request->all(),
        [
            'supplier_name' => 'required|string',
            'contact_person' => 'required|string',
            'email' => 'required|email|unique:suppliers,email',
            'phone' => 'required|string',
        ],
        [
            'supplier_name.required' => 'The supplier name is required.',
            'contact_person.required' => 'The contact person is required.',
            'email.required' => 'The email is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email already exists.',
            'phone.required' => 'The phone number is required.',
            'phone.unique' => 'The phone number already exists.',
        ])->validate();

        try {
            $supplier = new Supplier();
            $supplier->supplier_name = $request->supplier_name;
            $supplier->contact_person = $request->contact_person;
            $supplier->email = $request->email;
            $supplier->phone = $request->phone;
            $supplier->save();

            return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully.');

        } catch (Exception $ex) {
            Log::error($ex);
            return redirect()->back()->withErrors(['error' => 'An error has occurred.']);
        }
    }

    public function edit(string $id): View | RedirectResponse
    {
        $supplier = Supplier::find($id);

        if (empty($supplier)) {
            return redirect()->back()->withErrors(['error' => "Supplier with id '$id' doesn't exist."]);
        }

        return view('suppliers.edit', ['supplier' => $supplier]);
    }

    public function update(Request $request): RedirectResponse
    {
        Validator::make($request->all(),
        [
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'supplier_name' => 'required|string',
            'contact_person' => 'required|string',
            'email' => 'required|email|unique:suppliers,email,' . $request->supplier_id . ',supplier_id',
            'phone' => 'required|string|unique:suppliers,phone,' . $request->supplier_id . ',supplier_id',
        ],
        [
            'supplier_id.required' => 'The supplier is required.',
            'supplier_id.exists' => 'The given id does not exist.',
            'supplier_name.required' => 'The supplier name is required.',
            'contact_person.required' => 'The contact person is required.',
            'email.required' => 'The email is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email already exists.',
            'phone.required' => 'The phone number is required.',
            'phone.unique' => 'The phone number already exists.',
        ])->validate();

        try {
            $supplier = Supplier::find($request->supplier_id);
            $supplier->supplier_name = $request->supplier_name;
            $supplier->contact_person = $request->contact_person;
            $supplier->email = $request->email;
            $supplier->phone = $request->phone;
            $supplier->save();

            return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');

        } catch (Exception $ex) {
            Log::error($ex);
            return redirect()->back()->withErrors(['error' => 'An error has occurred.']);
        }
    }

    public function delete(string $id): RedirectResponse
    {
        try {
            $supplier = Supplier::find($id);

            if (empty($supplier)) {
                return redirect()->back()->withErrors(['error' => "Supplier with id '$id' doesn't exist."]);
            }

            $supplier->delete();

            return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');

        } catch (Exception $ex) {
            Log::error($ex);
            return redirect()->back()->withErrors(['error' => 'An error has occurred.']);
        }
    }
}
