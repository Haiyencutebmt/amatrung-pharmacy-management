<?php

namespace App\Services\Chatbot;

use App\Models\Article;
use App\Models\MedicinalHerb;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PublicChatbotSearchService
{
    public function __construct(private VietnameseQueryNormalizerService $normalizer)
    {
    }

    public function search(array $normalizedQuery, array $extraTerms = []): array
    {
        $terms = $this->buildSearchTerms($normalizedQuery, $extraTerms);

        $articles = $this->searchArticles($terms);
        $herbs = $this->searchMedicinalHerbs($terms);
        $dictionaryHerbs = $this->searchDictionaryEntries($terms);

        $herbs = $this->mergeHerbResults($dictionaryHerbs, $herbs);

        return [
            'terms' => $terms,
            'articles' => array_slice($articles, 0, 3),
            'herbs' => array_slice($herbs, 0, 5),
            'sources' => $this->buildSources(array_slice($articles, 0, 3), array_slice($herbs, 0, 5)),
            'context' => $this->buildContext(array_slice($articles, 0, 3), array_slice($herbs, 0, 5)),
            'has_results' => !empty($articles) || !empty($herbs),
        ];
    }

    private function buildSearchTerms(array $normalizedQuery, array $extraTerms): array
    {
        $terms = array_merge(
            $normalizedQuery['canonical_terms'] ?? [],
            $normalizedQuery['keywords'] ?? [],
            $normalizedQuery['variants'] ?? [],
            $extraTerms
        );

        $terms = array_map(fn($term) => trim((string) $term), $terms);
        $terms = array_filter($terms, fn($term) => mb_strlen($term, 'UTF-8') >= 3);

        return array_values(array_unique(array_slice($terms, 0, 36)));
    }

    private function searchArticles(array $terms): array
    {
        if (!Schema::hasTable('articles')) {
            return [];
        }

        $columns = ['id', 'title', 'slug', 'content', 'is_published', 'published_at', 'created_at'];
        foreach (['summary', 'category', 'tags'] as $column) {
            if (Schema::hasColumn('articles', $column)) {
                $columns[] = $column;
            }
        }

        $articles = Article::published()
            ->select(array_values(array_unique($columns)))
            ->latest('published_at')
            ->latest('id')
            ->limit(250)
            ->get();

        $results = [];
        foreach ($articles as $article) {
            $title = (string) $article->title;
            $summary = (string) ($article->summary ?? $article->excerpt ?? '');
            $content = $this->plainText((string) $article->content);
            $tags = is_array($article->tags ?? null) ? implode(' ', $article->tags) : '';

            $score = $this->score($title, [$summary, $tags], [$content], $terms);
            if ($score < 18) {
                continue;
            }

            $results[] = [
                'type' => 'article',
                'score' => $score,
                'title' => $title,
                'url' => route('articles.show', $article->slug, false),
                'summary' => $summary ?: Str::limit($content, 220),
                'content' => Str::limit($content, 700),
            ];
        }

        return $this->sortResults($results);
    }

    private function searchMedicinalHerbs(array $terms): array
    {
        if (!Schema::hasTable('medicinal_herbs')) {
            return [];
        }

        $columns = ['id', 'name'];
        foreach ([
            'category',
            'usage_type',
            'description',
            'warning_note',
            'status',
            'scientific_name',
            'other_names',
            'uses',
            'effects',
            'notes',
        ] as $column) {
            if (Schema::hasColumn('medicinal_herbs', $column)) {
                $columns[] = $column;
            }
        }

        $query = MedicinalHerb::query()->select(array_values(array_unique($columns)));
        if (Schema::hasColumn('medicinal_herbs', 'status')) {
            $query->where(function ($q) {
                $q->whereNull('status')->orWhere('status', 'active');
            });
        }

        $herbs = $query->orderBy('name')->limit(500)->get();
        $results = [];

        foreach ($herbs as $herb) {
            $title = (string) $herb->name;
            $important = [
                (string) ($herb->category ?? ''),
                (string) ($herb->usage_type ?? ''),
                (string) ($herb->scientific_name ?? ''),
                (string) ($herb->other_names ?? ''),
            ];
            $body = [
                (string) ($herb->description ?? ''),
                (string) ($herb->uses ?? ''),
                (string) ($herb->effects ?? ''),
                (string) ($herb->notes ?? ''),
                (string) ($herb->warning_note ?? ''),
            ];

            $score = $this->score($title, $important, $body, $terms);
            if ($score < 18) {
                continue;
            }

            $results[] = [
                'type' => 'herb',
                'score' => $score,
                'title' => $title,
                'url' => route('herb-dictionary.index', ['q' => $title], false),
                'scientific_name' => (string) ($herb->scientific_name ?? ''),
                'other_names' => (string) ($herb->other_names ?? ''),
                'description' => Str::limit($this->plainText(implode(' ', $body)), 760),
                'warning' => Str::limit($this->plainText((string) ($herb->warning_note ?? '')), 220),
                'source_origin' => 'medicinal_herbs',
            ];
        }

        return $this->sortResults($results);
    }

    private function searchDictionaryEntries(array $terms): array
    {
        if (!class_exists(\App\Models\HerbDictionaryEntry::class) || !Schema::hasTable('herb_dictionary_entries')) {
            return [];
        }

        $entries = \App\Models\HerbDictionaryEntry::published()
            ->select([
                'id',
                'name',
                'slug',
                'scientific_name',
                'other_names',
                'family',
                'plant_part',
                'properties',
                'basic_info',
                'effects',
                'usage_notes',
                'safety_warning',
            ])
            ->orderBy('name')
            ->limit(500)
            ->get();

        $results = [];
        foreach ($entries as $entry) {
            $title = (string) $entry->name;
            $important = [
                (string) $entry->scientific_name,
                (string) $entry->other_names,
                (string) $entry->family,
                (string) $entry->plant_part,
                (string) $entry->properties,
            ];
            $body = [
                (string) $entry->basic_info,
                (string) $entry->effects,
                (string) $entry->usage_notes,
                (string) $entry->safety_warning,
            ];

            $score = $this->score($title, $important, $body, $terms);
            if ($score < 18) {
                continue;
            }

            $results[] = [
                'type' => 'herb',
                'score' => $score + 2,
                'title' => $title,
                'url' => route('herb-dictionary.show', $entry->slug, false),
                'scientific_name' => (string) $entry->scientific_name,
                'other_names' => (string) $entry->other_names,
                'description' => Str::limit($this->plainText(($entry->basic_info ?: '') . ' ' . ($entry->effects ?: '') . ' ' . ($entry->usage_notes ?: '')), 760),
                'warning' => Str::limit($this->plainText((string) $entry->safety_warning), 220),
                'source_origin' => 'herb_dictionary_entries',
            ];
        }

        return $this->sortResults($results);
    }

    private function score(string $title, array $importantFields, array $bodyFields, array $terms): int
    {
        $score = 0;
        $titleAscii = $this->normalizer->ascii($title);
        $importantAscii = $this->normalizer->ascii(implode(' ', $importantFields));
        $bodyAscii = $this->normalizer->ascii(implode(' ', $bodyFields));

        foreach ($terms as $term) {
            $termAscii = $this->normalizer->ascii($term);
            if (mb_strlen($termAscii, 'UTF-8') < 3) {
                continue;
            }

            $termHasSpace = str_contains($termAscii, ' ');

            if ($titleAscii === $termAscii) {
                $score += 150;
                continue;
            }

            if ($termHasSpace && str_contains($titleAscii, $termAscii)) {
                $score += 95;
                continue;
            }

            if (str_contains($titleAscii, $termAscii)) {
                $score += 45;
            }

            if ($termHasSpace && str_contains($importantAscii, $termAscii)) {
                $score += 32;
            } elseif (str_contains($importantAscii, $termAscii)) {
                $score += 18;
            }

            if ($termHasSpace && str_contains($bodyAscii, $termAscii)) {
                $score += 18;
            } elseif (str_contains($bodyAscii, $termAscii)) {
                $score += 7;
            }
        }

        return $score;
    }

    private function sortResults(array $results): array
    {
        usort($results, function ($a, $b) {
            return ($b['score'] <=> $a['score']) ?: strcmp($a['title'], $b['title']);
        });

        return $results;
    }

    private function mergeHerbResults(array $dictionaryHerbs, array $medicinalHerbs): array
    {
        $merged = [];

        foreach (array_merge($dictionaryHerbs, $medicinalHerbs) as $result) {
            $key = $this->normalizer->ascii($result['title']);
            if (!isset($merged[$key]) || $result['score'] > $merged[$key]['score']) {
                $merged[$key] = $result;
            }
        }

        return $this->sortResults(array_values($merged));
    }

    private function buildSources(array $articles, array $herbs): array
    {
        $sources = [];

        foreach ($articles as $article) {
            $sources[] = [
                'type' => 'article',
                'title' => $article['title'],
                'url' => $article['url'],
            ];
        }

        foreach ($herbs as $herb) {
            $sources[] = [
                'type' => 'herb',
                'title' => $herb['title'],
                'url' => $herb['url'],
            ];
        }

        return array_values(array_unique($sources, SORT_REGULAR));
    }

    private function buildContext(array $articles, array $herbs): string
    {
        $sections = [];

        if (!empty($herbs)) {
            $lines = ["[DƯỢC LIỆU / TỪ ĐIỂN AMATRUNG]"];
            foreach ($herbs as $herb) {
                $lines[] = "- {$herb['title']}";
                if (!empty($herb['scientific_name'])) {
                    $lines[] = "  Tên khoa học: {$herb['scientific_name']}";
                }
                if (!empty($herb['other_names'])) {
                    $lines[] = "  Tên gọi khác: {$herb['other_names']}";
                }
                if (!empty($herb['description'])) {
                    $lines[] = "  Thông tin: {$herb['description']}";
                }
                if (!empty($herb['warning'])) {
                    $lines[] = "  Lưu ý an toàn: {$herb['warning']}";
                }
            }
            $sections[] = implode("\n", $lines);
        }

        if (!empty($articles)) {
            $lines = ["[BÀI VIẾT AMATRUNG]"];
            foreach ($articles as $article) {
                $lines[] = "- {$article['title']}";
                if (!empty($article['summary'])) {
                    $lines[] = "  Tóm tắt: {$article['summary']}";
                }
                if (!empty($article['content'])) {
                    $lines[] = "  Nội dung liên quan: {$article['content']}";
                }
            }
            $sections[] = implode("\n", $lines);
        }

        if (empty($sections)) {
            return "Không tìm thấy dữ liệu liên quan trong bài viết hoặc từ điển dược liệu của AmaTrung.";
        }

        return implode("\n\n", $sections);
    }

    private function plainText(string $text): string
    {
        $text = html_entity_decode(strip_tags($text), ENT_QUOTES, 'UTF-8');
        return trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
    }
}
