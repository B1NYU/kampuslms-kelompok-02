<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationCollection;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Semua method bekerja LEWAT relasi milik pengguna login ($request->user()->notifications()),
 * tidak pernah lewat DatabaseNotification::find($id). ID notifikasi milik orang lain
 * karenanya berujung 404 (bukan 403): tidak ada IDOR dan keberadaan ID itu tidak bocor.
 */
class NotificationController extends Controller
{
    /** GET /api/v1/notifications?filter=semua|belum&page=&per_page= */
    public function index(Request $request): NotificationCollection
    {
        $request->validate([
            'filter'   => ['nullable', Rule::in(['semua', 'belum'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $user    = $request->user();
        $perPage = max(1, min(50, $request->integer('per_page', 15)));

        $query = $request->query('filter') === 'belum'
            ? $user->unreadNotifications()
            : $user->notifications();

        $notifications = $query->latest()->latest('id')->paginate($perPage);

        return new NotificationCollection($notifications, $user->unreadNotifications()->count());
    }

    /** POST /api/v1/notifications/{id}/read → 200 (idempoten: membaca ulang tetap 200) */
    public function read(Request $request, string $id): NotificationResource
    {
        // Kolom id bertipe uuid; string sembarang tidak boleh sampai ke database.
        abort_unless(Str::isUuid($id), 404);

        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return new NotificationResource($notification->refresh());
    }
}
