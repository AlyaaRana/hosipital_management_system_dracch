<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\User;
use App\Http\Resources\v1\PatientResource;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PatientController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $patients = Patient::with('user')->paginate(10);
        $resource = PatientResource::collection($patients);
        $data = array_merge(
            ['items' => $resource->toArray(request())],
            $resource->response()->getData(true)
        );
        
        unset($data['data']);
        return $this->success($data, 'Daftar pasien berhasil diambil');
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

            // Simpan ke tabel users
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'patient',
            ]);

            // Simpan ke tabel patients
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

            // Update tabel users
            $user->update($request->only(['name', 'email']));
            
            // Update tabel patients
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
        return $this->success(new PatientResource($patient), 'Detail pasien ditemukan');
    }

    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);
        $patient->user->delete();
        return $this->success(null, 'Data pasien berhasil dihapus');
    }
}