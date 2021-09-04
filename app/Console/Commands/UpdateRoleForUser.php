<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateRoleForUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:role-for-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update role id for user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $roleAdmin = Role::where('name', 'Admin')->first();
            $roleUser = Role::where('name', 'Nhân viên')->first();
            $admins = User::where('role_id', 1)->get();
            $users = User::where('role_id', 0)->get();
            foreach ($admins as $admin) {
                $admin->role_id = $roleAdmin->id;
                $admin->save();
            }
            foreach ($users as $user) {
                $user->role_id = $roleUser->id;
                $user->save();
            }
        } catch (\Exception $exception) {
            Log::error('Error update role_id for user', [
                'method' => __METHOD__,
                'message' => $exception->getMessage()
            ]);
        }
    }
}
