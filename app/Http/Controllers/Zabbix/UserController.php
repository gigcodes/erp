<?php

declare(strict_types=1);

namespace App\Http\Controllers\Zabbix;

use App\Zabbix\ZabbixException;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Zabbix\User;

class UserController extends Controller
{
    /**
     * @param Request $request
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $user = new User();

        $users = $user->getAllUsers();
        $roles = $user->getAllRoles();

        $array = [];

        foreach ($roles as $value) {
            $array[(int)$value['roleid']] = $value;
        }

        return view('zabbix.user.index', [
            'users' => $users,
            'roles' => $array
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {
        $data = $request->all();

        try {
            $user = new User();
            $userId = (int)$data['id'] ?? null;
            if (!empty($data['id'])) {
                $user = $user->getById($userId);
            }

            $user->setUsername($data['username'] ?? '');
            $user->setName($data['name'] ?? '');
            $user->setSurname($data['surname'] ?? '');
            $user->setRoleId((int)$data['role_id'] ?? 1);

            if (!$user->getId()) {
                $user->setPassword($data['password'] ?? '');
            }

            $user->save();
        }
        catch (ZabbixException $zabbixException)
        {
            return response()->json([
                'message' => $zabbixException->getMessage(),
                'code' => 500
            ]);
        }
        catch (Exception $e) {
            return response()->json([
                'message' => 'Something went wrong.',
                'code' => 500
            ]);
        }

        return response()->json([
            'message' => sprintf('User with username: %s was edited. Reload page.', $user->getUsername()),
            'user' => $user,
            'code' => 200
        ]);
    }

    /**
     * @param Request $request
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function roles(Request $request)
    {
        $user = new User();

        $roles = $user->getAllRoles();

        return view('zabbix.user.role.index', [
            'role' => $roles
        ]);
    }

    public function delete(Request $request)
    {
        $data = $request->all();

        try {
            $user = new User();
            $userId = (int)$data['id'] ?? null;
            if (!empty($data['id'])) {
                $user = $user->getById($userId);
            } else {
                throw new ZabbixException(sprintf('User with id: %s not found.', $userId));
            }

            $user->delete();
        }
        catch (ZabbixException $zabbixException)
        {
            return response()->json([
                'message' => $zabbixException->getMessage(),
                'code' => 500
            ]);
        }
        catch (Exception $e) {
            return response()->json([
                'message' => 'Something went wrong.',
                'code' => 500
            ]);
        }

        return response()->json([
            'message' => sprintf('User with id: %s was deleted. Reload page.', $userId),
            'code' => 200
        ]);
    }
}