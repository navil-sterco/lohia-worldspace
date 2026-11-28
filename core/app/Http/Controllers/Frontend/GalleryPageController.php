<?php

namespace App\Http\Controllers\Frontend;

use App\Models\ModuleEntry;
use App\Http\Controllers\Controller;

class GalleryPageController extends Controller
{
    public function index()
    {
        $gallery = ModuleEntry::forModule(8, 'display_order', 'asc')
            ->paginate(10)
            ->through(function ($entry) {
                return $this->normalizeGalleryItem($entry->toCleanData());
            });

        return view('dynamic.gallery', [
            'gallery' => $gallery,
        ]);
    }

    public function detail(string $slug)
    {
        $galleryEntry = ModuleEntry::forModule(8, 'display_order', 'asc')
            ->where('slug', $slug)
            ->firstOrFail();

        $galleryDetail = $this->normalizeGalleryItem($galleryEntry->toCleanData());

        return view('dynamic.gallery-detail', [
            'galleryDetail' => $galleryDetail,
        ]);
    }

    protected function normalizeGalleryItem(array $item): array
    {
        $item['image'] = $this->normalizeImageUrl($item['image'] ?? '');

        $detailImages = $item['mapping_items']['detail_images'] ?? [];
        $item['detail_images'] = [];

        foreach ($detailImages as $detailImage) {
            $image = is_array($detailImage)
                ? ($detailImage['images'] ?? $detailImage['image'] ?? '')
                : $detailImage;
            $image = $this->normalizeImageUrl(is_string($image) ? $image : '');

            if ($image !== '') {
                $item['detail_images'][] = $image;
            }
        }

        return $item;
    }

    protected function normalizeImageUrl(string $value): string
    {
        $value = trim($value);

        if (preg_match('/\]\(([^)\s]+)\)/', $value, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
        }

        if (preg_match('/^https?:\/\/[^\s]+$/', $value)) {
            return html_entity_decode($value, ENT_QUOTES, 'UTF-8');
        }

        return $value;
    }
}
