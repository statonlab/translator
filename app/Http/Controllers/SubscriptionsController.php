<?php

namespace App\Http\Controllers;

use App\Jobs\AttachUsersToNotification;
use App\NotificationType;
use App\Services\NotificationRegistry;
use Illuminate\Http\Request;

class SubscriptionsController extends Controller
{
    /**
     * Get a list of notification types.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $types = NotificationType::select(['id', 'title'])->public()->withCount([
            'users' => function ($query) use ($user) {
                $query->where('users.id', $user->id);
            },
        ])->orderBy('title', 'asc')->get()->map(function ($type) {
            $data = [
                'id' => $type->id,
                'title' => $type->title,
                'subscribed' => false,
            ];

            if ($type->users_count) {
                $data['subscribed'] = true;
            }

            return $data;
        });

        return $this->success($types);
    }

    /**
     * Subscribe or unsubscribe from a notification type.
     *
     * @param \App\NotificationType $notification_type
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(NotificationType $notification_type, Request $request)
    {
        /** @var \App\User $user */
        $user = $request->user();
        $this->validate($request, [
            'subscribe' => 'nullable|boolean',
        ]);

        if (! empty($request->subscribe)) {
            if ($request->subscribe) {
                $changes = ['attached' => [1]];
                $user->notificationTypes()
                    ->syncWithoutDetaching([$notification_type->id]);
            } else {
                $changes = ['attached' => []];
                $user->notificationTypes()->detach([$notification_type->id]);
            }
        } else {
            $changes = $user->notificationTypes()->toggle([$notification_type->id]);
        }

        return $this->success([
            'subscribed' => count($changes['attached']) > 0,
        ]);
    }

    /**
     * Get a list of available notifications.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function registry()
    {
        $this->authorize('create', NotificationType::class);

        $registry = new NotificationRegistry();

        return $this->success($registry->find());
    }

    /**
     * Create a new notification type.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function create(Request $request)
    {
        $this->authorize('create', NotificationType::class);

        $this->validate($request, [
            'machine_name' => 'required|unique:notification_types,machine_name',
            'title' => 'required|unique:notification_types,title',
            'is_private' => 'required|boolean',
        ]);

        $notification = NotificationType::create([
            'machine_name' => $request->machine_name,
            'title' => $request->title,
            'is_private' => ! ! $request->is_private == '1',
        ]);

        if(!$notification->is_private) {
            $this->dispatch(new AttachUsersToNotification($notification));
        }

        return $this->created($notification);
    }

    /**
     * Update a given notification type.
     *
     * @param \App\NotificationType $notification_type
     * @param \Illuminate\Http\Request $request
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(NotificationType $notification_type, Request $request)
    {
        $this->authorize('update', $notification_type);

        $rule = $request->title !== $notification_type->title ? '|unique:notification_types,title' : '';

        $this->validate($request, [
            'title' => 'required'.$rule,
            'is_private' => 'required|boolean',
        ]);

        $notification_type->fill([
            'title' => $request->title,
            'is_private' => ! ! $request->is_private == '1',
        ])->save();

        return $this->created($notification_type);
    }
}
