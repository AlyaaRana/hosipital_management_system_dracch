<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\User;
use App\Http\Resources\v1\DoctorResource;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DoctorController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $doctors = Doctor::with(['user', 'schedules'])->paginate(10);
        $resource = DoctorResource::collection($doctors);

        $data = [
            'items' => $resource->toArray($request),
            'meta' => [
                'current_page' => $doctors->currentPage(),
                'per_page' => $doctors->perPage(),
                'total' => $doctors->total(),
                'last_page' => $doctors->lastPage(),
                'next_page_url' => $doctors->nextPageUrl(),
                'prev_page_url' => $doctors->previousPageUrl(),
            ],
        ];

        return $this->success($data, 'Daftar dokter berhasil diambil');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'specialization' => 'required',
            'phone' => 'required'
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'doctor',
            ]);

            $doctor = Doctor::create([
                'user_id' => $user->id,
                'specialization' => $request->specialization,
                'phone' => $request->phone,
            ]);

            DB::commit();
            return $this->success(new DoctorResource($doctor), 'Dokter berhasil ditambahkan', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Gagal menambah dokter: ' . $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        $doctor = Doctor::with('user')->findOrFail($id);
        return $this->success(new DoctorResource($doctor), 'Detail dokter berhasil diambil');
    }

    public function update(Request $request, $id)
    {
        $doctor = Doctor::findOrFail($id);
        $user = $doctor->user;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8',
            'specialization' => 'required',
            'phone' => 'required'
        ]);

        try {
            DB::beginTransaction();

            $userData = $request->only(['name', 'email']);
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $user->update($userData);

            $doctor->update($request->only(['specialization', 'phone']));

            DB::commit();
            return $this->success(new DoctorResource($doctor), 'Data dokter berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Gagal update dokter: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        $doctor = Doctor::findOrFail($id);

        try {
            $doctor->user->delete();
            return $this->success(null, 'Dokter berhasil dihapus');
        } catch (\Exception $e) {
            return $this->error('Gagal menghapus dokter', 500);
        }
    }
}
