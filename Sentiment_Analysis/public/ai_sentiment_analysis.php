<?php

class SentimentLexicon {
    private $lexicon;
    private $negations;
    private $modifiers;

    public function __construct() {
        $this->lexicon = [
            'good' => 1, 'great' => 2, 'excellent' => 2, 'amazing' => 2, 'wonderful' => 2,
            'bad' => -1, 'terrible' => -2, 'awful' => -2, 'horrible' => -2, 'disappointing' => -1,
            'love' => 2, 'hate' => -2, 'like' => 1, 'dislike' => -1,
            'best' => 2, 'worst' => -2, 'awesome' => 2, 'poor' => -1,
            'fantastic' => 2, 'mediocre' => -1, 'outstanding' => 2, 'subpar' => -1,
            'superb' => 2, 'inferior' => -1, 'pleasant' => 1, 'unpleasant' => -1,
            'satisfactory' => 1, 'unsatisfactory' => -1, 'delightful' => 2, 'dreadful' => -2,
            'happy' => 1, 'sad' => -1, 'joyful' => 2, 'miserable' => -2,
            'positive' => 1, 'negative' => -1, 'favorable' => 1, 'unfavorable' => -1,
            'recommend' => 1, 'avoid' => -1, 'impressed' => 1, 'disappointed' => -1,
            'perfect' => 2, 'imperfect' => -1, 'ideal' => 1, 'flawed' => -1,
            'reliable' => 1, 'unreliable' => -1, 'efficient' => 1, 'inefficient' => -1,
            'helpful' => 1, 'unhelpful' => -1, 'useful' => 1, 'useless' => -1,
            'fast' => 1, 'slow' => -1, 'quick' => 1, 'sluggish' => -1,
            'easy' => 1, 'difficult' => -1, 'simple' => 1, 'complicated' => -1,
            'clear' => 1, 'confusing' => -1, 'convenient' => 1, 'inconvenient' => -1,
            'smooth' => 1, 'rough' => -1, 'comfortable' => 1, 'uncomfortable' => -1,
            'friendly' => 1, 'unfriendly' => -1, 'polite' => 1, 'rude' => -1,
            'professional' => 1, 'unprofessional' => -1, 'responsive' => 1, 'unresponsive' => -1,
            'innovative' => 1, 'outdated' => -1, 'fresh' => 1, 'stale' => -1,
            'clean' => 1, 'dirty' => -1, 'safe' => 1, 'dangerous' => -1,
            'reliable' => 1, 'unreliable' => -1, 'trustworthy' => 1, 'untrustworthy' => -1,
            'valuable' => 1, 'worthless' => -1, 'affordable' => 1, 'overpriced' => -1,
            'fair' => 1, 'unfair' => -1, 'honest' => 1, 'dishonest' => -1,
            'impressive' => 1, 'unimpressive' => -1, 'organized' => 1, 'disorganized' => -1
        ];

        $this->negations = ['not', 'no', 'never', 'neither', 'nor', 'none', 'nobody', 'nothing', 'nowhere', 'hardly', 'scarcely', 'barely'];
        
        $this->modifiers = [
            'very' => 1.5, 'extremely' => 2, 'slightly' => 0.5, 'somewhat' => 0.75,
            'pretty' => 1.2, 'quite' => 1.3, 'really' => 1.4, 'so' => 1.3,
            'too' => 1.2, 'absolutely' => 1.8, 'completely' => 1.7, 'totally' => 1.6,
            'utterly' => 1.7, 'rather' => 0.8, 'fairly' => 0.7, 'kind of' => 0.6,
            'sort of' => 0.6, 'a bit' => 0.5, 'a little' => 0.5, 'incredibly' => 1.8
        ];
    }

    public function getScore($word) {
        return isset($this->lexicon[$word]) ? $this->lexicon[$word] : 0;
    }

    public function isNegation($word) {
        return in_array($word, $this->negations);
    }

    public function getModifier($word) {
        return isset($this->modifiers[$word]) ? $this->modifiers[$word] : 1;
    }
}

class SentimentAnalyzer {
    private $lexicon;
    private $threshold;

    public function __construct() {
        $this->lexicon = new SentimentLexicon();
        $this->threshold = 0.1; // Adjust this value to fine-tune the neutral range
    }

    public function analyzeSentiment($text) {
        $words = $this->tokenize($text);
        $score = 0;
        $wordCount = 0;
        $negation = false;
        $modifier = 1;

        for ($i = 0; $i < count($words); $i++) {
            $word = $words[$i];
            
            if ($this->lexicon->isNegation($word)) {
                $negation = true;
                continue;
            }

            $currentModifier = $this->lexicon->getModifier($word);
            if ($currentModifier != 1) {
                $modifier = $currentModifier;
                continue;
            }

            $wordScore = $this->lexicon->getScore($word);
            if ($wordScore != 0) {
                if ($negation) {
                    $wordScore = -$wordScore;
                    $negation = false;
                }
                $score += $wordScore * $modifier;
                $wordCount++;
                $modifier = 1;
            }
        }

        if ($wordCount > 0) {
            $averageScore = $score / $wordCount;
        } else {
            $averageScore = 0;
        }

        if ($averageScore > $this->threshold) {
            return 'positive';
        } elseif ($averageScore < -$this->threshold) {
            return 'negative';
        } else {
            return 'neutral';
        }
    }

    private function tokenize($text) {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z\s]/', '', $text);
        return array_filter(explode(' ', $text), function($word) { return strlen($word) > 1; });
    }
}

function analyze_sentiment($text) {
    static $analyzer = null;

    if ($analyzer === null) {
        $analyzer = new SentimentAnalyzer();
    }

    $sentiment = $analyzer->analyzeSentiment($text);
    
    error_log("Analyzed text: " . $text);
    error_log("Classified sentiment: " . $sentiment);

    return $sentiment;
}
?>