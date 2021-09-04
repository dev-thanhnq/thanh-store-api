<?php

namespace App\Http\Controllers;

use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Models\Role;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    use ResponseTrait;
    
    public function index(Request $request)
    {
        $query = Role::query();
        if ($request->has('q') && strlen($request->input('q')) > 0) {
            $query->where('name', 'LIKE', "%" . $request->input('q'). "%" );
        }
        $roles = $query->orderBy('created_at', 'DESC')->paginate(config('constants.per_page'));

        return $this->responseSuccess($roles);
    }

    public function store(StoreRoleRequest $request)
    {
        try {
            $role = new Role();
            $role->name = $request->input('name');
            $role->is_protected = false;
            $role->description = $request->input('description');
            $role->save();

            return $this->responseSuccess();

        } catch(Exception $e) {
            Log::error('Error store role', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);

            return $this->responseError();
        }
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        try {
            $role = Role::find($id);
           
            if ($role) {
                if ($this->isExistRole($request->input('name'), $id)) {
                    $error = ['name' => ['Chức vụ đã tồn tại.']];
                    return $this->responseError('error', $error, 400);
                }
                $role->name = $request->input('name');
                $role->description = $request->input('description');

                $role->save();
            } else {
                return $this->responseError('Không có chức vụ này!', [], 404);
            }

            return $this->responseSuccess();

        } catch (Exception $e) {
            Log::error('Error update role', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);

            return $this->responseError();
        }
    }

    public function show($id)
    {
        $role = Role::find($id);
        return $this->responseSuccess($role);
    }

    private function isExistRole($name, $id)
    {
        $count = Role::where('name', $name)
            ->where('_id', '<>', $id)
            ->count();
        return $count > 0;
    }

    public function destroy($id)
    {
        Role::destroy($id);
        return $this->responseSuccess();
    }

    public function addPermissionForRole(Request $request, $id)
    {
        try {
            $role = Role::find($id);
            if ($role) {
                if ($request->has('permission_id')) {
                    $permissionIds = $this->getPermission($request->input('permission_id'));
                    $role->permissions()->attach($permissionIds);
                    $role->save();
                } else {
                    return $this->responseError('Quyền này không tồn tại!', [], 404);
                }
            } else {
                return $this->responseError('Không có chức vụ này!', [], 404);
            }

            return $this->responseSuccess();
        } catch (Exception $e) {
            Log::error('Error add permission for role', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);

            return $this->responseError();
        }
    }

    private function getPermission ($idPermission)
    {
        $permissionIds = [];
        $permission = Permission::find($idPermission);
        $permissionGroup = PermissionGroup::find($idPermission);
        if (!$permission && !$permissionGroup) {
            return $this->responseError('Quyền này không tồn tại!', [], 404);
        }
        if ($permissionGroup) {
            $permissions = Permission::where('group_code', $permissionGroup->code)->get(['_id'])->toArray();
            $permissionIds = Arr::flatten($permissions);
        }
        if ($permission) {
            array_push($permissionIds, $permission->_id);
        }
        return $permissionIds;
    }

    public function removePermissionForRole(Request $request ,$id)
    {
        try {
            $role = Role::findOrFail($id);
            if ($role) {
                if ($request->has('permission_id')) {
                    $permissionIds = $this->getPermission($request->input('permission_id'));
                    $role->permissions()->detach($permissionIds);
                    $role->save();
                } else {
                    return $this->responseError('Quyền này không tồn tại!', [], 404);
                }
            } else {
                return $this->responseError('Không có chức vụ này!', [], 404);
            }

            return $this->responseSuccess();
        } catch (Exception $e) {
            Log::error('Error remove permission for role', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);

            return $this->responseError();
        }
    }
}
