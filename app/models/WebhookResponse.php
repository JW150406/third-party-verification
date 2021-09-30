<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class WebhookResponse extends Model
{
	protected $table = 'webhook_response';

    protected $guarded = ['id'];
}
