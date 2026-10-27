<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /** Banyaknya notifikasi terbaru yang ditampilkan di dropdown lonceng. */
    private const TAMPIL = 5;

    /**
     * Ringkasan untuk lonceng (di-polling browser).
     *
     * Tanpa parameter ID sama sekali: yang dikembalikan selalu notifikasi milik pengguna yang
     * sedang login (relasi notifications() terikat ke notifiable-nya), sehingga tidak ada celah
     * IDOR lewat ID notifikasi. Hanya field tampilan yang dikirim, bukan seluruh kolom `data`.
     */
    public function ringkas(Request $request): JsonResponse
    {
        $user = $request->user();

        $items = $user->unreadNotifications()
            ->latest()
            ->limit(self::TAMPIL)
            ->get()
            ->map(fn ($n) => [
                'id'      => $n->id,
                'message' => (string) ($n->data['message'] ?? 'Notifikasi baru'),
                'url'     => $this->urlAman($n->data['url'] ?? null),
                'time'    => $n->created_at->locale('id')->diffForHumans(),
            ])
            ->values();

        return response()
            ->json([
                'unread_count' => $user->unreadNotifications()->count(),
                'items'        => $items,
            ])
            ->header('Cache-Control', 'no-store, private');
    }

    /** Hanya path internal ("/assignments/5"); tolak URL absolut atau protocol-relative ("//host"). */
    private function urlAman(?string $url): ?string
    {
        return is_string($url) && str_starts_with($url, '/') && ! str_starts_with($url, '//') ? $url : null;
    }
}
