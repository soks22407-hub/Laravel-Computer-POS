<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    public static array $modules = [
        'dashboard'         => 'Dashboard',
        'products'          => 'Products',
        'orders'            => 'Orders',
        'pos'               => 'Point of Sale',
        'purchase_requests' => 'Purchase Requests',
        'stock_alert'       => 'Stock Alert',
        'tasks'             => 'Tasks',
        'contacts'          => 'Messages',
    ];


    protected function panel(Request $request): string
    {
        return $request->is('ceo/*') ? 'ceo' : 'admin';
    }

    public function index(Request $request)
    {
        $panel = $this->panel($request);

        $employeeCount = User::where('utype', 'EMP')->count();

        $firstEmployeeId = User::where('utype', 'EMP')->orderBy('id')->value('id');

        $existing = $firstEmployeeId
            ? DB::table('permissions')->where('user_id', $firstEmployeeId)->get()->keyBy('module')
            : collect();

        return view("{$panel}.permissions", [
            'panel'         => $panel,
            'modules'       => self::$modules,
            'employeeCount' => $employeeCount,
            'existing'      => $existing,
        ]);
    }

    public function update(Request $request)
    {
        $panel = $this->panel($request);

        $userIds = User::where('utype', 'EMP')->pluck('id');

        if ($userIds->isEmpty()) {
            return redirect()
                ->route("{$panel}.permissions.index")
                ->with('error', 'No employees found to apply permissions to.');
        }

        $permissions = $request->input('permissions', []);

        foreach ($userIds as $userId) {
            foreach (self::$modules as $key => $label) {
                $row = $permissions[$key] ?? [];

                DB::table('permissions')->updateOrInsert(
                    ['user_id' => $userId, 'module' => $key],
                    [
                        'can_view'   => isset($row['view']),
                        'can_add'    => isset($row['add']),
                        'can_edit'   => isset($row['edit']),
                        'can_delete' => isset($row['delete']),
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        }

        return redirect()
            ->route("{$panel}.permissions.index")
            ->with('success', "Permissions applied to all {$userIds->count()} employees.");
    }
}
