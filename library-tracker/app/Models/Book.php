<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Book extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'author_id',
        'title',
        'genre',
        'isbn',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function toSearchableArray()
    {
        return [
            'id'=> (string) $this->id,
            'title'=> $this->title,
            'genre'=> $this->genre,
            'isbn'=> $this->isbn,
            'published_at'=> $this->published_at->timestamp,
        ];
    }

}
