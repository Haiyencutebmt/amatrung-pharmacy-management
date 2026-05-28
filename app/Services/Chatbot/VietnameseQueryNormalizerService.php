<?php

namespace App\Services\Chatbot;

use Illuminate\Support\Str;

class VietnameseQueryNormalizerService
{
    private array $phraseCorrections = [
        'duoc liru' => 'dược liệu',
        'duoc lieu' => 'dược liệu',
        'kim tien thao' => 'kim tiền thảo',
        'kim tien thao co' => 'kim tiền thảo có',
        'rau meo' => 'râu mèo',
        'tia to' => 'tía tô',
        'huong nhu' => 'hương nhu',
        'da day' => 'đau dạ dày',
        'dau bao tu' => 'đau dạ dày',
        'bao tu' => 'dạ dày',
        'soa bop' => 'xoa bóp',
        'xoa bop' => 'xoa bóp',
        'xuong khop' => 'xương khớp',
        'dau khop' => 'xương khớp',
        'yhct' => 'y học cổ truyền',
        'tac dung j' => 'tác dụng gì',
        'cong dung gi' => 'tác dụng gì',
        'cong dung j' => 'tác dụng gì',
        'thuoc gi' => 'thuốc gì',
        'thuoc j' => 'thuốc gì',
        'ke don' => 'kê đơn',
    ];

    private array $stopWords = [
        'la', 'gi', 'j', 'cho', 'hoi', 'cach', 'nhu', 'the', 'nao', 'khi',
        'bi', 'nen', 'khong', 've', 'toi', 'minh', 'em', 'anh', 'chi', 'co',
        'va', 'hay', 'duoc', 'khac', 'nhau', 'sao', 'a', 'oi', 'nha',
        'cau', 'du', 'lieu', 'tren', 'website', 'ama', 'trung', 'amatrung',
        'thong', 'tin', 'nay', 'kia', 'do',
    ];

    private array $emergencyPhrases = [
        'dau nguc',
        'kho tho',
        'ngat',
        'ngat xiu',
        'chay mau nhieu',
        'co giat',
        'liet nua nguoi',
        'meo mieng',
        'noi kho',
        'dau dau du doi',
        'non ra mau',
        'di ngoai ra mau',
    ];

    private array $treatmentRequestPhrases = [
        'uong thuoc gi',
        'dung thuoc nao',
        'nen dung thuoc',
        'ke don',
        'ke cho toi',
        'thang thuoc',
        'bai thuoc nao',
        'bao nhieu gam',
        'lieu dung',
        'lieu luong',
        'tu dieu tri',
        'thuoc tri',
    ];

    private array $promptInjectionPhrases = [
        'bo qua quy tac',
        'bo qua huong dan',
        'ignore previous',
        'ignore all',
        'jailbreak',
        'khong can an toan',
        'khong can bac si',
        'ke don cho toi',
    ];

    public function normalize(string $question): array
    {
        $original = trim($question);
        $cleaned = $this->clean($original);
        $ascii = $this->ascii($cleaned);

        $canonicalTerms = [];
        $correctedAscii = $ascii;

        foreach ($this->phraseCorrections as $wrong => $canonical) {
            if (str_contains($ascii, $wrong)) {
                $canonicalTerms[] = $canonical;
                $correctedAscii = str_replace($wrong, $this->ascii($canonical), $correctedAscii);
            }
        }

        $canonicalTerms = array_values(array_unique($canonicalTerms));
        $normalized = trim($cleaned . ' ' . implode(' ', $canonicalTerms));

        $variants = array_values(array_unique(array_filter([
            $original,
            $cleaned,
            $normalized,
            $ascii,
            $correctedAscii,
            implode(' ', $canonicalTerms),
        ])));

        return [
            'original' => $original,
            'cleaned' => $cleaned,
            'normalized' => $normalized ?: $cleaned,
            'ascii' => $ascii,
            'corrected_ascii' => $correctedAscii,
            'canonical_terms' => $canonicalTerms,
            'keywords' => $this->extractKeywords($normalized . ' ' . $correctedAscii, $canonicalTerms),
            'variants' => $variants,
            'is_emergency' => $this->containsAny($correctedAscii . ' ' . $ascii, $this->emergencyPhrases),
            'is_treatment_request' => $this->containsAny($correctedAscii . ' ' . $ascii, $this->treatmentRequestPhrases),
            'is_prompt_injection' => $this->containsAny($correctedAscii . ' ' . $ascii, $this->promptInjectionPhrases),
        ];
    }

    public function clean(string $text): string
    {
        $text = mb_strtolower(trim($text), 'UTF-8');
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text) ?? $text;
        return trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
    }

    public function ascii(string $text): string
    {
        $text = Str::ascii($this->clean($text));
        return trim(preg_replace('/\s+/', ' ', mb_strtolower($text, 'UTF-8')) ?? $text);
    }

    private function extractKeywords(string $text, array $canonicalTerms): array
    {
        $keywords = $canonicalTerms;
        $asciiText = $this->ascii($text);
        $words = preg_split('/\s+/', $asciiText) ?: [];

        foreach ($words as $word) {
            if (mb_strlen($word, 'UTF-8') < 3) {
                continue;
            }

            if (in_array($word, $this->stopWords, true)) {
                continue;
            }

            $keywords[] = $word;
        }

        return array_values(array_unique(array_slice($keywords, 0, 24)));
    }

    private function containsAny(string $asciiHaystack, array $phrases): bool
    {
        foreach ($phrases as $phrase) {
            if (str_contains($asciiHaystack, $phrase)) {
                return true;
            }
        }

        return false;
    }
}
