<?php

namespace App\Services;

use App\Models\PerformanceKpi;
use App\Models\PerformanceReview;
use Illuminate\Support\Facades\DB;

class PerformanceReviewService
{
    /**
     * Generate sequential review code: PRF-YYYYMM-XXXX
     */
    public function generateReviewCode(): string
    {
        $prefix = 'PRF-' . date('Ym') . '-';
        $last = PerformanceReview::where('review_code', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->value('review_code');

        $next = 1;
        if ($last && preg_match('/-(\d{4})$/', $last, $m)) {
            $next = (int) $m[1] + 1;
        }

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create performance review with KPI indicators
     */
    public function createReview(array $data, array $kpis = []): PerformanceReview
    {
        return DB::transaction(function () use ($data, $kpis) {
            $code = $data['review_code'] ?? $this->generateReviewCode();

            $totalScore = 0.0;
            $kpiRecords = [];

            foreach ($kpis as $k) {
                $weight = (float) ($k['weight'] ?? 25.0);
                $score = (float) ($k['score'] ?? 0.0);
                $weighted = round(($score * $weight) / 100, 2);
                $totalScore += $weighted;

                $kpiRecords[] = [
                    'kpi_name' => $k['kpi_name'],
                    'weight' => $weight,
                    'target_value' => $k['target_value'] ?? null,
                    'actual_value' => $k['actual_value'] ?? null,
                    'score' => $score,
                    'weighted_score' => $weighted,
                    'notes' => $k['notes'] ?? null,
                ];
            }

            // Determine Grade based on score
            $grade = match (true) {
                $totalScore >= 90.0 => 'EXCEEDS',
                $totalScore >= 75.0 => 'MEETS',
                $totalScore >= 60.0 => 'NEEDS_IMPROVEMENT',
                default => 'POOR',
            };

            $review = PerformanceReview::create([
                'review_code' => $code,
                'nik' => $data['nik'],
                'reviewer_nik' => $data['reviewer_nik'] ?? null,
                'period_title' => $data['period_title'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'overall_score' => $totalScore,
                'rating_grade' => $grade,
                'strengths' => $data['strengths'] ?? null,
                'areas_for_improvement' => $data['areas_for_improvement'] ?? null,
                'goals_next_period' => $data['goals_next_period'] ?? null,
                'status' => $data['status'] ?? 'DRAFT',
            ]);

            foreach ($kpiRecords as $kpi) {
                $kpi['performance_review_id'] = $review->id;
                PerformanceKpi::create($kpi);
            }

            return $review;
        });
    }

    /**
     * Submit review for approval
     */
    public function submitReview(int $reviewId): PerformanceReview
    {
        $review = PerformanceReview::findOrFail($reviewId);
        $review->status = 'SUBMITTED';
        $review->save();
        return $review;
    }

    /**
     * Approve review
     */
    public function approveReview(int $reviewId): PerformanceReview
    {
        $review = PerformanceReview::findOrFail($reviewId);
        $review->status = 'APPROVED';
        $review->save();
        return $review;
    }

    /**
     * Get review stats
     */
    public function getStats(): array
    {
        return [
            'total_reviews' => PerformanceReview::count(),
            'approved_reviews' => PerformanceReview::where('status', 'APPROVED')->count(),
            'pending_reviews' => PerformanceReview::whereIn('status', ['DRAFT', 'SUBMITTED'])->count(),
            'avg_score' => round((float) PerformanceReview::avg('overall_score'), 1),
        ];
    }
}
