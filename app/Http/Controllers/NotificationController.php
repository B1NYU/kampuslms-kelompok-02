<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Semua method di sini bekerja LEWAT relasi milik pengguna login
 * ($request->user()->notifications()), tidak pernah lewat Notification::find($id).
 * Akibatnya ID notifikasi milik orang lain tidak pernah ditemukan (404), bukan 403,
 * sehingga tidak ada celah IDOR dan keberadaan ID itu pun tidak bocor.
 */
class NotificationController extends Controller
{
    /** Banyaknya notifikasi terbaru yang ditampilkan di dropdown lonceng. */
    private const TAMPIL = 5;

    /** Banyaknya notifikasi per halaman pada halaman daftar. */
    private const PER_HALAMAN = 15;

    /**
     * Halaman daftar notifikasi (semua / belum dibaca), dengan pagination.
     * Hanya field tampilan yang dikirim ke view, bukan seluruh kolom `data`.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $filter = $request->query('filter') === 'belum' ? 'belum' : 'semua';

        $query = $filter === 'belum' ? $user->unreadNotifications() : $user->notifications();

        $notifications = $query
            ->latest()
            ->paginate(self::PER_HALAMAN)
            ->withQueryString()
            ->through(fn ($n) => [
                'id'       => $n->id,
                'message'  => (string) ($n->data['message'] ?? 'Notifikasi baru'),
                'course'   => (string) ($n->data['course_name'] ?? ''),
                'dibaca'   => $n->read_at !== null,
                'has_url'  => $this->urlAman($n->data['url'] ?? null) !== null,
                'time'     => $n->created_at->locale('id')->diffForHumans(),
                'waktu'    => $n->created_at->locale('id')->translatedFormat('d M Y, H:i'),
            ]);

        return view('notifications.index', [
            'notifications' => $notifications,
            'filter'        => $filter,
            'unreadCount'   => $user->unreadNotifications()->count(),
        ]);
    }

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

    /**
     * Tandai SATU notifikasi sebagai sudah dibaca, lalu arahkan ke tujuannya.
     *
     * Kepemilikan diperiksa oleh query itu sendiri: notifications() hanya berisi baris milik
     * pengguna login, jadi findOrFail() pada ID orang lain berujung 404.
     */
    public function baca(Request $request, string $id): RedirectResponse
    {
        // Kolom id bertipe uuid; string sembarang tidak boleh sampai ke database
        // (di PostgreSQL itu menjadi error 500, bukan 404).
        abort_unless(Str::isUuid($id), 404);

        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $tujuan = $this->urlAman($notification->data['url'] ?? null);

        return $tujuan !== null
            ? redirect($tujuan)
            : redirect()->route('notifikasi.index');
    }

    /** Tandai seluruh notifikasi MILIK PENGGUNA INI sebagai sudah dibaca. */
    public function bacaSemua(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    /**
     * Hanya path internal ("/assignments/5").
     * Ditolak: URL absolut ("https://..."), protocol-relative ("//host"), backslash
     * ("/\host" yang oleh browser diperlakukan seperti "//host"), dan karakter kontrol.
     */
    private function urlAman(?string $url): ?string
    {
        if (! is_string($url)) {
            return null;
        }

        return preg_match('#^/(?![/\\\\])[^\x00-\x1F\x7F\\\\]*$#', $url) === 1 ? $url : null;
    }
}
