<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\ResponseTrait;

class CustomerController extends Controller
{
    use ResponseTrait;
    public function index(Request $request)
    {
        $data = $request->all();
        $query = Customer::query();
        $perPage = config('constants.per_page');
        if ($request->has('q') && strlen($request->input('q')) > 0 ) {
            $query->where('name', 'LIKE', "%" . $data['q'] . "%")
                ->orWhere('code', 'LIKE', "%" . $data['q'] . "%");
        }
        $customers = $query->orderBy('created_at', 'DESC')->paginate($perPage);

        return $this->responseSuccess($customers);
    }

    public function store(StoreCustomerRequest $request)
    {
        try {
            $customer = new Customer;
            $customer->code = Customer::generateCode();
            $customer->name = $request->input('name');
            if ($request->input('email')) {
                if ($this->isExistEmail($request->input('email'), $customer->_id)) {
                    $error = ['email' => ['Email đã tồn tại !']];
                    return $this->responseError('error', $error, 400);
                }
                $customer->email = $request->input('email');
            }
            if ($this->isExistPhone($request->input('phone'), $customer->_id)) {
                $error = ['phone' => ['Số điện thoại đã tồn tại !']];
                return $this->responseError('error', $error, 400);
            }
            $customer->phone = $request->input('phone');
            $customer->address = $request->input('address');
            $customer->save();
            return $this->responseSuccess();

        } catch (Exception $e) {
            DB::rollback();
            Log::error('Error store customer', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);

            return $this->responseError();
        }
    }

    public function update(UpdateCustomerRequest $request, $id)
    {
        try {
            $customer = Customer::find($id);

            if ($customer) {
                $customer->name = $request->input('name');
                if ($request->input('email')) {
                    if ($this->isExistEmail($request->input('email'), $id)) {
                        $error = ['email' => ['Email đã tồn tại !']];
                        return $this->responseError('error', $error, 400);
                    }
                    $customer->email = $request->input('email');
                }
                if ($this->isExistPhone($request->input('phone'), $id)) {
                    $error = ['phone' => ['Số điện thoại đã tồn tại !']];
                    return $this->responseError('error', $error, 400);
                }
                $customer->phone = $request->input('phone');
                $customer->address = $request->input('address');
                $customer->save();
            } else {
                return $this->responseError('Chỉnh sửa thất bại',[],400);
            }

            return $this->responseSuccess();
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Error update customer', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);

            return $this->responseError();
        }
    }

    private function isExistPhone($phone, $customer_id)
    {
        $count = Customer::where('phone', $phone)->where('_id', '<>', $customer_id)->count();
        return $count > 0;
    }

    private function isExistEmail($email, $customer_id)
    {
        $count = Customer::where('email', $email)->where('_id', '<>', $customer_id)->count();
        return $count > 0;
    }

    public function destroy($id)
    {
        Customer::destroy($id);

        return $this->responseSuccess();
    }
}
