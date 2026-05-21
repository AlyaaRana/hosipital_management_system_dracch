<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\User;
use App\Http\Resources\v1\PatientResource;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PatientController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $patients = Patient::with('user')->paginate(10);

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar pasien berhasil diambil',
            'data' => PatientResource::collection($patients)->items(),
            'meta' => [
                'current_page' => $patients->currentPage(),
                'per_page' => $patients->perPage(),
                'total' => $patients->total(),
                'last_page' => $patients->lastPage(),
                'next_page_url' => $patients->nextPageUrl(),
                'prev_page_url' => $patients->previousPageUrl(),
            ]
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'date_of_birth' => 'required|date',
            'address' => 'required|string',
            'phone' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'patient',
            ]);

            $patient = Patient::create([
                'user_id' => $user->id,
                'date_of_birth' => $request->date_of_birth,
                'address' => $request->address,
                'phone' => $request->phone,
            ]);

            DB::commit();
            return $this->success(new PatientResource($patient), 'Pasien berhasil ditambahkan', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Gagal menambah pasien: ' . $e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);
        $user = $patient->user;
        $currentUser = Auth::user();

        if ($currentUser->role !== 'admin' && $currentUser->patient?->id !== $patient->id) {
            return response()->json(['status' => 'error', 'message' => 'Access Denied'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'required|min:8',
            'date_of_birth' => 'required|date',
            'address' => 'required|string',
            'phone' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $user->update($request->only(['name', 'email']));

            $patient->update($request->only(['date_of_birth', 'address', 'phone']));

            DB::commit();
            return $this->success(new PatientResource($patient), 'Data pasien berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Gagal update pasien', 500);
        }
    }

    public function show($id)
    {
        $patient = Patient::with('user')->findOrFail($id);
        $currentUser = Auth::user();

        if ($currentUser->role !== 'admin' && $currentUser->patient?->id !== $patient->id) {
            return response()->json(['status' => 'error', 'message' => 'Access Denied'], 403);
        }

        return $this->success(new PatientResource($patient), 'Detail pasien ditemukan');
    }

    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);
        $patient->user->delete();
        return $this->success(null, 'Data pasien berhasil dihapus');
    }
}
