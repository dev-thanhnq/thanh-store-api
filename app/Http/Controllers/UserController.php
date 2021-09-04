<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\ChangeProfileRequest;
use App\Http\Requests\User\ResetPasswordRequest;
use App\Http\Requests\User\StoreStaffRequest;
use App\Http\Requests\User\UpdateStaffRequest;
use App\Models\Role;
use App\Models\User;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    use ResponseTrait;
    public function getDataProfile()
    {
        $user = User::find(auth()->id());

        return $this->responseSuccess($user);
    }

    public function listStaff(Request $request)
    {
        $data = $request->all();
        $query = User::query();
        if ($request->has('q') && strlen($request->input('q')) > 0 ) {
            $query->where('name', 'LIKE', "%" . $data['q'] . "%");
        }
        $users = $query->orderBy('created_at', 'DESC')->paginate(config('constants.per_page'));

        return $this->responseSuccess($users);
    }

    public function createStaff(StoreStaffRequest $request)
    {
        try {
            if (Auth::user()->role_id === User::ROLE_ID['admin']) {
                $user = new User;
                $user->name = $request->input('name');
                $user->email = $request->input('email');
                $user->phone = $request->input('phone');
                $user->address = $request->input('address');
                $user->password = Hash::make($request->input('password'));
                $user->role_id = (int)$request->input('role_id');
                $user->save();

                return $this->responseSuccess();
            }

            return $this->responseError('Unauthorized', [], 401);

        } catch (Exception $e) {
            DB::rollback();
            Log::error('Error store staff', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);

            return $this->responseError();
        }

    }

    public function updateStaff(UpdateStaffRequest $request, $id)
    {
        try {
            if (Auth::user()->role_id === User::ROLE_ID['admin']) {
                $user = User::find($id);
                if ($user) {
                    if ($request->has('phone')) {
                        if ($this->isExistPhone($request->input('phone'), $id)) {
                            $error = ['phone' => ['Số điện thoại đã tồn tại.']];
                            return $this->responseError('error', $error, 400);
                        }
                        $user->phone = $request->input('phone');
                    }
                    if ($request->has('email')) {
                        if ($this->isExistEmail($request->input('email'), $id)) {
                            $error = ['email' => ['Email đã tồn tại.']];
                            return $this->responseError('error', $error, 400);
                        }
                        $user->email = $request->input('email');
                    }
                    $user->name = $request->input('name');
                    $user->address = $request->input('address');
                    if ($request->has('role_id')) {
                        $user->role_id = $request->input('role_id');
                    }
                    $user->save();

                } else {
                    return $this->responseError('User not found', [], 404);
                }

            } else {
                return $this->responseError('Unauthorized', [], 401);
            }

            return $this->responseSuccess();

        } catch (Exception $e) {
            DB::rollback();
            Log::error('Error update staff', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);

            return $this->responseError();
        }

    }

    public function changeProfile(ChangeProfileRequest $request)
    {
        try {
            $user = User::find(auth()->id());
            $user->name = $request->input('name');
            $user->phone = $request->input('phone');
            $user->email = $request->input('email');
            $user->address = $request->input('address');
            $user->save();

            return $this->responseSuccess();

        } catch (Exception $e) {
            DB::rollback();
            Log::error('Error change profile', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);

            return $this->responseError();
        }

    }

    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $user = User::find(auth()->id());
            if ($user) {
                $credentials = [
                    'phone' => $user->phone,
                    'password' => $request->input('password'),
                ];

                if (!auth()->attempt($credentials)) {
                    $error = ['password' => ['Mật khẩu hiện tại không chính xác']];
                    return $this->responseError('error', $error, 400);
                }

                if ($request->input('new_password') === $request->input('password')) {
                    $error = ['password' => ['Mật khẩu mới phải khác mật khẩu hiện tại']];
                    return $this->responseError('error', $error, 400);
                }

                $user->password = Hash::make($request->input('new_password'));
                $user->save();
            }

            return $this->responseSuccess();

        } catch (Exception $e) {
            Log::error('Error change password', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);

            return $this->responseError();
        }
    }

    public function destroy($id)
    {
        User::destroy($id);

        return $this->responseSuccess();
    }

    private function isExistPhone($phone, $userId)
    {
        $count = User::where('phone', $phone)->where('_id', '<>', $userId)->count();
        return $count > 0;
    }

    private function isExistEmail($email, $userId)
    {
        $count = User::where('email', $email)->where('_id', '<>', $userId)->count();
        return $count > 0;
    }

    public function resetPassword(ResetPasswordRequest $request, $id)
    {
        try {
            if (Auth::user()->role_id === User::ROLE_ID['admin']) {
                $user = User::find($id);
                if ($user) {
                    $user->password = Hash::make($request->input('password'));
                    $user->save();
                } else {
                    return $this->responseError('User not found', [], 404);
                }

            } else {
                return $this->responseError('Unauthorized', [], 401);
            }

            return $this->responseSuccess();

        } catch (Exception $e) {
            DB::rollback();
            Log::error('Error reset password', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);

            return $this->responseError();
        }
    }
}
