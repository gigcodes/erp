<?php

namespace Modules\BookStack\Auth;

use Images;
use Activity;
use Exception;
use Modules\BookStack\Uploads\Image;
use Illuminate\Database\Eloquent\Builder;
use Modules\BookStack\Entities\Repos\EntityRepo;
use Modules\BookStack\Exceptions\NotFoundException;
use Modules\BookStack\Exceptions\UserUpdateException;

class UserRepo
{
    protected $user;

    protected $role;

    protected $entityRepo;

    /**
     * UserRepo constructor.
     */
    public function __construct(User $user, Role $role, EntityRepo $entityRepo)
    {
        $this->user       = $user;
        $this->role       = $role;
        $this->entityRepo = $entityRepo;
    }

    /**
     * @param string $email
     *
     * @return User|null
     */
    public function getByEmail($email)
    {
        return $this->user->where('email', '=', $email)->first();
    }

    /**
     * @param int $id
     *
     * @return User
     */
    public function getById($id)
    {
        return $this->user->newQuery()->findOrFail($id);
    }

    /**
     * Get all the users with their permissions.
     *
     * @return Builder|static
     */
    public function getAllUsers()
    {
        return $this->user->with('roles', 'avatar')->orderBy('name', 'asc')->get();
    }

    /**
     * Get all the users with their permissions in a paginated format.
     *
     * @param int   $count
     * @param mixed $sortData
     *
     * @return Builder|static
     */
    public function getAllUsersPaginatedAndSorted($count, $sortData)
    {
        $query = $this->user->with('roles', 'avatar')->orderBy($sortData['sort'], $sortData['order']);

        if ($sortData['search']) {
            $term = '%' . $sortData['search'] . '%';
            $query->where(function ($query) use ($term) {
                $query->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term);
            });
        }

        return $query->paginate($count);
    }

    /**
     * Creates a new user and attaches a role to them.
     *
     * @param bool $verifyEmail
     *
     * @return \BookStack\Auth\User
     */
    public function registerNew(array $data, $verifyEmail = false)
    {
        $user = $this->create($data, $verifyEmail);
        $this->attachDefaultRole($user);
        $this->downloadAndAssignUserAvatar($user);

        return $user;
    }

    /**
     * Give a user the default role. Used when creating a new user.
     */
    public function attachDefaultRole(User $user)
    {
        $roleId = setting('registration-role');
        if ($roleId !== false && $user->roles()->where('id', '=', $roleId)->count() === 0) {
            $user->attachRoleId($roleId);
        }
    }

    /**
     * Assign a user to a system-level role.
     *
     *
     * @param mixed $systemRoleName
     *
     * @throws NotFoundException
     */
    public function attachSystemRole(User $user, $systemRoleName)
    {
        $role = $this->role->newQuery()->where('name', '=', $systemRoleName)->first();
        if ($role === null) {
            throw new NotFoundException("Role '{$systemRoleName}' not found");
        }
        $user->attachRole($role);
    }

    /**
     * Checks if the give user is the only admin.
     *
     * @param \BookStack\Auth\User $user
     *
     * @return bool
     */
    public function isOnlyAdmin(User $user)
    {
        if (! $user->hasSystemRole('admin')) {
            return false;
        }

        $adminRole = $this->role->getSystemRole('admin');
        if ($adminRole->users->count() > 1) {
            return false;
        }

        return true;
    }

    /**
     * Set the assigned user roles via an array of role IDs.
     *
     *
     * @throws UserUpdateException
     */
    public function setUserRoles(User $user, array $roles)
    {
        if ($this->demotingLastAdmin($user, $roles)) {
            throw new UserUpdateException(trans('errors.role_cannot_remove_only_admin'), $user->getEditUrl());
        }

        $user->roles()->sync($roles);
    }

    /**
     * Check if the given user is the last admin and their new roles no longer
     * contains the admin role.
     */
    protected function demotingLastAdmin(User $user, array $newRoles): bool
    {
        if ($this->isOnlyAdmin($user)) {
            $adminRole = $this->role->getSystemRole('admin');
            if (! in_array(strval($adminRole->id), $newRoles)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Create a new basic instance of user.
     *
     * @param bool $verifyEmail
     *
     * @return \BookStack\Auth\User
     */
    public function create(array $data, $verifyEmail = false)
    {
        return $this->user->forceCreate([
            'name'            => $data['name'],
            'email'           => $data['email'],
            'password'        => bcrypt($data['password']),
            'email_confirmed' => $verifyEmail,
        ]);
    }

    /**
     * Remove the given user from storage, Delete all related content.
     *
     * @param \BookStack\Auth\User $user
     *
     * @throws Exception
     */
    public function destroy(User $user)
    {
        $user->socialAccounts()->delete();
        $user->delete();

        // Delete user profile images
        $profileImages = Image::where('type', '=', 'user')->where('uploaded_to', '=', $user->id)->get();
        foreach ($profileImages as $image) {
            Images::destroy($image);
        }
    }

    /**
     * Get the latest activity for a user.
     *
     * @param \BookStack\Auth\User $user
     * @param int                  $count
     * @param int                  $page
     *
     * @return array
     */
    public function getActivity(User $user, $count = 20, $page = 0)
    {
        return Activity::userActivity($user, $count, $page);
    }

    /**
     * Get the recently created content for this given user.
     *
     * @param \BookStack\Auth\User $user
     * @param int                  $count
     *
     * @return mixed
     */
    public function getRecentlyCreated(User $user, $count = 20)
    {
        $createdByUserQuery = function (Builder $query) use ($user) {
            $query->where('created_by', '=', $user->id);
        };

        return [
            'pages'    => $this->entityRepo->getRecentlyCreated('page', $count, 0, $createdByUserQuery),
            'chapters' => $this->entityRepo->getRecentlyCreated('chapter', $count, 0, $createdByUserQuery),
            'books'    => $this->entityRepo->getRecentlyCreated('book', $count, 0, $createdByUserQuery),
            'shelves'  => $this->entityRepo->getRecentlyCreated('bookshelf', $count, 0, $createdByUserQuery),
        ];
    }

    /**
     * Get asset created counts for the give user.
     *
     * @param \BookStack\Auth\User $user
     *
     * @return array
     */
    public function getAssetCounts(User $user)
    {
        return [
            'pages'    => $this->entityRepo->getUserTotalCreated('page', $user),
            'chapters' => $this->entityRepo->getUserTotalCreated('chapter', $user),
            'books'    => $this->entityRepo->getUserTotalCreated('book', $user),
            'shelves'  => $this->entityRepo->getUserTotalCreated('bookshelf', $user),
        ];
    }

    /**
     * Get the roles in the system that are assignable to a user.
     *
     * @return mixed
     */
    public function getAllRoles()
    {
        return $this->role->newQuery()->orderBy('name', 'asc')->get();
    }

    /**
     * Get all the roles which can be given restricted access to
     * other entities in the system.
     *
     * @return mixed
     */
    public function getRestrictableRoles()
    {
        return $this->role->where('name', '!=', 'admin')->get();
    }

    /**
     * Get an avatar image for a user and set it as their avatar.
     * Returns early if avatars disabled or not set in config.
     *
     * @return bool
     */
    public function downloadAndAssignUserAvatar(User $user)
    {
        if (! Images::avatarFetchEnabled()) {
            return false;
        }

        try {
            $avatar = Images::saveUserAvatar($user);
            $user->avatar()->associate($avatar);
            $user->save();

            return true;
        } catch (Exception $e) {
            \Log::error('Failed to save user avatar image');

            return false;
        }
    }
}
