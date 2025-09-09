<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Artisan Console Commands - Custom CLI Tools
|--------------------------------------------------------------------------
|
| Custom Artisan commands for maintenance, data processing, and automation.
| These commands can be run via 'php artisan command-name' in terminal.
| 
| Used for: database maintenance, cleanup jobs, data imports, system checks
| 
| @author SlowWebDev
|
*/

/*
|--------------------------------------------------------------------------
| Utility Commands
|--------------------------------------------------------------------------
|
| General-purpose commands for development and maintenance
|
*/

// Display inspirational quote (Laravel default)
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Future Custom Commands
|--------------------------------------------------------------------------
|
| Add custom commands here for:
| - Database cleanup/optimization
| - Image processing/compression  
| - SEO sitemap generation
| - Security log analysis
| - Backup operations
|
*/
