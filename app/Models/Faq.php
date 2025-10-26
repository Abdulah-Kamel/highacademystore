<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'answer',
         'display_order',
        'status'
    ];

    protected $casts = [
         'display_order' => 'integer',
        'status' => 'string'
    ];

    /**
     * Scope for active FAQs
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for ordered FAQs
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy( 'display_order')->orderBy('id');
    }

    /**
     * Get status badge attribute
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'active' => '<span class="badge bg-success">نشط</span>',
            'inactive' => '<span class="badge bg-secondary">غير نشط</span>',
            default => '<span class="badge bg-secondary">غير محدد</span>'
        };
    }
}
