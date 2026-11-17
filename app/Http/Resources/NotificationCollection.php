<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class NotificationCollection extends ApiCollection
{
    public $collects = NotificationResource::class;

    /** Jumlah belum dibaca (seluruh halaman, bukan hanya halaman ini) untuk lonceng/badge klien. */
    public function __construct($resource, private int $unreadCount = 0)
    {
        parent::__construct($resource);
    }

    public function paginationInformation(Request $request, array $paginated, array $default): array
    {
        $info = parent::paginationInformation($request, $paginated, $default);
        $info['meta']['unread_count'] = $this->unreadCount;

        return $info;
    }
}
