<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class FerrySetting extends Model {
    use HasFactory;
    protected $fillable = ['title', 'has_announcement', 'announcement_text', 'announcement_type'];
}
