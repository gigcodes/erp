<?php

namespace Modules\BookStack\Actions;

use Session;
use Modules\BookStack\Entities\Entity;
use Modules\BookStack\Auth\Permissions\PermissionService;

class ActivityService
{
    protected $activity;

    protected $user;

    protected $permissionService;

    /**
     * ActivityService constructor.
     *
     * @param \BookStack\Actions\Activity $activity
     */
    public function __construct(Activity $activity, PermissionService $permissionService)
    {
        $this->activity          = $activity;
        $this->permissionService = $permissionService;
        $this->user              = user();
    }

    /**
     * Add activity data to database.
     *
     * @param int   $bookId
     * @param bool  $extra
     * @param mixed $activityKey
     */
    public function add(Entity $entity, $activityKey, $bookId = 0, $extra = false)
    {
        $activity          = $this->activity->newInstance();
        $activity->user_id = $this->user->id;
        $activity->book_id = $bookId;
        $activity->key     = strtolower($activityKey);
        if ($extra !== false) {
            $activity->extra = $extra;
        }
        $entity->activity()->save($activity);
        $this->setNotification($activityKey);
    }

    /**
     * Adds a activity history with a message & without binding to a entity.
     *
     * @param int        $bookId
     * @param bool|false $extra
     * @param mixed      $activityKey
     */
    public function addMessage($activityKey, $bookId = 0, $extra = false)
    {
        $this->activity->user_id = $this->user->id;
        $this->activity->book_id = $bookId;
        $this->activity->key     = strtolower($activityKey);
        if ($extra !== false) {
            $this->activity->extra = $extra;
        }
        $this->activity->save();
        $this->setNotification($activityKey);
    }

    /**
     * Removes the entity attachment from each of its activities
     * and instead uses the 'extra' field with the entities name.
     * Used when an entity is deleted.
     *
     * @return mixed
     */
    public function removeEntity(Entity $entity)
    {
        $activities = $entity->activity;
        foreach ($activities as $activity) {
            $activity->extra       = $entity->name;
            $activity->entity_id   = 0;
            $activity->entity_type = null;
            $activity->save();
        }

        return $activities;
    }

    /**
     * Gets the latest activity.
     *
     * @param int $count
     * @param int $page
     *
     * @return array
     */
    public function latest($count = 20, $page = 0)
    {
        $activityList = $this->permissionService
            ->filterRestrictedEntityRelations($this->activity, 'book_activities', 'entity_id', 'entity_type')
            ->orderBy('created_at', 'desc')->with('user', 'entity')->skip($count * $page)->take($count)->get();

        return $this->filterSimilar($activityList);
    }

    /**
     * Gets the latest activity for an entity, Filtering out similar
     * items to prevent a message activity list.
     *
     * @param Entity $entity
     * @param int    $count
     * @param int    $page
     *
     * @return array
     */
    public function entityActivity($entity, $count = 20, $page = 1)
    {
        if ($entity->isA('book')) {
            $query = $this->activity->where('book_id', '=', $entity->id);
        } else {
            $query = $this->activity->where('entity_type', '=', $entity->getMorphClass())
                ->where('entity_id', '=', $entity->id);
        }

        $activity = $this->permissionService
            ->filterRestrictedEntityRelations($query, 'book_activities', 'entity_id', 'entity_type')
            ->orderBy('created_at', 'desc')
            ->with(['entity', 'user.avatar'])
            ->skip($count * ($page - 1))
            ->take($count)
            ->get();

        return $this->filterSimilar($activity);
    }

    /**
     * Get latest activity for a user, Filtering out similar
     * items.
     *
     * @param int   $count
     * @param int   $page
     * @param mixed $user
     *
     * @return array
     */
    public function userActivity($user, $count = 20, $page = 0)
    {
        $activityList = $this->permissionService
            ->filterRestrictedEntityRelations($this->activity, 'book_activities', 'entity_id', 'entity_type')
            ->orderBy('created_at', 'desc')->where('user_id', '=', $user->id)->skip($count * $page)->take($count)->get();

        return $this->filterSimilar($activityList);
    }

    /**
     * Filters out similar activity.
     *
     * @param Activity[] $activities
     *
     * @return array
     */
    protected function filterSimilar($activities)
    {
        $newActivity  = [];
        $previousItem = false;
        foreach ($activities as $activityItem) {
            if ($previousItem === false) {
                $previousItem  = $activityItem;
                $newActivity[] = $activityItem;

                continue;
            }
            if (! $activityItem->isSimilarTo($previousItem)) {
                $newActivity[] = $activityItem;
            }
            $previousItem = $activityItem;
        }

        return $newActivity;
    }

    /**
     * Flashes a notification message to the session if an appropriate message is available.
     *
     * @param mixed $activityKey
     */
    protected function setNotification($activityKey)
    {
        $notificationTextKey = 'book_activities.' . $activityKey . '_notification';
        if (trans()->has($notificationTextKey)) {
            $message = trans($notificationTextKey);
            Session::flash('success', $message);
        }
    }
}
