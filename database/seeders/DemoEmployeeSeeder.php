<?php

namespace Database\Seeders;

use App\Enums\EmploymentStatus;
use App\Enums\Gender;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoEmployeeSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Akun Admin (Staff IT Desa) dengan Email pemdesneglasarijtw@gmail.com
        $itPosition = Position::where('code', 'STAF_IT')->first();
        $adminEmployee = Employee::updateOrCreate(['employee_number' => 'EMP-ADMIN01'], [
            'nik' => '3206000000000001',
            'full_name' => 'Administrator IT Desa',
            'gender' => Gender::MALE,
            'position_id' => $itPosition ? $itPosition->id : null,
            'employment_status' => EmploymentStatus::PERMANENT,
            'phone' => '081234567890',
            'email' => 'pemdesneglasarijtw@gmail.com',
            'joined_at' => now()->subYears(3),
            'is_active' => true,
        ]);

        $adminUser = User::updateOrCreate(['username' => 'admin'], [
            'employee_id' => $adminEmployee->id,
            'name' => 'Administrator IT Desa',
            'email' => 'pemdesneglasarijtw@gmail.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);
        $adminUser->syncRoles(['Admin', 'Super Admin', 'Admin Desa']);

        // Akun superadmin tambahan dengan email yang sama/opsional
        $superAdminUser = User::updateOrCreate(['username' => 'superadmin'], [
            'employee_id' => $adminEmployee->id,
            'name' => 'Super Administrator',
            'email' => 'superadmin@neglasari.desa.id',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);
        $superAdminUser->syncRoles(['Admin', 'Super Admin']);

        // 2. Data Perangkat Desa dari PDF "Data Perangkat Desa Periode 22-29.pdf"
        $perangkatDesa = [
            [
                'username' => 'nandang',
                'emp_no' => '141.1/Kep.123-Pemdes/2021',
                'name' => 'NANDANG',
                'gender' => Gender::MALE,
                'birth_place' => 'SALOPA',
                'birth_date' => '1973-02-04',
                'pos_code' => 'KADES',
                'nik' => '3206190402730001',
            ],
            [
                'username' => 'yayan',
                'emp_no' => '19850207061920040013',
                'name' => 'YAYAN RADIAN, S.Pd.I',
                'gender' => Gender::MALE,
                'birth_place' => 'TASIKMALAYA',
                'birth_date' => '1985-02-07',
                'pos_code' => 'SEKDES',
                'nik' => '3206190702850013',
            ],
            [
                'username' => 'darso',
                'emp_no' => '19670520061920040001',
                'name' => 'DARSO RACHMAN',
                'gender' => Gender::MALE,
                'birth_place' => 'TASIKMALAYA',
                'birth_date' => '1967-05-20',
                'pos_code' => 'KAUR_TU',
                'nik' => '3206192005670001',
            ],
            [
                'username' => 'badru',
                'emp_no' => '19830310061920040002',
                'name' => 'BADRU TAMAM, S.Pd',
                'gender' => Gender::MALE,
                'birth_place' => 'BOGOR',
                'birth_date' => '1983-03-10',
                'pos_code' => 'KAUR_PER',
                'nik' => '3206191003830002',
            ],
            [
                'username' => 'ade',
                'emp_no' => '19870103061920050013',
                'name' => 'ADE JAMIAT, SE',
                'gender' => Gender::MALE,
                'birth_place' => 'TASIKMALAYA',
                'birth_date' => '1987-01-03',
                'pos_code' => 'KAUR_KEU',
                'nik' => '3206190301870013',
            ],
            [
                'username' => 'mohamad',
                'emp_no' => '19870205061920040006',
                'name' => 'MOHAMAD RAHMAT HIDAYAT',
                'gender' => Gender::MALE,
                'birth_place' => 'TASIKMALAYA',
                'birth_date' => '1987-02-05',
                'pos_code' => 'KASI_PEM',
                'nik' => '3206190502870006',
            ],
            [
                'username' => 'sri',
                'emp_no' => '19810814061920040005',
                'name' => 'SRI RAHAYU YAYU',
                'gender' => Gender::FEMALE,
                'birth_place' => 'TASIKMALAYA',
                'birth_date' => '1981-08-14',
                'pos_code' => 'KASI_PEL',
                'nik' => '3206191408810005',
            ],
            [
                'username' => 'ica',
                'emp_no' => '19950813061920040007',
                'name' => 'ICA RODIATULLOH, SH',
                'gender' => Gender::FEMALE,
                'birth_place' => 'TASIKMALAYA',
                'birth_date' => '1995-08-13',
                'pos_code' => 'KASI_KES',
                'nik' => '3206191308950007',
            ],
            [
                'username' => 'aip',
                'emp_no' => '19740125061920040008',
                'name' => 'AIP',
                'gender' => Gender::MALE,
                'birth_place' => 'BANDUNG',
                'birth_date' => '1974-01-25',
                'pos_code' => 'KADUS_NANGELA',
                'nik' => '3206192501740008',
            ],
            [
                'username' => 'herti',
                'emp_no' => '19760402061920040004',
                'name' => 'HERTI APRIANI, A.md',
                'gender' => Gender::FEMALE,
                'birth_place' => 'TASIKMALAYA',
                'birth_date' => '1976-04-02',
                'pos_code' => 'KADUS_GARUNGGANG',
                'nik' => '3206190204760004',
            ],
            [
                'username' => 'tatang',
                'emp_no' => '19691015061920040010',
                'name' => 'TATANG',
                'gender' => Gender::MALE,
                'birth_place' => 'TASIKMALAYA',
                'birth_date' => '1969-10-15',
                'pos_code' => 'KADUS_SANGGARIANG',
                'nik' => '3206191510690010',
            ],
            [
                'username' => 'yusup',
                'emp_no' => '19860710061920040012',
                'name' => 'YUSUP ABDILLAH, S.Pd.I',
                'gender' => Gender::MALE,
                'birth_place' => 'TASIKMALAYA',
                'birth_date' => '1986-07-10',
                'pos_code' => 'KADUS_KARANGSARI',
                'nik' => '3206191007860012',
            ],
            [
                'username' => 'gugun',
                'emp_no' => '141.3/SK-018/Desa/2021',
                'name' => 'GUGUN GUMILAR, S.Pd',
                'gender' => Gender::MALE,
                'birth_place' => 'TASIKMALAYA',
                'birth_date' => '1998-06-06',
                'pos_code' => 'STAF_PEMDES',
                'nik' => '3206190606980001',
            ],
            [
                'username' => 'ai',
                'emp_no' => '141.3/SK-019/Desa/2021',
                'name' => 'AI HERJANAH',
                'gender' => Gender::FEMALE,
                'birth_place' => 'SALOPA',
                'birth_date' => '1972-08-26',
                'pos_code' => 'STAF_PEMDES',
                'nik' => '3206192608720002',
            ],
        ];

        foreach ($perangkatDesa as $item) {
            $position = Position::where('code', $item['pos_code'])->first();

            $employee = Employee::updateOrCreate(['employee_number' => $item['emp_no']], [
                'nik' => $item['nik'],
                'full_name' => $item['name'],
                'gender' => $item['gender'],
                'birth_place' => $item['birth_place'],
                'birth_date' => $item['birth_date'],
                'position_id' => $position ? $position->id : null,
                'employment_status' => EmploymentStatus::PERMANENT,
                'joined_at' => now()->subYears(2),
                'is_active' => true,
            ]);

            $user = User::updateOrCreate(['username' => $item['username']], [
                'employee_id' => $employee->id,
                'name' => $employee->full_name,
                'email' => $item['username'] . '@neglasari.desa.id',
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]);

            $rolesToAssign = ['Anggota', 'Pegawai'];
            if ($item['pos_code'] === 'KADES') {
                $rolesToAssign[] = 'Kepala Desa';
            }
            $user->syncRoles($rolesToAssign);
        }
    }
}
