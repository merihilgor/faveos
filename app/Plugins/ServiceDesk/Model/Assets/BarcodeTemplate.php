<?php

namespace App\Plugins\ServiceDesk\Model\Assets;

use Illuminate\Database\Eloquent\Model;

class BarcodeTemplate extends Model
{
    protected $table = 'sd_barcode_templates';
    protected $fillable = [
        'height',
        'width',
        'labels_per_row',
        'space_between_labels',
        'logo_text',
        'logo_image',
        'display_logo_confirmed'
    ];
}