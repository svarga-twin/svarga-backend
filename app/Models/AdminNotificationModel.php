<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminNotificationModel extends Model
{
    use HasFactory;

    protected $table = 'admin_notifications';

    protected $fillable = ['category', 'title', 'description', 'is_read'];

    protected $casts = ['is_read' => 'boolean'];
}
