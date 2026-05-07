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

    // 1. READ List dengan Pagination
    public function index()
    {
        $doctors = Doctor::with('user')->paginate(10);    
        $resource = DoctorResource::collection($doctors);
        
        $data = array_merge(
            ['items' => $resource->toArray(request())],
            $resource->response()->getData(true)
        );

        return $this->success($data, 'Daftar dokter berhasil diambil');
    }

    // 2. CREATE Dokter Baru
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

            // Buat User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'doctor',
            ]);

            // Buat Detail Dokter
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

    // 3. READ Detail Satu Dokter
    public function show($id)
    {
        $doctor = Doctor::with('user')->findOrFail($id);
        return $this->success(new DoctorResource($doctor), 'Detail dokter berhasil diambil');
    }

    // 4. UPDATE Dokter
    public function update(Request $request, $id)
    {
        $doctor = Doctor::findOrFail($id);
        $user = $doctor->user;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8', // Password jadi opsional saat update
            'specialization' => 'required',
            'phone' => 'required'
        ]);

        try {
            DB::beginTransaction();

            // Update User
            $userData = $request->only(['name', 'email']);
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $user->update($userData);

            // Update Detail Dokter
            $doctor->update($request->only(['specialization', 'phone']));

            DB::commit();
            return $this->success(new DoctorResource($doctor), 'Data dokter berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Gagal update dokter: ' . $e->getMessage(), 500);
        }
    }

    // 5. DELETE Dokter
    public function destroy($id)
    {
        $doctor = Doctor::findOrFail($id);
        
        try {
            // Hapus User-nya, tabel doctor akan ikut terhapus karena Cascade Delete
            $doctor->user->delete();
            return $this->success(null, 'Dokter berhasil dihapus');
        } catch (\Exception $e) {
            return $this->error('Gagal menghapus dokter', 500);
        }
    }
}