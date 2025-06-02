<?php

namespace SolutionForest\SimpleContactForm\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactForm extends Model
{
    /** @use HasFactory<\Database\Factories\ContactFormFactory> */
    use HasFactory;

    protected $table = 'simple_contact_form_table';

    protected $fillable = [
        'name',
        'email',
        'subject',
        'content',
        'from',
        'to',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'content' => 'array',
    ];
}
