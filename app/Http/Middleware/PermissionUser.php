<?php

namespace App\Http\Middleware;

use App\Models\Permission;
use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PermissionUser
{
    use ResponseTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $namePermission)
    {
        try {
            $user = Auth::user();
            $role = Role::find($user->role_id);
            if ($role) {
                $isSuperAdmin = $this->hasPermission($role, 'admin');
                if ($isSuperAdmin) {
                    return $next($request);
                }
                
                $isPermission = $this->hasPermission($role, $namePermission);
                if ($isPermission) {
                    return $next($request);
                }
            }
            return $this->responseError(
                'Bạn không có quyền truy cập vào chức năng này!', 
                [],  
                Response::HTTP_FORBIDDEN,
                403
            );

        } catch (Exception $e) {
            Log::error('Error middleware permission for user', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);

            return $this->responseError();
        }
    }

    private function hasPermission($role, $namePermission)
    {
        $idPermission = Permission::where('name', $namePermission)->first();
        $idPermission = in_array($idPermission->_id, $role['permission_ids']);
        return $idPermission;
    }
}
