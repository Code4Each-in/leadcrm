<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\HeaderUtils;

class KnowledgeBaseController extends Controller
{
    private const CATEGORIES = [
        'document' => ['label' => 'Documents', 'icon' => 'mdi-file-document-outline'],
        'link'     => ['label' => 'Links', 'icon' => 'mdi-open-in-new'],
        'video'    => ['label' => 'Videos', 'icon' => 'mdi-play-circle-outline'],
        'policy'   => ['label' => 'Policies', 'icon' => 'mdi-shield-check-outline'],
        'faq'      => ['label' => 'FAQs', 'icon' => 'mdi-help-circle-outline'],
    ];

    /**
     * The section a user may see: Account Executives get the AE
     * Knowledge Base only, every other role the General one only.
     */
    public static function sectionFor(User $user): string
    {
        return $user->isAe() ? 'ae' : 'general';
    }

    /**
     * Section contents come from config/knowledge_base.php plus any
     * file sitting in the section's folder that the config doesn't
     * list - see that file for how to add/remove documents.
     */
    public function index()
    {
        $section = $this->sectionForCurrentUser();
        $items = $this->itemsFor($section);

        // Only offer filter chips for categories this section actually has.
        $categories = array_intersect_key(self::CATEGORIES, array_flip(array_column($items, 'type')));

        return view('knowledge-base.index', compact('section', 'categories', 'items'));
    }

    /**
     * Viewer page for one document, opened in a new tab from the card.
     * PDFs are embedded and .docx files rendered in the page, so the
     * browser never falls back to downloading the file.
     */
    public function view(string $file)
    {
        $section = $this->sectionForCurrentUser();

        abort_unless(in_array($file, $this->filesIn($section), true), 404);

        $item = collect($this->itemsFor($section))->firstWhere('file', $file);

        return view('knowledge-base.view', [
            'section' => $section,
            'item' => $item,
            'extension' => strtolower(pathinfo($file, PATHINFO_EXTENSION)),
            'fileUrl' => route('knowledge-base.document', ['file' => $file]),
        ]);
    }

    /**
     * Streams a document from the signed-in user's own section folder,
     * marked inline so it is displayed rather than saved. The folder is
     * derived from the user's role, never the request, so an AE can't
     * reach General documents (or vice versa) by URL.
     */
    public function document(string $file)
    {
        $section = $this->sectionForCurrentUser();

        abort_unless(in_array($file, $this->filesIn($section), true), 404);

        return response()->file($this->folderPath($section) . '/' . $file, [
            'Content-Disposition' => HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_INLINE, $file, str_replace('%', '_', Str::ascii($file))),
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    private function sectionForCurrentUser(): array
    {
        return config('knowledge_base.sections.' . self::sectionFor(Auth::user()));
    }

    private function itemsFor(array $section): array
    {
        $items = [];
        $listed = [];

        foreach ($section['items'] as $item) {
            if (isset($item['file'])) {
                $listed[] = $item['file'];

                // Listed but not (yet) copied into the folder - skip
                // rather than render a link that would 404.
                if (!in_array($item['file'], $this->filesIn($section), true)) {
                    continue;
                }

                $item = $this->documentItem($item['file'], $item);
            } elseif (!$this->isRealLink($item['url'] ?? null)) {
                // No file and no usable link - nothing to open.
                continue;
            }

            $items[] = $item;
        }

        foreach (array_diff($this->filesIn($section), $listed) as $file) {
            $items[] = $this->documentItem($file);
        }

        return $items;
    }

    private function isRealLink(?string $url): bool
    {
        return $url !== null && filter_var($url, FILTER_VALIDATE_URL) && preg_match('#^https?://#i', $url);
    }

    private function documentItem(string $file, array $overrides = []): array
    {
        return [
            'type'        => $overrides['type'] ?? 'document',
            'title'       => $overrides['title'] ?? Str::of(pathinfo($file, PATHINFO_FILENAME))->replace(['_', '-'], ' ')->squish()->toString(),
            'description' => $overrides['description'] ?? '',
            'meta'        => $overrides['meta'] ?? strtoupper(pathinfo($file, PATHINFO_EXTENSION)),
            'file'        => $file,
            'url'         => route('knowledge-base.view', ['file' => $file]),
        ];
    }

    /**
     * Visible files directly inside the section's folder, sorted by name.
     */
    private function filesIn(array $section): array
    {
        $folder = $this->folderPath($section);

        if (!is_dir($folder)) {
            return [];
        }

        $files = array_values(array_filter(
            scandir($folder),
            fn ($name) => !str_starts_with($name, '.') && is_file($folder . '/' . $name)
        ));

        natcasesort($files);

        return array_values($files);
    }

    private function folderPath(array $section): string
    {
        return resource_path('knowledge-base/' . $section['folder']);
    }
}
